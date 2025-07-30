<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\AccountingEntry;
use App\Models\Invoice;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CashBoxController extends Controller
{
    /**
     * عرض صفحة الصندوق
     */
    public function index()
    {
        // التحقق من تسجيل دخول المستخدم
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();
        Log::info('CashBox index accessed by user:', ['user_id' => $userId]);

        $cashBox = CashBox::where('user_id', $userId)->first();
        if (!$cashBox) {
            Log::info('Creating new cash box for user', ['user_id' => $userId]);
            $cashBox = CashBox::create([
                'user_id' => $userId,
                'name' => 'الصندوق الرئيسي',
                'initial_balance' => 0,
                'current_balance' => 0
            ]);
        }

        // حساب الإحصائيات الأولية للمستخدم الحالي
        $totalRevenue = Invoice::where('user_id', $userId)
            ->where('status', 'مقبوضة')
            ->sum('total_after_discount');

        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');

        return view('dashboard.cash-box', [
            'cashBox' => $cashBox,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses
        ]);
    }

    /**
     * الحصول على بيانات الصندوق بالشكل الجديد
     */
    public function getData(Request $request)
    {
        // التحقق من تسجيل دخول المستخدم
        if (!Auth::check()) {
            Log::error('User not authenticated for cash box data request');
            return response()->json(['error' => 'يجب تسجيل الدخول أولاً'], 401);
        }

        $userId = Auth::id();
        Log::info('CashBox getData called by user:', ['user_id' => $userId]);

        $cashBox = CashBox::where('user_id', $userId)->first();

        // تحديث الرصيد
        $cashBox->reconcileBalance();

        // إعداد فلاتر البحث
        $startDate = $request->input('date_from');
        $endDate = $request->input('date_to');
        $searchTerm = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');

        // جمع الفواتير والمصاريف
        $transactions = collect();

        // إضافة الفواتير للمستخدم الحالي
        Log::info('Fetching invoices for user:', ['user_id' => $userId]);

        // إذا كان النوع "مصروف"، لا نضيف الفواتير
        if ($type !== 'مصروف') {
            $invoicesQuery = Invoice::with(['client', 'accountingEntry'])
                ->where('user_id', $userId) // فلتر حسب المستخدم الحالي
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('date', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('date', '<=', $endDate);
                })
                ->when($type, function($q) use ($type) {
                    return $q->where('type', $type);
                })
                ->when($status, function($q) use ($status) {
                    return $q->where('status', $status);
                })
                ->when($searchTerm, function($q) use ($searchTerm) {
                    return $q->where(function($sq) use ($searchTerm) {
                        $sq->where('invoice_number', 'like', "%{$searchTerm}%")
                            ->orWhereHas('client', function($cq) use ($searchTerm) {
                                $cq->where('name', 'like', "%{$searchTerm}%");
                            });
                    });
                });

            $invoices = $invoicesQuery->get();

            Log::info('عدد الفواتير الموجودة:', [
                'count' => $invoices->count(),
                'user_id' => $userId,
                'query_sql' => $invoicesQuery->toSql(),
                'query_bindings' => $invoicesQuery->getBindings()
            ]);

            // إضافة الفواتير للمعاملات
            foreach ($invoices as $invoice) {
                $transactions->push([
                    'id' => 'INV-' . $invoice->id,
                    'name' => 'فاتورة رقم: ' . $invoice->invoice_number . ' - ' . ($invoice->client->name ?? 'عميل غير محدد'),
                    'type' => $invoice->type,
                    'amount' => $invoice->total,
                    'amount_after_discount' => $invoice->total_after_discount,
                    'debit' => $this->getDebitAccount($invoice),
                    'credit' => $this->getCreditAccount($invoice),
                    'status' => $invoice->status,
                    'date' => $invoice->date,
                    'source_type' => 'invoice',
                    'source_id' => $invoice->id,
                    'is_received' => $invoice->status === 'مقبوضة'
                ]);
            }
        } else {
            Log::info('تم تخطي الفواتير لأن النوع المحدد هو "مصروف"');
        }



        // إضافة المصاريف للمستخدم الحالي
        // إذا كان النوع "مصروف" أو لم يتم تحديد نوع، أضف المصاريف
        if ($type === 'مصروف' || !$type) {
            $expensesQuery = Expense::with(['accountingEntry'])
                ->where('user_id', $userId) // فلتر حسب المستخدم الحالي
                ->when($startDate, function($q) use ($startDate) {
                    return $q->whereDate('expense_date', '>=', $startDate);
                })
                ->when($endDate, function($q) use ($endDate) {
                    return $q->whereDate('expense_date', '<=', $endDate);
                })
                ->when($searchTerm, function($q) use ($searchTerm) {
                    return $q->where('name', 'like', "%{$searchTerm}%");
                });

            $expenses = $expensesQuery->get();

            Log::info('عدد المصاريف الموجودة:', [
                'count' => $expenses->count(),
                'user_id' => $userId,
                'query_sql' => $expensesQuery->toSql(),
                'query_bindings' => $expensesQuery->getBindings()
            ]);

            foreach ($expenses as $expense) {
                $transactions->push([
                    'id' => 'EXP-' . $expense->id,
                    'name' => 'مصروف: ' . $expense->name,
                    'type' => 'مصروف',
                    'amount' => $expense->amount,
                    'amount_after_discount' => $expense->amount,
                    'debit' => null, // لا يوجد مدين للمصروف
                    'credit' => 'صندوقي',
                    'status' => 'مقبوضة', // المصروف دائماً مقبوض
                    'date' => $expense->expense_date,
                    'source_type' => 'expense',
                    'source_id' => $expense->id,
                    'is_received' => true
                ]);
            }
        }

        // ترتيب البيانات حسب التاريخ (الأحدث أولاً)
        $transactions = $transactions->sortByDesc('date');

        Log::info('إجمالي المعاملات:', ['count' => $transactions->count()]);

        // حساب الإجماليات
        $totalCredit = $transactions->where('credit', 'صندوقي')->sum('amount_after_discount');
        $totalRevenue = $transactions->where('debit', 'صندوقي')->sum('amount_after_discount'); // الإيرادات (المبالغ التي يدخل فيها الصندوق مدين)
        $totalExpenses = $transactions->where('credit', 'صندوقي')->sum('amount_after_discount'); // المصاريف (المبالغ التي يكون فيها الصندوق دائن)
        $balance = ($cashBox->initial_balance ?? 0) + ($totalRevenue - $totalExpenses); // الرصيد الحالي = المبلغ الأولي + (الإيرادات - المصاريف)

        Log::info('الإجماليات:', [
            'totalCredit' => $totalCredit,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'initial_balance' => $cashBox->initial_balance ?? 0,
            'balance' => $balance
        ]);

        // تطبيق الترقيم
        $perPage = $request->input('per_page', 10);
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedTransactions = $transactions->slice($offset, $perPage);
        $total = $transactions->count();

        $response = [
            'transactions' => $paginatedTransactions->values(),
            'totalCredit' => $totalCredit,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'balance' => $balance,
            'transactionsCount' => $total,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => ceil($total / $perPage)
            ]
        ];

        Log::info('CashBox Data Response:', $response);

        // إذا لم تكن هناك بيانات، إرجاع رسالة واضحة
        if ($total === 0) {
            Log::info('لا توجد بيانات للمستخدم الحالي');
            $response['message'] = 'لا توجد بيانات لعرضها. يرجى إضافة فواتير أو مصاريف أولاً.';
        }

        return response()->json($response);
    }

    /**
     * تحديد حساب المدين حسب نوع الفاتورة
     */
    private function getDebitAccount($invoice)
    {
        $isReceived = $invoice->status === 'مقبوضة';

        switch ($invoice->type) {
            case 'بيع':
                return $isReceived ? 'صندوقي' : 'صندوق العميل';
            case 'شراء':
                return $isReceived ? 'صندوق العميل' : 'منتجاتي';
            case 'مردودات بيع':
                return $isReceived ? 'منتجاتي' : 'صندوق العميل';
            case 'مردودات شراء':
                return $isReceived ? 'صندوقي' : 'صندوقي';
            default:
                return 'صندوقي';
        }
    }

    /**
     * تحديد حساب الدائن حسب نوع الفاتورة
     */
    private function getCreditAccount($invoice)
    {
        $isReceived = $invoice->status === 'مقبوضة';

        switch ($invoice->type) {
            case 'بيع':
                return $isReceived ? 'صندوق العميل' : 'منتجاتي';
            case 'شراء':
                return $isReceived ? 'صندوقي' : 'صندوق العميل';
            case 'مردودات بيع':
                return $isReceived ? 'صندوقي' : 'منتجاتي';
            case 'مردودات شراء':
                return $isReceived ? 'صندوق العميل' : 'منتجاتي';
            default:
                return 'صندوق العميل';
        }
    }

    /**
     * الحصول على بيانات الصندوق (الطريقة القديمة - للتوافق)
     */
    public function getDataOld(Request $request)
    {
        $cashBox = CashBox::where('user_id', $request->user()->id)->first();

        if (!$cashBox) {
            return response()->json(['error' => 'لم يتم العثور على صندوق للمستخدم'], 404);
        }

        // تحديث الرصيد
        $cashBox->reconcileBalance();

        // إعداد فلاتر البحث
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $searchTerm = $request->input('search');
        $operationType = $request->input('operation_type');
        $sourceType = $request->input('source_type');
        $isReceived = $request->input('is_received');

        // استعلام القيود المحاسبية
        $query = AccountingEntry::query()
            ->where(function($q) {
                $q->where('debtor_type', 'صندوقي')
                    ->orWhere('creditor_type', 'صندوقي');
            })
            ->when($startDate, function($q) use ($startDate) {
                return $q->whereDate('entry_date', '>=', $startDate);
            })
            ->when($endDate, function($q) use ($endDate) {
                return $q->whereDate('entry_date', '<=', $endDate);
            })
            ->when($operationType, function($q) use ($operationType) {
                return $q->where('operation_type', $operationType);
            })
            ->when($sourceType, function($q) use ($sourceType) {
                return $q->where('sourceable_type', 'like', "%{$sourceType}%");
            })
            ->when($isReceived !== null, function($q) use ($isReceived) {
                return $q->where('is_received', $isReceived);
            })
            ->when($searchTerm, function($q) use ($searchTerm) {
                return $q->where(function($sq) use ($searchTerm) {
                    $sq->where('operation_description', 'like', "%{$searchTerm}%")
                        ->orWhere('notes', 'like', "%{$searchTerm}%");
                });
            });

        // الحصول على إجماليات الفترة
        $summary = [
            'total_in' => (clone $query)->where('debtor_type', 'صندوقي')->sum('amount'),
            'total_out' => (clone $query)->where('creditor_type', 'صندوقي')->sum('amount'),
        ];
        $summary['net'] = $summary['total_in'] - $summary['total_out'];

        // الحصول على الحركات مع الترقيم
        $entries = $query->orderBy('entry_date', 'desc')
            ->paginate($request->input('per_page', 10));

        // تحويل البيانات إلى التنسيق المطلوب
        $movements = $entries->map(function($entry) {
            return [
                'id' => $entry->id,
                'date' => $entry->entry_date->format('Y-m-d H:i'),
                'type' => $entry->operation_type,
                'description' => $entry->operation_description,
                'amount' => $entry->amount,
                'movement_type' => $entry->debtor_type === 'صندوقي' ? 'in' : 'out',
                'source_type' => class_basename($entry->sourceable_type),
                'source_id' => $entry->sourceable_id,
                'is_received' => $entry->is_received,
                'notes' => $entry->notes,
                'formatted_amount' => $entry->formatted_amount
            ];
        });

        return response()->json([
            'current_balance' => $cashBox->current_balance,
            'formatted_balance' => $cashBox->formatted_balance,
            'last_calculation_date' => $cashBox->last_calculation_date?->format('Y-m-d H:i'),
            'summary' => $summary,
            'movements' => $movements,
            'pagination' => [
                'total' => $entries->total(),
                'per_page' => $entries->perPage(),
                'current_page' => $entries->currentPage(),
                'last_page' => $entries->lastPage()
            ]
        ]);
    }

    /**
     * الحصول على الإحصائيات
     */
    public function getStats(Request $request)
    {
        $cashBox = CashBox::where('user_id', $request->user()->id)->first();

        if (!$cashBox) {
            return response()->json(['error' => 'لم يتم العثور على صندوق للمستخدم'], 404);
        }

        // إحصائيات اليوم
        $today = Carbon::today();
        $todayStats = $cashBox->getMovementsSummary($today, $today);

        // إحصائيات الأسبوع
        $weekStart = Carbon::now()->startOfWeek();
        $weekStats = $cashBox->getMovementsSummary($weekStart, Carbon::now());

        // إحصائيات الشهر
        $monthStart = Carbon::now()->startOfMonth();
        $monthStats = $cashBox->getMovementsSummary($monthStart, Carbon::now());

        // الرصيد الحالي
        $currentBalance = $cashBox->current_balance;

        return response()->json([
            'today' => $todayStats,
            'week' => $weekStats,
            'month' => $monthStats,
            'current_balance' => $currentBalance,
            'formatted_balance' => $cashBox->formatted_balance
        ]);
    }

    /**
     * تحديث المبلغ الأولي للصندوق
     */
    public function updateInitialBalance(Request $request)
    {
        // التحقق من تسجيل دخول المستخدم
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول أولاً'], 401);
        }

        $request->validate([
            'initial_balance' => 'required|numeric|min:0'
        ]);

        $userId = Auth::id();
        Log::info('Updating initial balance for user:', ['user_id' => $userId, 'balance' => $request->initial_balance]);

        $cashBox = CashBox::where('user_id', $userId)->first();
        if (!$cashBox) {
            $cashBox = CashBox::create([
                'user_id' => $userId,
                'name' => 'الصندوق الرئيسي',
                'initial_balance' => $request->initial_balance,
                'current_balance' => $request->initial_balance
            ]);
        } else {
            $cashBox->update([
                'initial_balance' => $request->initial_balance
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ المبلغ الأولي بنجاح',
            'initial_balance' => number_format($request->initial_balance, 2)
        ]);
    }
}
