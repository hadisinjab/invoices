<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Expense extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'expense_date',
        'comment',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the accounting entry for this expense
     */
    public function accountingEntry(): MorphOne
    {
        return $this->morphOne(AccountingEntry::class, 'sourceable');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // إنشاء قيد محاسبي عند إنشاء المصروف
        static::created(function ($expense) {
            AccountingEntry::createFromExpense($expense);
        });

        // تحديث القيد المحاسبي عند تحديث المصروف
        static::updated(function ($expense) {
            if ($expense->accountingEntry) {
                $expense->accountingEntry->delete();
            }
            AccountingEntry::createFromExpense($expense);
        });

        // حذف القيد المحاسبي عند حذف المصروف
        static::deleted(function ($expense) {
            if ($expense->accountingEntry) {
                $expense->accountingEntry->delete();
            }
        });
    }
}
