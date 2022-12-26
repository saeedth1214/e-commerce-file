<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;
use App\Traits\ConvertDateTime;
use App\Enums\UserRoleEnum;

class UserTransformer extends TransformerAbstract
{
    use ConvertDateTime;

    protected array $availableIncludes = [
        'plans',
        'comments',
        'transactions',
        'files',
        'vouchers',
        'orders',
        'activePlan'
    ];
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'media_url' => $this->getMediaUrl($user),
            'role' => UserRoleEnum::getDescription(UserRoleEnum::getKey($user->role)),
            'role_id' => $user->role,
            'email_verified_at' =>  $this->convertToMilai($user->email_verified_at),
            'mobile_verified_at' => $this->convertToMilai($user->mobile_verified_at),
            'created_at' => $this->convertToMilai($user->created_at),
        ];
    }

    private function getMediaUrl(User $user)
    {
        return $user->getFirstMediaUrl('avatar-image');
    }
    public function IncludePlans(User $user)
    {
        return $this->collection($user->plans, new PlanTransformer());
    }

    public function IncludeFiles(User $user)
    {
        return $this->collection($user->files, new FileTransformer());
    }

    public function IncludeTransactions(User $user)
    {
        return $this->collection($user->transactions, new TransactionTransformer());
    }

    public function IncludeComments(User $user)
    {
        return $this->collection($user->comments, new CommentTransformer());
    }

    public function IncludeVouchers(User $user)
    {
        return $this->collection($user->vouchers, new VoucherTransformer);
    }
    public function IncludeOrders(User $user)
    {
        return $this->collection($user->orders, new OrderTransformer);
    }
    public function IncludeActivePlan(User $user)
    {
        
        $plan = $user->activePlan();
        // dd($plan);
        if (!$plan) {

            return $this->null();
        }
        return $this->item($plan, new PlanTransformer);
    }
}
