<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

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
        'acceptedMainComments',
        'mainComments'
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
            'activation_days' => $plan->activation_days,
            'amount_after_rebate' => (int) $this->calculateRebate($plan),
            'amount_after_rebate_code' => (int)optional($plan->pivot)->amount,
            'bought_at' => $this->convertToMilai(optional($plan->pivot)->bought_at),
            'activation_at' => $this->convertToMilai(optional($plan->pivot)->activation_at),
            'expired_at' => $this->convertToMilai(optional($plan->pivot)->expired_at),
            'has_been_expired' =>  $this->hasBeenExpired(optional($plan->pivot)->expired_at),
            'media_url' => $this->getMediaUrl($plan),
            'usersCount' => $plan->users_count ?? $plan->users->count()
        ];
    }
    private function getMediaUrl(Plan $plan)
    {
        return $plan->getFirstMediaUrl('plan-image');
    }
    public function IncludeUsers(Plan $plan)
    {
        return $this->collection($plan->users, new UserTransformer());
    }

    public function IncludeAcceptedMainComments(Plan $plan)
    {
        return $this->collection($plan->acceptedMainComments, new CommentTransformer());
    }
    public function IncludeMainComments(Plan $plan)
    {
        return $this->collection($plan->mainComments, new CommentTransformer());
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
