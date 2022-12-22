<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidationServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Validator::extend('mobile', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[9][0|1|2|3|4|9][0-9]{8}$/', $value);
        });

        Validator::extend('username', function ($attribute, $value, $parameters, $validator) {
            $email = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $value);

            $mobile = preg_match('/^[9][0|1|2|3|4|9][0-9]{8}$/', $value);

            return $email || $mobile;
        });
    }
}
