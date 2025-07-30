<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBoxTransaction extends Model
{
    protected $fillable = [
        'cash_box_id',
        'invoice_id',
        'expense_id',
        'amount',
        'type',
        'currency',
        'exchange_rate',
        'description'
    ];

    /**
     * العلاقة مع الصندوق
     */
    public function cashBox(): BelongsTo
    {
        return $this->belongsTo(CashBox::class);
    }

    /**
     * العلاقة مع الفاتورة
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * العلاقة مع المصروف
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
