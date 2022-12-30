<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\UserRoleEnum;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'first_name' => 'required|string|min:4|max:64',
            'last_name' => 'required|string|min:4|max:64',
            'role_id' => [
                'required',
                'integer',
                Rule::in(UserRoleEnum::asArray())
            ],
            'files' => 'sometimes|array',
            'files.*' => 'sometimes|integer|exists:files,id',
        ];
    }
}
