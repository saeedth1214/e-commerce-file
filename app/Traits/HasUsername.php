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
        $pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

        if (preg_match($pattern, $username)) {
            return 'email';
        }

        return 'mobile';
    }
}
