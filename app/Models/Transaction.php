<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'order_id',
        'gateway_type',
        'amount',
        'reference_code',
        'authority',
        'status',
        'payed_at',
    ];

    protected static function booted()
    {

        static::creating(function ($transaction) {
            Cache::forget('dashboardDetails');
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
