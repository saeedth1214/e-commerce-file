<?php

namespace App\Traits;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Model;

trait AmountAfterModelRebate
{
    public function calculateRebate(Model $model)
    {
        if (!$model->rebate) {
            return $model->amount;
        }
        if ($model->percentage) {
            return $this->rebateWithPercentageType($model);
        }
        return $this->rebateWithNumberType($model);
    }

    public function calculateVoucherCode(Voucher $voucher, $total_amount)
    {
        if ($voucher->percentage) {
            return (1 - $voucher->rebate / 100) * $total_amount;
        }

        return $total_amount - $voucher->rebate;
    }
    private function rebateWithPercentageType(Model $model)
    {
        return (1 - $model->rebate / 100) * $model->amount;
    }

    private function rebateWithNumberType(Model $model)
    {
        return $model->amount - $model->rebate;
    }
}
