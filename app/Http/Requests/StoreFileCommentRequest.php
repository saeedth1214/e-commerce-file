<?php

namespace App\Http\Requests;

use App\Enums\CommentStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFileCommentRequest extends FormRequest
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
            'parent_id' => 'nullable|integer|exists:comments,id',
            'content' => 'required|string',
            'status' => [
                'sometimes',
                'required',
                Rule::in(CommentStatusEnum::asArray())
            ]
        ];
    }
}
