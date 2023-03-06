<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'voucher_id' => 'sometimes|required|integer|exists:vouchers,id,deleted_at,NULL',
            'files' => 'required|array',
            'files.*' => [
                'required',
                'integer',
                Rule::exists('files', 'id')->where('sale_as_single', true)->whereNull('deleted_at')
            ]
        ];
    }
}
