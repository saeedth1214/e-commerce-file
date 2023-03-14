<?php

namespace App\Traits;

use App\Models\Order;
use App\Observers\OrderObserver;

trait ObservOrder
{
    public static function bootObservOrder(): void
    {
        Order::observe(OrderObserver::class);
    }
}
