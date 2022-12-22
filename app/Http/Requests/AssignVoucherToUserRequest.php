<?php

namespace App\Http\Requests;

use App\Enums\VoucherTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignVoucherToUserRequest extends FormRequest
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
            'vouchers' => 'required|array',
            'vouchers.*.id' => [
                'required',
                'integer',
                Rule::exists('vouchers','id')->where(fn ($query) => $query->where('type', '=', VoucherTypeEnum::SOME_OF_USERS_HAVE_THIS()))
            ],

            'vouchers.*.authorize_use' => 'required|integer|min:1|max:1000',
            'vouchers.*.times_use' => 'required|integer|min:0|max:1000',
        ];
    }
}
