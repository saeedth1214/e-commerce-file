<?php

namespace App\Transformers;

use App\Enums\OrderTypeEnum;
use App\Models\Order;
use App\Traits\ConvertDateTime;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    use ConvertDateTime;
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        'voucher',
        'user',
        'files',
        'transactions'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'id' => $order->id,
            'total_items' => $order->total_items,
            'total_amount' => $order->total_amount,
            'rebate' => optional($order->voucher)->rebate,
            'percentage' => optional($order->voucher)->percentage,
            'status_dec' => OrderTypeEnum::getDescription(OrderTypeEnum::getKey($order->status)),
            'status' => $order->status,
            'created_at' => $this->shamsiDate($order->created_at),
        ];
    }

    public function IncludeUser(Order $order)
    {
        return $this->item($order->user, new UserTransformer);
    }
    public function IncludeVoucher(Order $order)
    {
        if (!$order->voucher) {
            return $this->null();
        }
        return $this->item($order->voucher, new VoucherTransformer);
    }
    public function IncludeFiles(Order $order)
    {
        return $this->collection($order->files, new FileTransformer);
    }

    public function IncludeTransactions(Order $order)
    {

        return $this->collection($order->transactions(), new TransactionTransformer);
    }
}
