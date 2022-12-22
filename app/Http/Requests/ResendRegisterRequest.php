<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class ResendRegisterRequest extends FormRequest
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
                        return;
                    }
                    if ($this->checkTry($cacheData['try']) || $this->checkLastTry($cacheData['last_try'])) {
                        $fail(':attribute وارد شده بیش از حد تکرار شده است . بایداز آخرین درخواست شما 1 دقیقه گذشته باشد');
                    }
                }
            ],
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
        return $lastTry->addMinutes(1) >= now();
    }

    private function getCacheData($email)
    {
        $key = self::cacheKey($email);

        return Cache::get($key, []);
    }
}
