<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class ForgetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                'string',
                'exists:users,email',
                function ($attribute, $val, $fail) {
                    $cacheData = $this->getCacheData($this->input('email'));
                    if (blank($cacheData)) {
                        return;
                    }

                    if ($this->checkTry($cacheData['try']) || $this->checkLastTry($cacheData['last_try'])) {
                        $fail(':attribute وارد شده بیش از حد تکرار شده است');
                    }
                },
            ],
        ];
    }
    private function cacheKey($email): string
    {
        return "user.forget-password.${email}";
    }

    private function checkTry($try)
    {
        return $try >= 5;
    }

    private function checkLastTry($lastTry)
    {
        return $lastTry->addMinute() >= now();
    }

    private function getCacheData($email)
    {
        $key = self::cacheKey($email);

        return Cache::get($key, []);
    }
}
