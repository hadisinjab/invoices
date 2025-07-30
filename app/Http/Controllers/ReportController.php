<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Expense;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $userId = auth()->id();
        $today = Carbon::today();
        // جلب الفواتير المقبوضة لليوم فقط
        $invoices = Invoice::where('user_id', $userId)
            ->where('status', 'مقبوضة')
            ->whereDate('date', $today)
            ->orderBy('date', 'desc')
            ->get();
        // جلب مصاريف اليوم فقط
        $expenses = Expense::where('user_id', $userId)
            ->whereDate('expense_date', $today)
            ->orderBy('expense_date', 'desc')
            ->get();
        // دمج الفواتير والمصاريف في مصفوفة واحدة مع تصنيف كل سجل
        $rows = [];
        foreach ($invoices as $invoice) {
            $rows[] = [
                'date' => Carbon::parse($invoice->date)->format('Y-m-d'),
                'type' => 'فاتورة',
                'subtype' => $invoice->type,
                'amount' => $invoice->total_after_discount,
                'ref' => $invoice->invoice_number,
            ];
        }
        foreach ($expenses as $expense) {
            $rows[] = [
                'date' => Carbon::parse($expense->expense_date)->format('Y-m-d'),
                'type' => 'مصروف',
                'subtype' => $expense->type ?? 'صرف',
                'amount' => $expense->amount,
                'ref' => $expense->id,
            ];
        }
        // ترتيب حسب التاريخ تنازلياً
        usort($rows, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        // تجميع حسب اليوم (سيكون يوم واحد فقط)
        $grouped = collect($rows)->groupBy('date');
        return view('dashboard.reports.daily', compact('grouped'));
    }
}
