<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingEntry extends Model
{
    protected $fillable = [
        'user_id',
        'operation_type',
        'operation_description',
        'amount',
        'is_received',
        'debtor_type',
        'debtor_id',
        'creditor_type',
        'creditor_id',
        'entry_date',
        'notes'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'amount' => 'decimal:2',
        'is_received' => 'boolean',
        'entry_date' => 'datetime'
    ];

    /**
     * علاقة القيد المحاسبي مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the source model (Invoice or Expense)
     */
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Create an accounting entry for an invoice
     */
    public static function createFromInvoice(Invoice $invoice): self
    {
        $entry = new self();

        // تحديد نوع العملية والوصف حسب نوع الفاتورة
        switch ($invoice->type) {
            case 'بيع':
                $entry->operation_type = 'قبض';
                $entry->operation_description = 'بيع';

                if ($invoice->is_received) {
                    $entry->debtor_type = 'صندوقي';
                    $entry->creditor_type = 'صندوق العميل';
                } else {
                    $entry->debtor_type = 'صندوق العميل';
                    $entry->creditor_type = 'منتجات';
                }
                break;

            case 'شراء':
                $entry->operation_type = 'دفع';
                $entry->operation_description = 'شراء';

                if ($invoice->is_received) {
                    $entry->debtor_type = 'صندوق العميل';
                    $entry->creditor_type = 'صندوقي';
                } else {
                    $entry->debtor_type = 'منتجات';
                    $entry->creditor_type = 'صندوق العميل';
                }
                break;

            case 'مردودات بيع':
                $entry->operation_type = 'دفع';
                $entry->operation_description = 'مردودات بيع';

                if ($invoice->is_received) {
                    $entry->debtor_type = 'منتجات';
                    $entry->creditor_type = 'صندوقي';
                } else {
                    $entry->debtor_type = 'صندوق العميل';
                    $entry->creditor_type = 'منتجات';
                }
                break;

            case 'مردودات شراء':
                $entry->operation_type = 'قبض';
                $entry->operation_description = 'مردودات شراء';

                if ($invoice->is_received) {
                    $entry->debtor_type = 'صندوقي';
                    $entry->creditor_type = 'صندوق العميل';
                } else {
                    $entry->debtor_type = 'صندوقي';
                    $entry->creditor_type = 'منتجات';
                }
                break;

            default:
                $entry->operation_type = 'قبض';
                $entry->operation_description = 'عملية أخرى';
                break;
        }

        // التأكد من وجود المبلغ
        $amount = $invoice->total_after_discount ?? $invoice->total ?? 0;
        $entry->amount = $amount;

        // تعيين باقي الحقول
        $entry->is_received = $invoice->status === 'مقبوضة';
        $entry->entry_date = $invoice->created_at ?? now();

        // ربط القيد بالفاتورة والمستخدم
        $entry->sourceable()->associate($invoice);
        $entry->user_id = $invoice->user_id;
        $entry->save();

        return $entry;
    }

    /**
     * Create an accounting entry for an expense
     */
    public static function createFromExpense(Expense $expense): self
    {
        $entry = new self();

        $entry->operation_type = 'دفع';
        $entry->operation_description = 'مصروف';
        $entry->amount = $expense->amount;
        $entry->is_received = true; // المصروف دائماً مقبوض
        $entry->debtor_type = null; // لا يوجد مدين للمصروف
        $entry->creditor_type = 'صندوقي';
        $entry->entry_date = $expense->created_at;

        $entry->sourceable()->associate($expense);
        $entry->user_id = $expense->user_id;
        $entry->save();

        return $entry;
    }

    /**
     * Scope a query to only include entries within a date range
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('entry_date', [$start, $end]);
    }

    /**
     * Scope a query to only include received entries
     */
    public function scopeReceived($query)
    {
        return $query->where('is_received', true);
    }

    /**
     * Scope a query to only include not received entries
     */
    public function scopeNotReceived($query)
    {
        return $query->where('is_received', false);
    }

    /**
     * Get the formatted amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . config('app.currency', 'USD');
    }
}
