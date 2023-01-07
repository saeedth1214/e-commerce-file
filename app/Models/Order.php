<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'voucher_id',
        'total_items',
        'total_amount',
        'total_amount_after_rebate_code',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            Cache::forget('latest-orders');
            Cache::forget('dashboardDetails');
        });
    }

    public function user()
    {

        return $this->belongsTo(User::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'order_has_files')->withPivot([
            'amount',
            'rebate',
            'amount_after_rebate'
        ]);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function transactions()
    {

        return $this->hasMany(Transaction::class);
    }
}
