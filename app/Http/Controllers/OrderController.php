<?php

namespace App\Http\Controllers;

use App\Enums\OrderTypeEnum;
use App\Filters\FilterBySpecialValue;
use App\Http\Requests\StoreOrderRequest;
use App\Models\File;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Voucher;
use App\Traits\AmountAfterModelRebate;
use App\Traits\FilterQueryBuilder;
use App\Transformers\OrderTransformer;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
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
                AllowedFilter::custom('total_items', new FilterBySpecialValue),
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
        $order_data = [];
        $order_data['user_id'] = auth()->id();
        $total_items = 0;
        $total_amount = 0;
        $attachments = [];
        $total_amount_after_rebate_code = 0;

        if ($request->has('plan_id')) {
            $order_data['plan_id'] = $request->input('plan_id');
            $plan = Plan::query()->find($request->input('plan_id'));
            $total_amount += $this->calculateRebate($plan);
            $total_items++;
        }

        if ($request->has('files')) {
            $fileIds = $request->input('files');
            $files = File::query()->whereIn('id', $fileIds)->get();
            foreach ($files as $file) {
                $total_amount += $this->calculateRebate($file);
                $total_items++;
            }
            $callback = fn ($pivot) => [
                $pivot->id => [
                    'amount' => $pivot->amount,
                    'rebate' => $pivot->rebate,
                    'amount_after_rebate' => $this->calculateRebate($pivot)
                ]
            ];
            $attachments = $files->mapToGroups($callback)->map(fn ($group) => $group->first());
        }
        if ($request->has('voucher_id')) {
            $order_data['voucher_id'] = $request->input('voucher_id');
            $voucher = Voucher::query()->find($request->input('voucher_id'));
            $total_amount_after_rebate_code = $this->calculateVoucherCode($voucher, $total_amount);
            $order_data['total_amount_after_rebate_code'] = $total_amount_after_rebate_code;
        } else {
            $order_data['total_amount_after_rebate_code'] = $total_amount;
        }
        $order_data['total_amount'] = $total_amount;
        $order_data['total_items'] = $total_items;
        DB::transaction(function () use ($order_data, $attachments) {
            $order = Order::query()->create($order_data);
            $order->files()->sync($attachments);
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
