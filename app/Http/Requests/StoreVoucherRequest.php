<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\VoucherTypeEnum;

class StoreVoucherRequest extends FormRequest
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
            'code'=>'required|string|min:3|max:64|unique:vouchers,code',
            'status'=>'required|boolean',
            'percentage' => 'required|boolean',
            'rebate'=>$this->input('percentage') ? 'required|integer|min:0|max:100' : 'required|integer|min:0|max:4294967295',
            'expired_at'=> 'required|date_format:Y-m-d H:i:s',
            'type'=>[
                'required',
                'integer',
                Rule::in(VoucherTypeEnum::asArray())
            ],
        ];
    }
}
