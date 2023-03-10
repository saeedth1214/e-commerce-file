<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
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
            'title' => 'required|string|min:2|max:256|unique:files,title',
            'description' => 'nullable|string',
            'percentage' => 'nullable|boolean',
            'sale_as_single' => 'required|boolean',
            'rebate' => $this->input('percentage') ? 'nullable|integer|min:0|max:100' : 'nullable|integer|min:0|max:4294967295',
            'amount' => 'required|integer|min:0|max:4294967295',
            'category_id' => 'required|integer|exists:categories,id',
        ];
    }
}
