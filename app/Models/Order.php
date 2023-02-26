<?php

namespace App\Models;

use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'voucher_id',
        'total_items',
        'total_amount',
        'total_amount_after_voucher_code',
        'status',
    ];

    protected static function boot()
    {
        Order::observe(OrderObserver::class);
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
            'amount_after_voucher_code',
            'bought_at'
        ]);
    }

    public function transactions()
    {

        return $this->hasMany(Transaction::class);
    }
}
