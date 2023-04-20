<?php

use Illuminate\Support\Facades\Route;
use App\Mail\VerificationCode;
use Illuminate\Support\Facades\Artisan;

Route::get('/mailable', function () {
    return new VerificationCode('456598');
});
Route::get('/refresh-link', function () {
    Artisan::call('refresh:link');
});
