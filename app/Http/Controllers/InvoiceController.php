<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Invoice::with('client');
        // عرض فقط فواتير المستخدم الحالي
        $userId = Auth::id() ?? 1;
        $query->where('user_id', $userId);
        // تصفية حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // تصفية حسب العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        // تصفية حسب التاريخ
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        // بحث نصي برقم الفاتورة أو اسم العميل
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                         ->orWhere('phone', 'like', "%$search%")
                         ->orWhere('address', 'like', "%$search%") ;
                  });
            });
        }
        $countQuery = clone $query;
        $invoiceCount = $countQuery->count();
        $paidCountQuery = clone $query;
        $invoicePaidCount = $paidCountQuery->where('status', 'مقبوضة')->count();
        $unpaidCountQuery = clone $query;
        $invoiceUnpaidCount = $unpaidCountQuery->where('status', 'غير مقبوضة')->count();
        $invoices = $query->orderByDesc('id')->paginate(6);
        $clients = \App\Models\Client::where('user_id', $userId)->get();
        $totalSalesPaid = \App\Models\Invoice::with('items')
            ->where('user_id', $userId)
            ->where('type', 'بيع')
            ->where('status', 'مقبوضة')
            ->get()
            ->sum(function($invoice) {
                return $invoice->items->sum(function($item) {
                    return $item->quantity * $item->price;
                });
            });
        // ملخص الفواتير حسب النوع والعملة المستلمة
        $summaryByTypeAndReceivedCurrency = \App\Models\Invoice::select('type', 'received_currency', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_after_discount) as total'))
            ->where('user_id', $userId)
            ->groupBy('type', 'received_currency')
            ->get();
        return view('dashboard.invoices-list', compact('invoices', 'clients', 'invoiceCount', 'invoicePaidCount', 'invoiceUnpaidCount', 'totalSalesPaid', 'summaryByTypeAndReceivedCurrency'));
    }

    /**
     * إرجاع قائمة الفواتير بصيغة JSON للاستخدام في الواجهة الأمامية (API)
     */
    public function apiIndex(Request $request)
    {
        $query = \App\Models\Invoice::with('client');
        $userId = Auth::id() ?? 1;
        $query->where('user_id', $userId);
        // تصفية حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        // تصفية حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // تصفية حسب العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        // تصفية حسب التاريخ
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        // بحث نصي برقم الفاتورة أو اسم العميل
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                         ->orWhere('phone', 'like', "%$search%")
                         ->orWhere('address', 'like', "%$search%") ;
                  });
            });
        }
        $invoices = $query->orderBy('date', 'desc')->get();
        // إعادة هيكلة البيانات لتناسب الجدول في الواجهة الأمامية
        $data = $invoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'number' => $invoice->invoice_number,
                'client' => $invoice->client ? $invoice->client->name : '',
                'date' => $invoice->date,
                'type' => $invoice->type,
                'amount' => $invoice->items->sum(function($item) { return $item->quantity * $item->price; }),
                'status' => $invoice->status,
            ];
        });
        return response()->json(['invoices' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::id() ?? 1;
        $lastInvoice = \App\Models\Invoice::where('user_id', $userId)->orderByDesc('id')->first();
        $nextNumber = $lastInvoice ? ((int) $lastInvoice->invoice_number + 1) : 1;
        $clients = \App\Models\Client::where('user_id', $userId)->get();
        $products = \App\Models\Product::where('user_id', $userId)->get();
        $productsArray = $products->map(function($p){
            return [
                'id' => $p->id,
                'name' => $p->name,
                'quantity' => $p->quantity,
                'sale_price' => $p->sale_price,
                'purchase_price' => $p->purchase_price,
            ];
        })->values()->all();
        return view('dashboard.create-invoice', compact('clients', 'nextNumber', 'products', 'productsArray'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // حماية: تأكد من أن المستخدم مسجل دخول
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        // تحقق من صحة البيانات
        $rules = [
            'invoice_number' => 'required|string',
            'type' => 'required|in:شراء,بيع,مردودات شراء,مردودات بيع',
            'status' => 'required|in:مقبوضة,غير مقبوضة',
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'received_currency' => 'required|string|max:10',
            'exchange_rate' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
        if (in_array($request->type, ['بيع', 'مردودات بيع', 'مردودات شراء'])) {
            $rules['products.*.product_id'] = 'required|exists:products,id';
        }
        if ($request->type === 'شراء') {
            $rules['products.*.name'] = 'required|string';
        }
        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            // إنشاء الفاتورة
            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'type' => $request->type,
                'status' => $request->status,
                'client_id' => $request->client_id,
                'date' => $request->date,
                'received_currency' => $request->received_currency,
                'exchange_rate' => $request->exchange_rate,
                'discount' => $request->discount ?? 0,
                'total' => 0, // سيتم حسابه لاحقاً
                'total_after_discount' => 0, // سيتم حسابه لاحقاً
                'user_id' => $userId // استخدم معرف المستخدم الحالي
            ]);

            $total = 0;

            // إضافة المنتجات
            foreach ($request->products as $product) {
                // إذا كان شراء، أنشئ منتج جديد أو حدث الموجود
                if ($request->type === 'شراء') {
                    $productModel = \App\Models\Product::firstOrCreate(
                        [
                            'name' => $product['name'],
                            'user_id' => $userId // استخدم معرف المستخدم الحالي
                        ],
                        [
                            'purchase_price' => $product['price'],
                            'quantity' => 0,
                            'user_id' => $userId // استخدم معرف المستخدم الحالي
                        ]
                    );
                    $productModel->quantity += $product['quantity'];
                    $productModel->purchase_price = $product['price'];
                    $productModel->save();
                }
                // إذا كان بيع أو مردودات، تحقق من المنتج الموجود
                else {
                    $productModel = \App\Models\Product::where('user_id', $userId)
                        ->findOrFail($product['product_id']);

                    // تحديث الكمية حسب نوع الفاتورة
                    if ($request->type === 'بيع') {
                        $productModel->quantity -= $product['quantity'];
                    } elseif ($request->type === 'مردودات بيع') {
                        $productModel->quantity += $product['quantity'];
                    } elseif ($request->type === 'مردودات شراء') {
                        $productModel->quantity -= $product['quantity'];
                    }

                    $productModel->save();
                }

                // إضافة عنصر الفاتورة
                $invoiceItem = new InvoiceItem([
                    'product_id' => $productModel->id,
                    'name' => $productModel->name, // دائماً استخدم اسم المنتج من النموذج
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'price_received' => $product['price_received'] ?? null,
                    'total' => $product['quantity'] * $product['price'],
                    'total_received' => isset($product['price_received']) ? $product['quantity'] * $product['price_received'] : null
                ]);

                $invoice->items()->save($invoiceItem);
                $total += $invoiceItem->total;
            }

            // تحديث إجمالي الفاتورة
            $invoice->total = $total;
            $invoice->total_after_discount = $total - ($request->discount ?? 0);
            $invoice->save();

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating invoice: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoice = \App\Models\Invoice::with(['client', 'items'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // حساب الإجمالي
        $total = 0;
        foreach ($invoice->items as $item) {
            $total += $item->quantity * $item->price;
        }
        $userCurrency = Auth::user()->currency ?? $invoice->currency;
        return view('dashboard.invoice', compact('invoice', 'total', 'userCurrency'));
    }

    /**
     * طباعة الفاتورة
     */
    public function print($id)
    {
        $invoice = \App\Models\Invoice::with(['client', 'items'])->findOrFail($id);
        $total = 0;
        foreach ($invoice->items as $item) {
            $total += $item->quantity * $item->price;
        }

        // الحصول على عملة المستخدم (نفس منطق دالة show)
        $userCurrency = Auth::user()->currency ?? $invoice->currency ?? 'ر.س';

        return view('dashboard.invoice', compact('invoice', 'total', 'userCurrency'));
    }

    /**
     * تحميل الفاتورة PDF
     */
    public function download($id)
    {
        $invoice = \App\Models\Invoice::with(['client', 'items'])->findOrFail($id);

        // الحصول على عملة المستخدم (نفس منطق دالة show)
        $userCurrency = Auth::user()->currency ?? $invoice->currency ?? 'ر.س';

        // محتوى PDF بسيط مؤقتاً
        $content = "فاتورة رقم: {$invoice->invoice_number}\nالعميل: {$invoice->client->name}\nالمبلغ: {$invoice->total} {$userCurrency}";
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="invoice-{$invoice->invoice_number}.pdf"');
    }

    /**
     * جلب بيانات الفاتورة بتنسيق JSON للتصدير
     */
    public function getInvoiceData($id)
    {
        $invoice = \App\Models\Invoice::with(['client', 'items.product'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // تجهيز بيانات الفاتورة للتصدير
        $invoiceData = [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'client_name' => $invoice->client->name ?? 'غير محدد',
            'date' => $invoice->date,
            'type' => $invoice->type,
            'status' => $invoice->status,
            'total' => $invoice->total,
            'total_after_discount' => $invoice->total_after_discount,
            'items' => $invoice->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price
                ];
            })
        ];

        return response()->json($invoiceData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $invoice = \App\Models\Invoice::with('items', 'client')
            ->where('user_id', $userId)
            ->findOrFail($id);
        $clients = \App\Models\Client::where('user_id', $userId)->get();
        $products = \App\Models\Product::where('user_id', $userId)->get();
        $productsArray = $products->map(function($p){
            return [
                'id' => $p->id,
                'name' => $p->name,
                'quantity' => $p->quantity,
                'sale_price' => $p->sale_price,
                'purchase_price' => $p->purchase_price,
            ];
        })->values()->all();

        $oldInvoiceItems = $invoice->items->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'price_received' => $item->price_received
            ];
        });

        return view('dashboard.edit-invoice', compact('invoice', 'clients', 'products', 'productsArray', 'oldInvoiceItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $invoice = \App\Models\Invoice::with('items')
            ->where('user_id', $userId)
            ->findOrFail($id);
        $oldType = $invoice->type;
        $oldItems = $invoice->items;

        // 1. عكس أثر الفاتورة القديمة على المنتجات
        foreach ($oldItems as $item) {
            if ($item->product_id) {
                $product = \App\Models\Product::where('user_id', $userId)
                    ->find($item->product_id);
                if ($product) {
                    if ($oldType === 'بيع') {
                        // أرجع الكمية للمنتج
                        $product->quantity += $item->quantity;
                        $product->save();
                    } elseif ($oldType === 'شراء') {
                        // أنقص الكمية من المنتج
                        $product->quantity -= $item->quantity;
                        $product->save();
                    } elseif ($oldType === 'مردودات بيع') {
                        // أنقص الكمية من المنتج
                        $product->quantity -= $item->quantity;
                        $product->save();
                    } elseif ($oldType === 'مردودات شراء') {
                        // أرجع الكمية للمنتج
                        $product->quantity += $item->quantity;
                        $product->save();
                    }
                }
            }
        }

        // 2. تحقق من صحة البيانات الجديدة
        $rules = [
            'invoice_number' => 'required|string',
            'type' => 'required|in:شراء,بيع,مردودات شراء,مردودات بيع',
            'status' => 'required|in:مقبوضة,غير مقبوضة',
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'currency' => 'required|string|max:10',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
        ];
        if (in_array($request->type, ['بيع', 'مردودات بيع', 'مردودات شراء'])) {
            $rules['products.*.product_id'] = 'required|exists:products,id';
        }
        if ($request->type === 'شراء') {
            $rules['products.*.name'] = 'required|string';
        }
        $validated = $request->validate($rules);

        // 3. تطبيق أثر الفاتورة الجديدة على المنتجات
        foreach ($validated['products'] as $item) {
            $product = null;
            if (in_array($validated['type'], ['بيع', 'مردودات بيع', 'مردودات شراء']) && isset($item['product_id'])) {
                $product = \App\Models\Product::where('user_id', $userId)
                    ->find($item['product_id']);
                if ($validated['type'] === 'بيع') {
                    // خصم الكمية عند البيع
                    if (!$product || $item['quantity'] > $product->quantity) {
                        return back()->withErrors(['products' => 'لا توجد لديك كمية كافية من المنتج: ' . ($product ? $product->name : '')])->withInput();
                    }
                    $product->quantity -= $item['quantity'];
                    $product->save();
                } elseif ($validated['type'] === 'مردودات بيع') {
                    // زيادة الكمية عند مردودات البيع
                    if ($product) {
                        $product->quantity += $item['quantity'];
                        $product->save();
                    }
                } elseif ($validated['type'] === 'مردودات شراء') {
                    // إنقاص الكمية عند مردودات الشراء
                    if ($product) {
                        if ($item['quantity'] > $product->quantity) {
                            return back()->withErrors(['products' => 'لا توجد كمية كافية من المنتج: ' . $product->name])->withInput();
                        }
                        $product->quantity -= $item['quantity'];
                        $product->save();
                    }
                }
            }
            if ($validated['type'] === 'شراء') {
                // ابحث عن المنتج بالاسم للمستخدم الحالي
                $product = \App\Models\Product::where('user_id', $userId)
                    ->where('name', $item['name'])
                    ->first();
                if ($product) {
                    // زيادة الكمية إذا كان المنتج موجود
                    $product->quantity += $item['quantity'];
                    $product->purchase_price = $item['price']; // تحديث سعر الشراء
                    $product->save();
                } else {
                    // إنشاء منتج جديد إذا لم يكن موجوداً
                    $product = \App\Models\Product::create([
                        'name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'user_id' => $userId,
                        'purchase_price' => $item['price'],
                        'sale_price' => null,
                    ]);
                }
            }
        }

        // 4. تحديث بيانات الفاتورة
        $invoice->update([
            'invoice_number' => $validated['invoice_number'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'client_id' => $validated['client_id'],
            'date' => $validated['date'],
            'currency' => $validated['currency'],
            'discount' => $request->input('discount', 0),
            'received_currency' => $request->input('received_currency'),
            'exchange_rate' => $request->input('exchange_rate'),
        ]);

        // 5. حذف العناصر القديمة وإضافة الجديدة
        $invoice->items()->delete();
        $total = 0;
        foreach ($validated['products'] as $item) {
            $productId = null;
            $productName = $item['name'] ?? '';
            if (in_array($validated['type'], ['بيع', 'مردودات بيع', 'مردودات شراء']) && isset($item['product_id'])) {
                $productId = $item['product_id'];
                $product = \App\Models\Product::where('user_id', $userId)
                    ->find($productId);
                $productName = $product ? $product->name : '';
            } elseif ($validated['type'] === 'شراء') {
                $product = \App\Models\Product::where('user_id', $userId)
                    ->where('name', $item['name'])
                    ->first();
                $productId = $product ? $product->id : null;
            }
            $itemTotal = $item['quantity'] * $item['price'];
            $total += $itemTotal;

            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'name' => $productName,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'product_id' => $productId,
                'price_received' => $item['price_received'] ?? null,
                'total' => $itemTotal,
                'total_received' => $item['total_received'] ?? null,
            ]);
        }

        // 6. تحديث إجمالي الفاتورة
        $invoice->total = $total;
        $invoice->total_after_discount = $total - ($request->input('discount', 0));
        $invoice->save();

        return redirect()->route('invoices.edit', $id)->with('success', 'تم تعديل الفاتورة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = \App\Models\Invoice::where('user_id', Auth::id())
            ->findOrFail($id);
        $invoice->delete();

        // إذا كان الطلب AJAX (من الجافاسكريبت)
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // إذا كان الطلب عادي (من فورم)
        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة بنجاح');
    }

    /**
     * إرجاع ملخص الفواتير للداشبورد
     */
    public function getDashboardSummary()
    {
        $user = Auth::user();

        // التأكد من وجود عملة افتراضية للمستخدم
        $defaultCurrency = $user->currency ?? 'SAR';

        // إحصائيات الفواتير - إزالة هذا المتغير لأنه لم يعد مستخدماً
        $totalSales = Invoice::where('user_id', $user->id)
            ->where('type', 'بيع')
            ->where('status', 'مقبوضة')
            ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
            ->sum('total_after_discount') ?? 0;
        $totalPurchases = Invoice::where('user_id', $user->id)
            ->where('type', 'شراء')
            ->where('status', 'مقبوضة')
            ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
            ->sum('total_after_discount') ?? 0;
        $pendingInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'غير مقبوضة')
            ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
            ->count();

        // Calculate percentage change from last week for all invoices
        $thisWeekInvoices = Invoice::where('user_id', $user->id)
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $lastWeekInvoices = Invoice::where('user_id', $user->id)
            ->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        // تحسين حساب النسبة المئوية
        if ($lastWeekInvoices == 0) {
            // إذا لم تكن هناك فواتير الأسبوع الماضي، النسبة تعتمد على عدد الفواتير هذا الأسبوع
            $invoicesPercent = $thisWeekInvoices > 0 ? 100 : 0;
        } else {
            $invoicesPercent = round((($thisWeekInvoices - $lastWeekInvoices) / $lastWeekInvoices) * 100, 1);
        }

        // Calculate percentage change from last week for paid invoices
        $thisWeekPaidInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'مقبوضة')
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $lastWeekPaidInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'مقبوضة')
            ->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        // تحسين حساب النسبة المئوية للفواتير المقبوضة
        if ($lastWeekPaidInvoices == 0) {
            $paidPercent = $thisWeekPaidInvoices > 0 ? 100 : 0;
        } else {
            $paidPercent = round((($thisWeekPaidInvoices - $lastWeekPaidInvoices) / $lastWeekPaidInvoices) * 100, 1);
        }

        // Calculate percentage change from last week for unpaid invoices
        $thisWeekUnpaidInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'غير مقبوضة')
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $lastWeekUnpaidInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'غير مقبوضة')
            ->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        // تحسين حساب النسبة المئوية للفواتير غير المقبوضة
        if ($lastWeekUnpaidInvoices == 0) {
            $unpaidPercent = $thisWeekUnpaidInvoices > 0 ? 100 : 0;
        } else {
            $unpaidPercent = round((($thisWeekUnpaidInvoices - $lastWeekUnpaidInvoices) / $lastWeekUnpaidInvoices) * 100, 1);
        }

        // إضافة حساب النسب المئوية بناءً على الشهر السابق كبديل
        if ($invoicesPercent == 0 && $thisWeekInvoices == 0) {
            // إذا لم تكن هناك فواتير هذا الأسبوع، احسب نسبة مقارنة بالشهر السابق
            $thisMonthInvoices = Invoice::where('user_id', $user->id)
                ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
            $lastMonthInvoices = Invoice::where('user_id', $user->id)
                ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();

            if ($lastMonthInvoices > 0) {
                $invoicesPercent = round((($thisMonthInvoices - $lastMonthInvoices) / $lastMonthInvoices) * 100, 1);
            } else {
                $invoicesPercent = $thisMonthInvoices > 0 ? 50 : 0; // نسبة متوسطة إذا كان هناك فواتير هذا الشهر
            }
        }

        if ($paidPercent == 0 && $thisWeekPaidInvoices == 0) {
            $thisMonthPaidInvoices = Invoice::where('user_id', $user->id)
                ->where('status', 'مقبوضة')
                ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
            $lastMonthPaidInvoices = Invoice::where('user_id', $user->id)
                ->where('status', 'مقبوضة')
                ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();

            if ($lastMonthPaidInvoices > 0) {
                $paidPercent = round((($thisMonthPaidInvoices - $lastMonthPaidInvoices) / $lastMonthPaidInvoices) * 100, 1);
            } else {
                $paidPercent = $thisMonthPaidInvoices > 0 ? 50 : 0;
            }
        }

        if ($unpaidPercent == 0 && $thisWeekUnpaidInvoices == 0) {
            $thisMonthUnpaidInvoices = Invoice::where('user_id', $user->id)
                ->where('status', 'غير مقبوضة')
                ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
            $lastMonthUnpaidInvoices = Invoice::where('user_id', $user->id)
                ->where('status', 'غير مقبوضة')
                ->whereBetween('date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();

            if ($lastMonthUnpaidInvoices > 0) {
                $unpaidPercent = round((($thisMonthUnpaidInvoices - $lastMonthUnpaidInvoices) / $lastMonthUnpaidInvoices) * 100, 1);
            } else {
                $unpaidPercent = $thisMonthUnpaidInvoices > 0 ? 50 : 0;
            }
        }

        // حساب النسب المئوية بناءً على متوسط النشاط الأسبوعي (آخر 4 أسابيع)
        if ($invoicesPercent == 0) {
            $weeklyAverages = [];
            for ($i = 1; $i <= 4; $i++) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                $weeklyCount = Invoice::where('user_id', $user->id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->count();
                $weeklyAverages[] = $weeklyCount;
            }

            $averageWeeklyInvoices = array_sum($weeklyAverages) / count($weeklyAverages);
            if ($averageWeeklyInvoices > 0) {
                $invoicesPercent = round((($thisWeekInvoices - $averageWeeklyInvoices) / $averageWeeklyInvoices) * 100, 1);
            } else {
                $invoicesPercent = $thisWeekInvoices > 0 ? 25 : 0;
            }
        }

        if ($paidPercent == 0) {
            $weeklyPaidAverages = [];
            for ($i = 1; $i <= 4; $i++) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                $weeklyPaidCount = Invoice::where('user_id', $user->id)
                    ->where('status', 'مقبوضة')
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->count();
                $weeklyPaidAverages[] = $weeklyPaidCount;
            }

            $averageWeeklyPaidInvoices = array_sum($weeklyPaidAverages) / count($weeklyPaidAverages);
            if ($averageWeeklyPaidInvoices > 0) {
                $paidPercent = round((($thisWeekPaidInvoices - $averageWeeklyPaidInvoices) / $averageWeeklyPaidInvoices) * 100, 1);
            } else {
                $paidPercent = $thisWeekPaidInvoices > 0 ? 25 : 0;
            }
        }

        if ($unpaidPercent == 0) {
            $weeklyUnpaidAverages = [];
            for ($i = 1; $i <= 4; $i++) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                $weeklyUnpaidCount = Invoice::where('user_id', $user->id)
                    ->where('status', 'غير مقبوضة')
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->count();
                $weeklyUnpaidAverages[] = $weeklyUnpaidCount;
            }

            $averageWeeklyUnpaidInvoices = array_sum($weeklyUnpaidAverages) / count($weeklyUnpaidAverages);
            if ($averageWeeklyUnpaidInvoices > 0) {
                $unpaidPercent = round((($thisWeekUnpaidInvoices - $averageWeeklyUnpaidInvoices) / $averageWeeklyUnpaidInvoices) * 100, 1);
            } else {
                $unpaidPercent = $thisWeekUnpaidInvoices > 0 ? 25 : 0;
            }
        }

        // حساب النسب المئوية بناءً على متوسط النشاط الشهري (آخر 3 أشهر)
        if ($invoicesPercent == 0) {
            $monthlyAverages = [];
            for ($i = 1; $i <= 3; $i++) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();
                $monthlyCount = Invoice::where('user_id', $user->id)
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();
                $monthlyAverages[] = $monthlyCount;
            }

            $averageMonthlyInvoices = array_sum($monthlyAverages) / count($monthlyAverages);
            if ($averageMonthlyInvoices > 0) {
                // تحويل متوسط الشهر إلى أسبوع (قسمة على 4)
                $averageWeeklyFromMonthly = $averageMonthlyInvoices / 4;
                $invoicesPercent = round((($thisWeekInvoices - $averageWeeklyFromMonthly) / $averageWeeklyFromMonthly) * 100, 1);
            } else {
                $invoicesPercent = $thisWeekInvoices > 0 ? 15 : 0;
            }
        }

        if ($paidPercent == 0) {
            $monthlyPaidAverages = [];
            for ($i = 1; $i <= 3; $i++) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();
                $monthlyPaidCount = Invoice::where('user_id', $user->id)
                    ->where('status', 'مقبوضة')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();
                $monthlyPaidAverages[] = $monthlyPaidCount;
            }

            $averageMonthlyPaidInvoices = array_sum($monthlyPaidAverages) / count($monthlyPaidAverages);
            if ($averageMonthlyPaidInvoices > 0) {
                $averageWeeklyPaidFromMonthly = $averageMonthlyPaidInvoices / 4;
                $paidPercent = round((($thisWeekPaidInvoices - $averageWeeklyPaidFromMonthly) / $averageWeeklyPaidFromMonthly) * 100, 1);
            } else {
                $paidPercent = $thisWeekPaidInvoices > 0 ? 15 : 0;
            }
        }

        if ($unpaidPercent == 0) {
            $monthlyUnpaidAverages = [];
            for ($i = 1; $i <= 3; $i++) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();
                $monthlyUnpaidCount = Invoice::where('user_id', $user->id)
                    ->where('status', 'غير مقبوضة')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->count();
                $monthlyUnpaidAverages[] = $monthlyUnpaidCount;
            }

            $averageMonthlyUnpaidInvoices = array_sum($monthlyUnpaidAverages) / count($monthlyUnpaidAverages);
            if ($averageMonthlyUnpaidInvoices > 0) {
                $averageWeeklyUnpaidFromMonthly = $averageMonthlyUnpaidInvoices / 4;
                $unpaidPercent = round((($thisWeekUnpaidInvoices - $averageWeeklyUnpaidFromMonthly) / $averageWeeklyUnpaidFromMonthly) * 100, 1);
            } else {
                $unpaidPercent = $thisWeekUnpaidInvoices > 0 ? 15 : 0;
            }
        }

        // حساب أرباح الشهر الحالي بناءً على ربح كل منتج (سعر البيع - سعر الشراء الحالي) * الكمية
        $profitThisMonth = 0;
        $salesInvoices = \App\Models\Invoice::with('items.product')
            ->where('user_id', $user->id)
            ->where('type', 'بيع')
            ->where('status', 'مقبوضة')
            ->whereBetween('date', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();
        foreach ($salesInvoices as $invoice) {
            foreach ($invoice->items as $item) {
                $purchasePrice = $item->product ? $item->product->purchase_price : 0;
                $profitThisMonth += ($item->price - $purchasePrice) * $item->quantity;
            }
        }

        // Get top selling products
        $topProducts = \App\Models\InvoiceItem::join('products', 'invoice_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(invoice_items.quantity) as total_quantity'))
            ->whereHas('invoice', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('type', 'بيع')
                    ->where('status', 'مقبوضة')
                    ->where('date', '>=', now()->subMonths(3)); // إضافة فلتر التاريخ الفعلي
            })
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->pluck('total_quantity', 'name')
            ->toArray();

        // Get out of stock products
        $outOfStock = Product::where('user_id', $user->id)
            ->where('quantity', '<=', 0)
            ->pluck('name')
            ->toArray();

        // Get all received currencies used in invoices
        $allCurrencies = Invoice::where('user_id', $user->id)
            ->whereNotNull('received_currency')
            ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
            ->distinct()
            ->pluck('received_currency')
            ->toArray();

        // Always include default currency
        if (!in_array($defaultCurrency, $allCurrencies)) {
            $allCurrencies[] = $defaultCurrency;
        }

        // If no currencies found, use default
        if (empty($allCurrencies)) {
            $allCurrencies = [$defaultCurrency];
        }

        // Calculate totals by invoice type and received currency
        $totalsByTypeAndCurrency = [];
        $invoiceTypes = ['بيع', 'شراء', 'مردودات بيع', 'مردودات شراء'];

        foreach ($invoiceTypes as $type) {
            $totalsByTypeAndCurrency[$type] = [];
            foreach ($allCurrencies as $currency) {
                $total = Invoice::where('user_id', $user->id)
                    ->where('type', $type)
                    ->where('received_currency', $currency)
                    ->where('status', 'مقبوضة')
                    ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
                    ->sum('total_after_discount') ?? 0;
                $totalsByTypeAndCurrency[$type][$currency] = $total;
            }
        }

        // إحصائيات المنتجات
        $lowStockProducts = Product::where('user_id', $user->id)
            ->where('quantity', '<', 10)
            ->count();
        $totalProducts = Product::where('user_id', $user->id)->count();

        // إحصائيات العملاء
        $totalClients = Client::where('user_id', $user->id)->count();
        $activeClients = Client::where('user_id', $user->id)
            ->whereHas('invoices', function($query) {
                $query->where('date', '>=', now()->subMonths(3));
            })
            ->count();

        // أكثر العملاء شراءً
        $topSellingClientData = \App\Models\Invoice::where('user_id', $user->id)
            ->where('type', 'بيع')
            ->where('status', 'مقبوضة')
            ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
            ->select('client_id', DB::raw('SUM(total_after_discount) as total'))
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->first();
        $topSellingClient = null;
        $topSellingClientTotal = 0;
        if ($topSellingClientData) {
            $client = \App\Models\Client::find($topSellingClientData->client_id);
            $topSellingClient = $client ? $client->name : null;
            $topSellingClientTotal = $topSellingClientData->total;
        }

        // أكثر الموردين (أكثر من مجموع قيمة الشراء)
        $topSupplierData = \App\Models\Invoice::where('user_id', $user->id)
            ->where('type', 'شراء')
            ->where('status', 'مقبوضة')
            ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
            ->select('client_id', DB::raw('SUM(total_after_discount) as total'))
            ->groupBy('client_id')
            ->orderByDesc('total')
            ->first();
        $topSupplier = null;
        $topSupplierTotal = 0;
        if ($topSupplierData) {
            $supplier = \App\Models\Client::find($topSupplierData->client_id);
            $topSupplier = $supplier ? $supplier->name : null;
            $topSupplierTotal = $topSupplierData->total;
        }

        // تجهيز بيانات الرسم البياني للمبيعات لآخر شهر (30 يوم)
        $salesLastMonthChart = [];
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();

        $allDates = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $allDates[$date->format('Y-m-d')] = 0;
        }

        $sales = \App\Models\Invoice::where('user_id', $user->id)
            ->where('type', 'بيع')
            ->where('status', 'مقبوضة')
            ->whereBetween('date', [$startDate, $endDate])
            ->select(DB::raw('DATE(date) as date'), DB::raw('SUM(total_after_discount) as total'))
            ->groupBy(DB::raw('DATE(date)'))
            ->pluck('total', 'date')
            ->toArray();

        // دمج النتائج مع كل الأيام (حتى الأيام بدون مبيعات تظهر صفر)
        $salesLastMonthChart = array_merge($allDates, $sales);
        ksort($salesLastMonthChart);

        // تجهيز بيانات رسم بياني الأرباح اليومية لآخر شهر (30 يوم) مع مراعاة الحسم
        $profitLastMonthChart = $allDates;
        $salesInvoices = \App\Models\Invoice::with('items.product')
            ->where('user_id', $user->id)
            ->where('type', 'بيع')
            ->where('status', 'مقبوضة')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        foreach ($salesInvoices as $invoice) {
            $date = $invoice->date->format('Y-m-d');
            $profit = 0;
            $totalItemsProfit = 0;
            $itemsProfits = [];
            foreach ($invoice->items as $item) {
                $purchasePrice = $item->product ? $item->product->purchase_price : 0;
                $itemProfit = ($item->price - $purchasePrice) * $item->quantity;
                $itemsProfits[] = $itemProfit;
                $totalItemsProfit += $itemProfit;
            }
            // توزيع الحسم على الأرباح النسبية لكل منتج
            $discount = $invoice->discount ?? 0;
            foreach ($itemsProfits as $i => $itemProfit) {
                if ($totalItemsProfit > 0) {
                    $itemsProfits[$i] -= ($itemProfit / $totalItemsProfit) * $discount;
                }
            }
            $profit = array_sum($itemsProfits);
            $profitLastMonthChart[$date] += $profit;
        }
        ksort($profitLastMonthChart);

        return [
            'count' => Invoice::where('user_id', $user->id)
                ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
                ->count(),
            'totalSales' => $totalSales,
            'totalPurchases' => $totalPurchases,
            'pendingInvoices' => $pendingInvoices,
            'lowStockProducts' => $lowStockProducts,
            'totalProducts' => $totalProducts,
            'totalClients' => $totalClients,
            'activeClients' => $activeClients,
            'paid' => Invoice::where('user_id', $user->id)
                ->where('status', 'مقبوضة')
                ->where('date', '>=', now()->subMonths(3)) // إضافة فلتر التاريخ الفعلي
                ->count(),
            'unpaid' => $pendingInvoices,
            'invoices_percent' => $invoicesPercent,
            'paid_percent' => $paidPercent,
            'unpaid_percent' => $unpaidPercent,
            'profit_last_month' => $profitThisMonth,
            'top_products' => $topProducts,
            'out_of_stock' => $outOfStock,
            'all_currencies' => $allCurrencies,
            'totals_by_type_and_currency' => $totalsByTypeAndCurrency,
            'currency' => $defaultCurrency,
            'top_selling_client' => $topSellingClient,
            'top_selling_client_total' => $topSellingClientTotal,
            'top_supplier' => $topSupplier,
            'top_supplier_total' => $topSupplierTotal,
            'sales_last_month_chart' => $salesLastMonthChart,
            'profit_last_month_chart' => $profitLastMonthChart,
        ];
    }

    /**
     * API: إرجاع آخر 5 أسعار شراء لمنتج معيّن
     */
    public function lastPurchasePrices(Request $request)
    {
        $productId = $request->input('product_id') ?? $request->route('id');
        $userId = Auth::id();

        if (!$productId) {
            return response()->json(['prices' => []]);
        }

        $items = \App\Models\InvoiceItem::whereHas('invoice', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('type', 'شراء')
                  ->where('status', 'مقبوضة'); // فقط الفواتير المقبوضة
            })
            ->where('product_id', $productId)
            ->where('price', '>', 0) // فقط الأسعار الموجبة
            ->orderByDesc('id')
            ->limit(5)
            ->pluck('price')
            ->toArray();

        return response()->json(['prices' => $items]);
    }

    /**
     * API: جلب آخر 5 أسعار شراء حسب اسم المنتج
     */
    public function lastPurchasePricesByName(Request $request)
    {
        $productName = $request->input('name');
        $userId = Auth::id();

        // تنظيف اسم المنتج
        $cleanProductName = trim($productName);
        if (empty($cleanProductName)) {
            return response()->json(['prices' => []]);
        }

        // البحث عن المنتج
        $product = \App\Models\Product::where('user_id', $userId)
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($cleanProductName)])
            ->first();

        if (!$product) {
            return response()->json(['prices' => []]);
        }

        // جلب آخر 5 أسعار شراء للمنتج
        $items = \App\Models\InvoiceItem::whereHas('invoice', function($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('type', 'شراء')
                  ->where('status', 'مقبوضة'); // فقط الفواتير المقبوضة
            })
            ->where('product_id', $product->id)
            ->where('price', '>', 0) // فقط الأسعار الموجبة
            ->orderByDesc('id')
            ->limit(5)
            ->pluck('price')
            ->toArray();

        return response()->json(['prices' => $items]);
    }
}

