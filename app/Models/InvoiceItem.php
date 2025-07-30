<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    // علاقة: عنصر الفاتورة يتبع فاتورة واحدة
    public function invoice()
    {
        return $this->belongsTo(\App\Models\Invoice::class, 'invoice_id');
    }

    // علاقة: عنصر الفاتورة يتبع منتج واحد
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    protected $fillable = [
        'invoice_id',
        'name',
        'quantity',
        'price',
        'product_id',
        'price_received',
        'total',
        'total_received',
    ];
}
