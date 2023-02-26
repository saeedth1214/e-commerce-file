<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    public $afterCommit = true;
    
    public function created(Order $order)
    {
        Cache::forget('latest-orders');
        Cache::forget('dashboardDetails');
    }
}
