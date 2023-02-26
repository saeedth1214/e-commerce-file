<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{

    public function created(User $user)
    {
        Cache::forget('dashboardDetails');
    }

    public function deleted(User $user)
    {
        Cache::forget('dashboardDetails');
    }

    public function forceDeleted(User $user)
    {
        Cache::forget('dashboardDetails');
    }
}
