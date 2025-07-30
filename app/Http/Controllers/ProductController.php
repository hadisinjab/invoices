<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $userId = Auth::id() ?? 1;
        $products = Product::where('user_id', $userId)->orderBy('name')->get();

        // حساب متوسط سعر الشراء لكل منتج
        foreach ($products as $product) {
            $lastPurchase = \App\Models\InvoiceItem::where('product_id', $product->id)
                ->whereHas('invoice', function($q) {
                    $q->where('type', 'شراء');
                })
                ->orderByDesc('id')
                ->value('price');
            $product->last_purchase_price = $lastPurchase ? round($lastPurchase, 2) : 0;
        }

        return view('dashboard.products-list', compact('products'));
    }

    public function updateSalePrice(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        if (!$product->user_id) {
            return response()->json(['success' => false, 'message' => 'المنتج غير مرتبط بمستخدم!'], 400);
        }
        $request->validate([
            'sale_price' => 'required|numeric|min:0',
        ]);
        $product->sale_price = $request->sale_price;
        $product->save();
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث سعر المبيع بنجاح']);
        }
        return back()->with('success', 'تم تحديث سعر المبيع بنجاح');
    }

    public function updatePurchasePrice(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        if (!$product->user_id) {
            return response()->json(['success' => false, 'message' => 'المنتج غير مرتبط بمستخدم!'], 400);
        }
        $request->validate([
            'purchase_price' => 'required|numeric|min:0',
        ]);
        $product->purchase_price = $request->purchase_price;
        $product->save();
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث سعر الشراء بنجاح']);
        }
        return back()->with('success', 'تم تحديث سعر الشراء بنجاح');
    }

    /**
     * API: إكمال تلقائي للمنتجات (autocomplete) لاستخدامه مع Select2
     */
    public function autocomplete(Request $request)
    {
        $userId = Auth::id() ?? 1;
        $q = $request->input('q');
        $products = Product::where('user_id', $userId)
            ->when($q, function($query) use ($q) {
                $query->where('name', 'like', "%$q%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'quantity', 'sale_price', 'purchase_price']); // أضف purchase_price
        return response()->json(['products' => $products]);
    }

    /**
     * API: البحث عن المنتجات لاستخدامه مع Select2 في صفحة قائمة المنتجات
     */
    public function search(Request $request)
    {
        $userId = Auth::id() ?? 1;
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = Product::where('user_id', $userId);

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $products = $query->orderBy('name')
            ->paginate($perPage, ['id', 'name', 'quantity'], 'page', $page);

        return response()->json([
            'data' => $products->items(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'total' => $products->total()
        ]);
    }
}
