<?php

namespace App\Http\Controllers;

use App\Enums\AccessTypeEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PlanTypeEnum;
use App\Enums\TransactionStatusEnum;
use App\Traits\FilterQueryBuilder;
use App\Models\Plan;
use App\Transformers\PlanTransformer;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\FilterByDateTime;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Spatie\QueryBuilder\AllowedInclude;
use App\Filters\FilterUniqueValue;
use App\Http\Requests\PurchaseSubscriptionRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class PlanController extends Controller
{
    use FilterQueryBuilder;
    private $user = null;

    public function __construct()
    {
        $this->user = auth()->user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        /**
         * @get('/api/panel/plans')
         * @name('panel.plans.index')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/plans')
         * @name('frontend..plans')
         * @middlewares('api')
         */
        $per_page = request()->input('per_page', 15);

        $plans = $this->queryBuilder(Plan::class)
            ->allowedFilters([
                'title',
                AllowedFilter::exact('percentage'),
                AllowedFilter::custom('created_at', new FilterByDateTime),
                AllowedFilter::custom('unique', new FilterUniqueValue),
                AllowedFilter::scope('user_id', 'userId')
            ])->allowedIncludes([
                AllowedInclude::count('users')
            ])
            ->paginate($per_page);

        return fractal()
            ->collection($plans)
            ->withResourceName('plans')
            ->paginateWith(new IlluminatePaginatorAdapter($plans))
            ->transformWith(PlanTransformer::class)
            ->respond();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePlanRequest $request): JsonResponse
    {
        /**
         * @post('/api/panel/plans')
         * @name('panel.plans.store')
         * @middlewares('api', 'auth:sanctum')
         */
        $planData = $request->safe()->all();
        $plan = Plan::query()->create($planData);
        return fractal()
            ->item($plan)
            ->withResourceName('plans')
            ->transformWith(new PlanTransformer())
            ->respond();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Plan $plan): JsonResponse
    {
        /**
         * @get('/api/panel/plans/{plan}')
         * @name('panel.plans.show')
         * @middlewares('api', 'auth:sanctum')
         *
         * @get('/api/frontend/plans/{plan}')
         * @name('frontend.show.plans')
         * @middlewares('api')
         */
        return fractal()
            ->item($plan)
            ->withResourceName('plans')
            ->transformWith(PlanTransformer::class)
            ->respond();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdatePlanRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        /**
         * @methods('PUT', PATCH')
         * @uri('/api/panel/plans/{plan}')
         * @name('panel.plans.update')
         * @middlewares('api', 'auth:sanctum')
         */
        $planData = $request->safe()->all();
        $plan->update($planData);
        return apiResponse()->empty();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Plan $plan): JsonResponse
    {
        /**
         * @delete('/api/panel/plans/{plan}')
         * @name('panel.plans.destroy')
         * @middlewares('api', 'auth:sanctum')
         */
        $plan->delete();

        return apiResponse()->empty();
    }

    public function buySubscription(PurchaseSubscriptionRequest $request, Plan $plan)
    {

        //create order

        $orderData = $this->makeOrder();

        $order = Order::query()->create($orderData);

        // create invoice
        $invoice = $this->makeInvoice($request->amount);
        $uuid = $invoice->getUuid();

        // create transaction
        $transactionData = $this->makeTransaction($uuid, $order->id);
        $transaction = $order->transactions()->create($transactionData);

        // create cache
        Cache::put($uuid, [
            'planId' => $plan->id,
            'userId' => auth()->id()
        ], 120);

        return Payment::callbackUrl(config('payment-urls.plan.callBackUrl') . "?uuid={$uuid}")->purchase($invoice, function ($driver, $transactionId) use ($transaction) {
            $transaction->update([
                'authority' => $transactionId
            ]);
        })->pay();
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
                DB::transaction(function () use ($transaction, $receipt, $order, $user, $cacheData) {
                    $plan = Plan::query()->find($cacheData['planId']);
                    $order->update([
                        'status' => OrderTypeEnum::PAY_OK
                    ]);
                    $transaction->update([
                        'status' => TransactionStatusEnum::Payed,
                        'reference_code' => $receipt->getReferenceId(),
                        'payed_at' => now()
                    ]);
                    $order->plans()->attach($plan, ['total_amount' => $transaction->amount]);
                    $user->plans()->attach($plan, [
                        'amount' => $transaction->amount,
                        'activation_at' => now(),
                        'expired_at' => now()->addDays(PlanTypeEnum::convertToDays($plan->type)),
                        'access' => AccessTypeEnum::Payment,
                        'bought_at' => now(),
                    ]);
                });
            }

            return redirect(config('payment-urls.plan.afterCallback') . "?uuid={$request->uuid}");
        } catch (InvalidPaymentException $exception) {
            $order->update([
                'status' => OrderTypeEnum::PAY_FAILED
            ]);
            $transaction->update([
                'status' => TransactionStatusEnum::Canceled
            ]);
            return redirect(config('payment-urls.plan.afterCallback') . "?uuid={$request->uuid}");
        }
    }

    private function makeOrder()
    {
        $orderData = [];
        $orderData['user_id'] = auth()->id();
        $orderData['total_amount'] = request()->input('amount', 0);
        $orderData['total_items'] = 1;
        $orderData['status'] = OrderTypeEnum::PENDING;
        $orderData['bought_at'] = now();

        return $orderData;
    }

    private function makeTransaction($uuid, $orderId)
    {
        $transactionData = [];
        $transactionData['uuid'] = $uuid;
        $transactionData['order_id'] = $orderId;
        $transactionData['amount'] = request()->input('amount', 0);
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
