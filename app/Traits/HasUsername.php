<?php

namespace App\Traits;

trait HasUsername
{
    /**
     * Check username is mobile or email.
     *
     * @param $username
     *
     * @return string
     */
    protected function getUsernameType($username): string
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        if (preg_match($pattern, $username)) {
            return 'email';
        }

        return 'mobile';
    }
}
