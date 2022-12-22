<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;

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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
