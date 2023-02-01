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
            'plan_id' => 'sometimes|required|integer|exists:plans,id,deleted_at,NULL',
            'files' => 'sometimes|required|array',
            'files.*' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('files', 'id')->where('sale_as_single', 1)->whereNull('deleted_at')
            ]
        ];
    }
}
