<?php

use Illuminate\Support\Facades\Route;
use App\Mail\VerificationCode;

Route::get('/mailable', function () {
    return new VerificationCode('456598');
});
