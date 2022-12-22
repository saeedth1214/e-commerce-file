<?php

namespace App\Traits;

use App\Transformers\UserTransformer;

trait HasToken
{
    protected function tokenData($token, $user)
    {
        return [
            'token' => $token,
            'type' => 'bearer',
            'user' => fractal()->item($user,UserTransformer::class)->withResourceName('users')
        ];
    }
}
