<?php

namespace App\Traits;

use App\Models\User;
use App\Observers\UserObserver;

trait ObservUser
{
    public static function bootObservUser(): void
    {
        User::observe(UserObserver::class);
    }
}
