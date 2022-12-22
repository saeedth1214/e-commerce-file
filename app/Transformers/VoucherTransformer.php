<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Voucher;
use App\Traits\ConvertDateTime;
use App\Enums\VoucherTypeEnum;

class VoucherTransformer extends TransformerAbstract
{
    use ConvertDateTime;

    protected array $availableIncludes = [
        'user'
    ];
    public function transform(Voucher $voucher)
    {
        return [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'rebate' => $voucher->rebate,
            'percentage' => $voucher->percentage,
            'status' => $voucher->status,
            'type_id' => $voucher->type,
            'type_text' => VoucherTypeEnum::getDescription(VoucherTypeEnum::getKey($voucher->type)),
            'expired_at' => $this->ConvertToMilai($voucher->expired_at),
            'created_at' => $this->ConvertToMilai($voucher->created_at),
            'number_authorize_use' => optional($voucher->pivot)->number_authorize_use,
            'number_times_use' => optional($voucher->pivot)->number_times_use
        ];
    }

    public function IncludeUser(Voucher $voucher)
    {
        return $this->collection($voucher->users, new UserTransformer);
    }
}
