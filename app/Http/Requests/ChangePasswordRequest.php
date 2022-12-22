<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class ChangePasswordRequest extends FormRequest
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
            'email' =>[
                'required',
                'email',
                    function ($attribute, $val, $fail) {
                        $cacheData = $this->getCacheData($this->input('email'));
                        if (blank($cacheData)) {
                            $fail(':attribute ارسالی نامعتبر است');
                            return;
                        }
                    }
                ],
            'token' => [
                'required',
                'string',
                function ($attribute, $val, $fail) {
                    $cacheData = $this->getCacheData($this->input('email'));
                    if (blank($cacheData)) {
                        $fail(':attribute ارسالی نامعتبر است');
                        return;
                    }
                    if (
                        $this->checkTry($cacheData['try']) ||
                        $this->checkLastTry($cacheData['last_try']) ||
                        $cacheData['token'] != $val
                    ) {
                        $fail(':attribute ارسالی نامعتبر است');
                    }
                }
            ],
            'password' => 'required|string|min:6|max:64|confirmed',
            'password_confirmation' => 'required|string|min:6|max:64',
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
