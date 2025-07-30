<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     * إرجاع قائمة المصاريف مع الإحصائيات والتصفية والبحث
     */
    public function index(Request $request)
    {
        return view('dashboard.expenses-list', [
            'title' => 'المصاريف',
            'layout' => 'layouts.app'
        ]);
    }

    public function apiIndex(Request $request)
    {
        $user = Auth::user();
        $query = Expense::where('user_id', $user->id);

        // بحث بالاسم
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        // تصفية حسب التاريخ
        if ($request->filled('date')) {
            if ($request->date === 'today') {
                $query->whereDate('expense_date', now()->toDateString());
            } elseif ($request->date === 'yesterday') {
                $query->whereDate('expense_date', now()->subDay()->toDateString());
            } elseif ($request->date === 'week') {
                $query->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->date === 'month') {
                $query->whereMonth('expense_date', now()->month);
            } elseif ($request->date === 'year') {
                $query->whereYear('expense_date', now()->year);
            }
        }
        // تصفية حسب المبلغ
        if ($request->filled('amount')) {
            if ($request->amount === 'low') {
                $query->where('amount', '<', 100);
            } elseif ($request->amount === 'medium') {
                $query->whereBetween('amount', [100, 500]);
            } elseif ($request->amount === 'high') {
                $query->where('amount', '>', 500);
            }
        }
        // ترتيب
        $sort = $request->get('sort');
        $dir = $request->get('dir');
        $sort = empty($sort) ? 'expense_date' : $sort;
        $dir = empty($dir) ? 'desc' : $dir;
        $query->orderBy($sort, $dir);

        $perPage = $request->get('per_page', 10);
        $expenses = $query->paginate($perPage);

        // إحصائيات
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');
        $todayExpenses = Expense::where('user_id', $user->id)->whereDate('expense_date', now()->toDateString())->sum('amount');
        $weekExpenses = Expense::where('user_id', $user->id)->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
        $expensesCount = Expense::where('user_id', $user->id)->count();

        $response = response()->json([
            'expenses' => array_values($expenses->map(function($expense) {
                return [
                    'id' => $expense->id,
                    'name' => $expense->name,
                    'amount' => $expense->amount,
                    'date' => $expense->expense_date,
                    'comment' => $expense->comment,
                ];
            })->toArray()),
            'pagination' => [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
            ],
            'statistics' => [
                'totalExpenses' => $totalExpenses,
                'todayExpenses' => $todayExpenses,
                'weekExpenses' => $weekExpenses,
                'expensesCount' => $expensesCount,
            ],
        ]);

        // إضافة headers لمنع التخزين المؤقت
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');

        return $response;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
            'comment' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $expense = Expense::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'comment' => $request->comment,
            'user_id' => Auth::id(),
        ]);
        return response()->json(['expense' => $expense], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['expense' => $expense]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $expense = Expense::where('user_id', Auth::id())->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'expense_date' => 'required|date',
                'comment' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $expense->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'comment' => $request->comment
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث المصروف بنجاح',
                'expense' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تحديث المصروف'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
            $expense->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف المصروف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء حذف المصروف'
            ], 500);
        }
    }
}
