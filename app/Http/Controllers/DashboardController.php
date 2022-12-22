<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatusEnum;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Transformers\CommentTransformer;
use App\Transformers\OrderTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{

    public function details()
    {
        /**
         * @get('/api/panel/dashboard/details')
         * @name('panel.dashboard.details')
         * @middlewares('api', 'auth:sanctum')
         */
        $details = Cache::remember('dashboardDetails', 86400, function () {
            $usersCount = User::query()->count();
            $ordersCount = Order::query()->count();
            $payOkTransactionsAmount = Transaction::query()->where('status', TransactionStatusEnum::Payed)->get()->sum('amount');
            $payFailedTransactionAmount = Transaction::query()->where('status', TransactionStatusEnum::Canceled)->get()->sum('amount');

            return [
                'usersCount' => $usersCount,
                'ordersCount' => $ordersCount,
                'payOkTransactionsAmount' => $payOkTransactionsAmount,
                'payFailedTransactionAmount' => $payFailedTransactionAmount,
            ];
        });

        return apiResponse()->content(compact('details'))->success();
    }
    public function latestOrders()
    {
        /**
         * @get('/api/panel/dashboard/latest-orders')
         * @name('panel.dashboard.latest.orders')
         * @middlewares('api', 'auth:sanctum')
         */
        $orders = Cache::remember('latest-orders', 86400, function () {
            return Order::query()->latest()->whereBetween('created_at', [Carbon::today()->subMonths(2), Carbon::today()])->get();
        });
        return fractal()
            ->collection($orders)
            ->transformWith(OrderTransformer::class)
            ->withResourceName('orders')
            ->respond();
    }
    public function latestComments()
    {
        /**
         * @get('/api/panel/dashboard/latest-comments')
         * @name('panel.dashboard.latest.comments')
         * @middlewares('api', 'auth:sanctum')
         */
        $comments = Cache::remember('latest-comments', 86400, function () {
            return Comment::query()->with('user')->latest()
                ->whereBetween('created_at', [Carbon::today()->subMonths(2), Carbon::today()])
                ->get();
        });
        return fractal()
            ->collection($comments)
            ->transformWith(CommentTransformer::class)
            ->withResourceName('comments')
            ->respond();
    }
}
