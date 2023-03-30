<?php

use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Route;
use App\Mail\VerificationCode;

Route::get('/mailable', function () {
    return new VerificationCode('456598');
});

Route::get('/forget', function () {
    return new ResetPassword('asd5asdasd44ad54sd545', 'ss+900@g.com');
});
