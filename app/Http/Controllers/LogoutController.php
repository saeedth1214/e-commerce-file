<?php

namespace App\Http\Controllers;

class LogoutController extends Controller
{
    public function logout()
    {
        /**
         * @post('/api/auth/logout')
         * @name('auth.user.logout')
         * @middlewares('api', 'auth:sanctum')
         */
        $user = auth('sanctum')->user();
        $user->currentAccessToken()->delete();
        return apiResponse()->content([
            'status' => 'SUCCESS'
        ])->success();
    }
}
