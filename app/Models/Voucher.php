<?php

namespace App\Models;

use App\Enums\VoucherTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'code',
        'rebate',
        'percentage',
        'expired_at',
        'status',
        'type',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_vouchers', 'voucher_id', 'user_id')
            ->withPivot([
                'number_authorize_use',
                'number_times_use',
                'last_date_of_use'
            ]);
    }

    public static function findByCode(string $code, $userId)
    {
        return static::with(['users' => fn ($query) => $query->select('number_times_use')])->where([
            ['expired_at', '>=', now()],
            ['status', 1]
        ])->whereIn('type', [VoucherTypeEnum::IS_GENERAL_FOR_PRODUCT, VoucherTypeEnum::IS_GENERAL_FOR_USER])
            ->orWhereHas('users', function ($query) use ($userId) {
                return $query->where('user_id', $userId)->whereRaw('number_authorize_use - number_times_use > 0');
            })->where('code', $code)
            ->first();
    }

    public function orders()
    {

        return $this->hasMany(Order::class);
    }
}
