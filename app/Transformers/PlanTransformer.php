<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Enums\PlanTypeEnum;
use App\Models\Plan;
use League\Fractal\TransformerAbstract;
use App\Traits\ConvertDateTime;
use App\Traits\AmountAfterModelRebate;
use Illuminate\Support\Carbon;

class PlanTransformer extends TransformerAbstract
{
    use ConvertDateTime;
    use AmountAfterModelRebate;
    protected array $availableIncludes = [
        'users',
    ];

    public function transform(Plan $plan)
    {
        return [
            'id' => $plan->id,
            'title' => $plan->title,
            'description' => $plan->description,
            'amount' => (int)$plan->amount,
            'rebate' => (int) $plan->rebate,
            'percentage' => $plan->percentage,
            'daily_download_limit_count' => $plan->daily_download_limit_count,
            'daily_free_download_limit_count' => $plan->daily_free_download_limit_count,
            'amount_after_rebate' => (int) $this->calculateRebate($plan),
            'bought_at' => $this->shamsiDate(optional($plan->pivot)->bought_at),
            'activation_at' => $this->shamsiDate(optional($plan->pivot)->activation_at),
            'expired_at' => $this->shamsiDate(optional($plan->pivot)->expired_at),
            'has_been_expired' =>  $this->hasBeenExpired(optional($plan->pivot)->expired_at),
            'type' => $plan->type,
            'type_desc' => PlanTypeEnum::getDescription(PlanTypeEnum::getKey($plan->type)),
        ];
    }

    public function IncludeUsers(Plan $plan)
    {
        return $this->collection($plan->users, new UserTransformer());
    }


    private function hasBeenExpired(?string $datetime)
    {

        if (!$datetime) {
            return;
        }
        $expiredDate = Carbon::parse($datetime);
        return $expiredDate->lessThan(now());
    }
}
