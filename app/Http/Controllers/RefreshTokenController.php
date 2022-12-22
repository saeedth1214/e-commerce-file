<?php

namespace App\Http\Controllers;
class RefreshTokenController extends Controller
{
    public function refreshToken()
    {
        /**
         * @post('/api/auth/refresh')
         * @name('auth.user.token.refresh')
         * @middlewares('api', 'auth:sanctum')
         */
        $user = auth('sanctum')->user();
        $user->currentAccessToken()->delete();
        $token = $user->createToken(request()->userAgent())->plainTextToken;
        return apiResponse()->content([
            'status' => 200,
            'token' => $token,
        ])->success();
    }
}
