<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFileRequest extends FormRequest
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
            'title' => [
                'required', 'string', 'min:2', 'max:256',
                Rule::unique('files', 'title')->whereNull('deleted_at')->ignore($this->file->id)
            ],
            'link' => 'nullable|string|max:1024|min:10',
            'description' => 'nullable|string',
            'percentage' => 'nullable|boolean',
            'sale_as_single' => 'required|boolean',
            'rebate' => $this->input('percentage') ? 'nullable|integer|min:0|max:100' : 'nullable|integer|min:0|max:4294967295',
            'category_id' => 'required|integer|exists:categories,id,deleted_at,NULL',
            'amount' => 'required|integer|min:0|max:4294967295',
            'tags' => 'sometimes|array',
            'tags.*' => 'sometimes|integer|exists:tags,id,deleted_at,NULL'
        ];
    }
}
