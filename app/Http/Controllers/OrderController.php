<?php

namespace App\Http\Controllers;

use App\Enums\AccessTypeEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Http\Requests\StoreOrderRequest;
use App\Models\File;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\AmountAfterModelRebate;
use App\Traits\FilterQueryBuilder;
use App\Transformers\OrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Spatie\QueryBuilder\AllowedFilter;

class OrderController extends Controller
{
    use AmountAfterModelRebate;
    use FilterQueryBuilder;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /**
         * @get('/api/panel/orders')
         * @name('panel.orders.index')
         * @middlewares('api', 'auth:sanctum')
         */
        $per_page = request()->input('per_page', 15);

        $orders = $this->queryBuilder(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('plan_id'),
                AllowedFilter::exact('voucher_id'),
                AllowedFilter::exact('status'),

            ])
            ->paginate($per_page);

        foreach (OrderTypeEnum::asArray() as $key => $value) {
            $types[] = ['value' => $value, 'name' => OrderTypeEnum::getDescription($key)];
        }

        return fractal()
            ->collection($orders)
            ->withResourceName('vouchers')
            ->paginateWith(new IlluminatePaginatorAdapter($orders))
            ->transformWith(OrderTransformer::class)
            ->addMeta(['types' => $types])
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        /**
         * @post('/api/panel/orders')
         * @name('panel.orders.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $fileIds = $request->input('files');
        $files = File::query()->whereIn('id', $fileIds)->get();

        $orderData = $this->makeOrder($files, $request->voucher_id);
        $order = Order::query()->create($orderData);

        $invoice = $this->makeInvoice($orderData['total_amount']);
        $uuid = $invoice->getUuid();

        $transactionId = $invoice->getTransactionId();
        $transactionData = $this->makeTransaction($uuid, $order->id, $orderData['total_amount']);

        $transaction = $order->transactions()->create($transactionData);

        // create cache
        Cache::put($uuid, [
            'fileIds' => $fileIds,
            'userId' => auth()->id(),
            'voucherId' => $request->voucher_id
        ], 120);

        return Payment::callbackUrl(config('payment-urls.order.callBackUrl') . "?uuid={$uuid}")->purchase($invoice, function ($driver, $transactionId) use ($transaction) {
            $transaction->update([
                'authority' => $transactionId
            ]);
        })->pay();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        /**
         * @get('/api/panel/orders/{order}')
         * @name('panel.orders.show')
         * @middlewares('api', 'auth:sanctum')
         */
        return fractal()
            ->item($order)
            ->transformWith(OrderTransformer::class)
            ->withResourceName('Orders')
            ->respond();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        /**
         * @delete('/api/panel/orders/{order}')
         * @name('panel.orders.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $order->delete();
        return apiResponse()->empty();
    }

    public function verifyTransaction(Request $request)
    {
        try {
            if ($request->filled('uuid') && Cache::has($request->uuid)) {
                $cacheData = Cache::pull($request->uuid);
                $user = User::query()->find($cacheData['userId']);
                $order = $user->orders()->latest()->first();
                $transaction = Transaction::query()->where('uuid', $request->uuid)->first();
                $receipt = Payment::amount($transaction->amount)->transactionId($transaction->authority)->verify();
                DB::transaction(function () use ($order, $transaction, $user, $receipt, $cacheData) {

                    $order->update([
                        'status' => OrderTypeEnum::PAY_OK
                    ]);
                    $transaction->update([
                        'status' => TransactionStatusEnum::Payed,
                        'reference_code' => $receipt->getReferenceId(),
                        'payed_at' => now()
                    ]);
                    $files = File::query()->whereIn('id', $cacheData['fileIds'])->get();

                    foreach ($files as $file) {
                        $order->files()->attach($file, [
                            'total_amount' => $this->calculateRebate($file)
                        ]);
                        $user->files()->attach($file, [
                            'total_amount' =>  $cacheData['voucherId']
                                ?
                                $this->calculateVoucherCode($cacheData['voucherId'], $this->calculateRebate($file))
                                :
                                $this->calculateRebate($file),
                            'voucher_id' => $cacheData['voucherId'],
                            'bought_at' => now(),
                            'access' => AccessTypeEnum::Payment
                        ]);
                    }
                });
            }

            return redirect(config('payment-urls.order.afterCallback') . "?uuid={$request->uuid}");
        } catch (InvalidPaymentException $exception) {
            $order->update([
                'status' => OrderTypeEnum::PAY_FAILED
            ]);
            $transaction->update([
                'status' => TransactionStatusEnum::Canceled
            ]);
            return redirect(config('payment-urls.order.afterCallback') . "?uuid={$request->uuid}");
        }
    }


    private function makeOrder($files, $voucherId)
    {
        $total_amount = 0;
        $orderData['user_id'] = auth()->id();
        $orderData['total_items'] = $files->count();
        $orderData['voucher_id'] = $voucherId;
        $orderData['status'] = OrderTypeEnum::PENDING;
        $orderData['bought_at'] = now();

        $total_amount = $files->sum(
            fn ($file)
            =>
            request()->has('voucher_id')
                ?
                $this->calculateVoucherCode($voucherId, $this->calculateRebate($file))
                :
                $this->calculateRebate($file)
        );

        $orderData['total_amount'] = $total_amount;

        return $orderData;
    }

    private function makeTransaction($uuid, $orderId, $amount)
    {

        $transactionData['uuid'] = $uuid;
        $transactionData['order_id'] = $orderId;
        $transactionData['amount'] = $amount;
        $transactionData['status'] = TransactionStatusEnum::Paying;
        return $transactionData;
    }

    private function makeInvoice($amount)
    {

        $invoice = new Invoice;
        $invoice->amount($amount);
        return $invoice;
    }
}
