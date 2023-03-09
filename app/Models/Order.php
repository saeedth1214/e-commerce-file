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
        'total_amount',
        'total_items',
        'bought_at',
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function plans()
    {
        return $this->morphedByMany(Plan::class, 'orderable')->withPivot(['total_amount']);
    }
    public function files()
    {
        return $this->morphedByMany(File::class, 'orderable')->withPivot(['total_amount']);
    }
}
