<?php

namespace App\Http\Controllers;

use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountingEntryController extends Controller
{
    /**
     * عرض قائمة القيود المحاسبية
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AccountingEntry::where('user_id', $user->id);

        // البحث
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // التصفية حسب التاريخ
        if ($request->filled('date')) {
            if ($request->date === 'today') {
                $query->whereDate('entry_date', now()->toDateString());
            } elseif ($request->date === 'week') {
                $query->whereBetween('entry_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->date === 'month') {
                $query->whereMonth('entry_date', now()->month);
            } elseif ($request->date === 'year') {
                $query->whereYear('entry_date', now()->year);
            }
        }

        // التصفية حسب النوع
        if ($request->filled('type')) {
            $query->where('reference_type', $request->type);
        }

        // الترتيب
        $sort = $request->get('sort', 'entry_date');
        $dir = $request->get('dir', 'desc');
        $query->orderBy($sort, $dir);

        // الإحصائيات
        $statistics = [
            'balance' => $user->cashBox->balance ?? 0,
            'total_credits' => AccountingEntry::where('user_id', $user->id)
                ->whereHas('items', function ($q) {
                    $q->where('credit', '>', 0);
                })
                ->sum('credit'),
            'total_debits' => AccountingEntry::where('user_id', $user->id)
                ->whereHas('items', function ($q) {
                    $q->where('debit', '>', 0);
                })
                ->sum('debit'),
            'total_entries' => $query->count()
        ];

        // التصفح
        $entries = $query->paginate(10);

        return response()->json([
            'entries' => $entries,
            'statistics' => $statistics,
            'pagination' => [
                'current_page' => $entries->currentPage(),
                'last_page' => $entries->lastPage(),
                'per_page' => $entries->perPage(),
                'total' => $entries->total()
            ]
        ]);
    }

    /**
     * إنشاء قيد محاسبي جديد
     */
    public function store(Request $request)
    {
        try {
            $entry = AccountingEntry::createEntry(
                Auth::id(),
                $request->description,
                $request->parties
            );

            return response()->json([
                'status' => 'success',
                'message' => 'تم إنشاء القيد بنجاح',
                'entry' => $entry
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * عرض تفاصيل قيد محاسبي
     */
    public function show($id)
    {
        $entry = AccountingEntry::with('items')->findOrFail($id);

        // التحقق من الملكية
        if ($entry->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بعرض هذا القيد'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'entry' => $entry
        ]);
    }
}
