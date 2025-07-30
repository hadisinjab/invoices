<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'type',
        'status',
        'client_id',
        'date',
        'total',
        'discount',
        'total_after_discount',
        'received_currency',
        'exchange_rate',
        'notes',
        'user_id', // تمت الإضافة هنا
    ];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_after_discount' => 'decimal:2',
        'exchange_rate' => 'decimal:4'
    ];

    protected static function boot()
    {
        parent::boot();

        // قبل الحفظ: حساب الإجمالي بعد الحسم
        static::saving(function ($invoice) {
            if (!$invoice->total_after_discount) {
                $invoice->total_after_discount = $invoice->total - ($invoice->discount ?? 0);
            }
        });

        // بعد الإنشاء: إنشاء قيد محاسبي
        static::created(function ($invoice) {
            try {
                AccountingEntry::createFromInvoice($invoice);
            } catch (\Exception $e) {
                Log::error('Error creating accounting entry: ' . $e->getMessage());
                throw $e;
            }
        });

        // بعد التحديث: تحديث القيد المحاسبي
        static::updated(function ($invoice) {
            try {
                if ($invoice->accountingEntry) {
                    $invoice->accountingEntry->delete();
                }
                AccountingEntry::createFromInvoice($invoice);
            } catch (\Exception $e) {
                Log::error('Error updating accounting entry: ' . $e->getMessage());
                throw $e;
            }
        });

        // قبل الحذف: حذف القيد المحاسبي
        static::deleting(function ($invoice) {
            try {
                if ($invoice->accountingEntry) {
                    $invoice->accountingEntry->delete();
                }
            } catch (\Exception $e) {
                Log::error('Error deleting accounting entry: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function accountingEntry(): MorphOne
    {
        return $this->morphOne(AccountingEntry::class, 'sourceable');
    }

    public function getIsReceivedAttribute(): bool
    {
        return $this->status === 'مقبوضة';
    }
}
