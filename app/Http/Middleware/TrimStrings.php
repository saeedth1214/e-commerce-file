<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Clean the given value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    protected function cleanValue($key, $value)
    {
        if (!is_string($value)) {
            return parent::cleanValue($key, $value);
        }

        $isMobile = preg_match('/^(\+98|0)?9\d{9}$/', $value);

        $mobileInputs = [
            'mobile',
            'username',
            'user.mobile',
        ];

        if (in_array($key, $mobileInputs, true) && $isMobile) {
            return $this->transform($key, preg_replace('/^(\+98|0)?9(\d{9})$/', '9$2', $value));
        }

        // if ($key === 'content') {
        //     return $this->transform($key, strip_tags($value));
        // }

        return parent::cleanValue($key, $value);
    }
}
