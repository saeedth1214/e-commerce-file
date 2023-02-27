<?php

namespace App\Models;

use App\Traits\ObservOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes, ObservOrder;

    protected $fillable = [
        'user_id',
        'voucher_id',
        'total_items',
        'total_amount',
        'total_amount_after_voucher_code',
        'status',
    ];

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
