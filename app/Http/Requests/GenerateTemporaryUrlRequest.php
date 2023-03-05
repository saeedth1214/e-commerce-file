<?php

namespace App\Http\Requests;

use App\Enums\FileFormatEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateTemporaryUrlRequest extends FormRequest
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
            'expiration_time' => 'required|integer|min:0|max:604800',
        ];
    }
}
