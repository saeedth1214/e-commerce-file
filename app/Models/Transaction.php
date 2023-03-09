<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeFindByUuid(Builder $query, $uuid)
    {
        return $query->whereHas('order', fn ($query) => $query->where('user_id', auth()->id()))->where('uuid', $uuid);
    }
}
