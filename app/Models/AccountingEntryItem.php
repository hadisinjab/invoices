<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingEntryItem extends Model
{
    protected $fillable = [
        'accounting_entry_id',
        'account_type',
        'account_id',
        'debit',
        'credit',
        'quantity',
        'unit_price'
    ];

    /**
     * العلاقة مع القيد المحاسبي
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(AccountingEntry::class, 'accounting_entry_id');
    }

    /**
     * الحصول على الحساب المرتبط
     */
    public function getAccount()
    {
        return match($this->account_type) {
            'cash_box' => CashBox::find($this->account_id),
            'client' => Client::find($this->account_id),
            'user' => User::find($this->account_id),
            'product' => Product::find($this->account_id),
            default => null
        };
    }

    /**
     * حساب المبلغ الإجمالي للمنتج
     */
    public function getTotalAmount(): float
    {
        if ($this->account_type === 'product' && $this->quantity && $this->unit_price) {
            return $this->quantity * $this->unit_price;
        }
        return $this->debit ?: $this->credit;
    }
}
