<?php

namespace App\Http\Controllers;

use App\Enums\AccessTypeEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Http\Requests\StoreOrderRequest;
use App\Models\File;
use App\Models\Order;
use App\Models\Voucher;
use App\Traits\AmountAfterModelRebate;
use App\Traits\FilterQueryBuilder;
use App\Transformers\OrderTransformer;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Shetabit\Multipay\Invoice;
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
        $orderData = [];
        $total_amount = 0;
        $orderData['user_id'] = auth()->id();
        $orderData['voucher_id'] = request()->input('voucher_id') ?? null;
        $orderData['status'] = OrderTypeEnum::PENDING;
        $orderData['bought_at'] = now();

        $total_amount = $files->sum(
            fn ($file)
            =>
            request()->has('voucher_id')
                ?
                $this->calculateVoucherCode(request()->input('voucher_id'), $this->calculateRebate($file))
                :
                $this->calculateRebate($file)
        );

        $orderData['total_amount'] = $total_amount;
        $order = Order::query()->create($orderData);


        $invoice = new Invoice;
        $invoice->amount($total_amount);
        $invoice->detail(['subscription' => 'خرید اشتراک']);

        $uuid = $invoice->getUuid();
        $transactionId = $invoice->getTransactionId();

        $transactionData = [];

        $transactionData['uuid'] = $uuid;
        $transactionData['order_id'] = $order->id;
        $transactionData['amount'] = $total_amount;
        $transactionData['status'] = TransactionStatusEnum::Paying;
        $transaction = $order->transactions()->create($transactionData);

        // transaction ok 

        DB::transaction(function () use ($order, $transaction, $files) {

            $order->update([
                'status' => OrderTypeEnum::PAY_OK
            ]);
            $transaction->update([
                'status' => TransactionStatusEnum::Payed
            ]);

            foreach ($files as $file) {
                $order->files()->attach($file, ['total_amount' => $this->calculateRebate($file)]);
            }
        });
        return apiResponse()->empty();
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
}
