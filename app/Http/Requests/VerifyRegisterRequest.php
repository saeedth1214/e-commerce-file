<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class VerifyRegisterRequest extends FormRequest
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
                function ($attribute, $val, $fail) {
                    $cacheData = $this->getCacheData($val);

                    if (blank($cacheData)) {
                        $fail('گزینه انتخاب شده :attribute صحیح نمی باشد');
                    }
                }
            ],
            'code' => [
                'required',
                'string',
                function ($attribute, $val, $fail) {
                    $cacheData = $this->getCacheData($this->input('email'));
                    if (blank($cacheData)) {
                        $fail('گزینه انتخاب شده :attribute صحیح نمی باشد');
                        return;
                    }
                    if (
                        $this->checkTry($cacheData['try']) ||
                        $cacheData['code'] != $val
                    ) {
                        $fail('وارد شده بیش از حد تکرار شده است :attribute');
                    }
                }
            ],
            'device_name' => 'required|string|min:5|max:128',
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
        return $lastTry->addMinutes(2) >= now();
    }

    private function getCacheData($email)
    {
        $key = self::cacheKey($email);

        return Cache::get($key, []);
    }
}
