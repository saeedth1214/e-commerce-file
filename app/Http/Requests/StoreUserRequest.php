<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\UserRoleEnum;

class StoreUserRequest extends FormRequest
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
            'first_name' => 'required|string|min:3|max:64',
            'last_name' => 'required|string|min:3|max:64',
            'role_id' => [
                'required',
                'integer',
                Rule::in(UserRoleEnum::asArray())
            ],
            'mobile' => 'required|string|mobile|unique:users,mobile,NULL,id,deleted_at,NULL',
            'email' => 'required|string|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required|string|min:6|max:64|confirmed',
            'password_confirmation' => 'required|string|min:6|max:64',
        ];
    }
}
