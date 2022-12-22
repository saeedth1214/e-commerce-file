<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenerateToken
{
    protected function generateToken()
    {
        return Str::random(16);
    }
}
