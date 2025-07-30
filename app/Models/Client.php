<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'address', 'phone', 'country', 'city'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // علاقة: عميل لديه عدة فواتير
    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'client_id');
    }
}
