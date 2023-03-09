<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Enums\TransactionStatusEnum;
use App\Models\Transaction;
use App\Traits\ConvertDateTime;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    use ConvertDateTime;
    
    protected array $availableIncludes = [
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
            'status_desc' => TransactionStatusEnum::getDescription(TransactionStatusEnum::getKey($transaction->status)),
            'status' => $transaction->status,
            'payed_at' => $this->shamsiDate($transaction->payed_at),
        ];
    }


    public function IncludeOrder(Transaction $transaction)
    {
        return $this->item($transaction->order, new OrderTransformer());
    }
}
