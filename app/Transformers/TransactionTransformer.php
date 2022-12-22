<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Models\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'user',
        'order'
    ];
    public function transform(Transaction $transaction)

    {
        return [
            'uuid' => $transaction->uuid,
            'order_id' => $transaction->order_id,
            'gateway_type' => $transaction->gateway_type,
            'amount' => $transaction->amount,
            'reference_code' => $transaction->reference_code,
            'authority' => $transaction->authority,
            'status' => $transaction->status,
            'payed_at' => $transaction->payed_at,
        ];
    }

    public function IncludeUser(Transaction $transaction)
    {
        if (!$transaction->user) {
            return $this->null();
        }
        return $this->item($transaction->user, new UserTransformer());
    }

    public function IncludeOrder(Transaction $transaction)
    {
        return $this->item($transaction->order, new OrderTransformer());
    }
}
