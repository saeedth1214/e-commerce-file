<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandleLoginRequest extends FormRequest
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
            'username'=>'required|string|username',
            'password'=>'required|string|min:6|max:40',
            'device_name'=>'required|string|min:5|max:128'
        ];
    }
}
