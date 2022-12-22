<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
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
            'filters'=>'sometimes|required|array',
            'filters.email'=>'sometimes|required|array',
            'filters.email.exact'=>[
                'required_with:filters.email',
            ]
        ];
    }
}
