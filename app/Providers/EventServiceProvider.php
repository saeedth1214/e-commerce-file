<?php

namespace App\Providers;

use App\Events\DailyFileDownloadEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SendVerificationCodeEvent;
use App\Listeners\DailyFileDownloadListener;
use App\Listeners\SendVerificationCodeListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        SendVerificationCodeEvent::class => [
            SendVerificationCodeListener::class
        ],

        DailyFileDownloadEvent::class => [
            DailyFileDownloadListener::class
        ],
        // MediaHasBeenAdded::class => [
        //     MediaOptimizerListener::class
        // ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
