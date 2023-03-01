<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class HandleRegisterRequest extends FormRequest
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
            'first_name' => 'sometimes|required|string|min:3|max:64',
            'last_name' => 'sometimes|required|string|min:3|max:64',
            'email' => [
                'required',
                'email',
                'string',
                'unique:users,email,NULL,id,deleted_at,NULL',
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
            'mobile' => 'sometimes|required|mobile|unique:users,mobile',
            'password' => 'required|string|min:6|max:64|confirmed',
            'password_confirmation' => 'required|string|min:6|max:64',
        ];
    }

    private function cacheKey($email): string
    {
        return "user.register.${email}";
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
