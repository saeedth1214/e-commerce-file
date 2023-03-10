<?php

namespace App\Http\Requests;

use App\Enums\PlanTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
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
            'title' => 'required|string|min:2|max:256',
            'description' => 'nullable|string',
            'percentage' => 'nullable|boolean',
            'rebate' => $this->input('percentage') ? 'nullable|integer|min:0|max:100' : 'nullable|integer|min:0|max:4294967295',
            'amount' => 'required|integer|min:0|max:4294967295',
            'daily_download_limit_count' => 'required|integer|min:0|max:65535',
            'daily_free_download_limit_count' => 'required|integer|min:0|max:65535',
            'type' => ['required', 'integer', Rule::in(PlanTypeEnum::asArray())]
        ];
    }
}
