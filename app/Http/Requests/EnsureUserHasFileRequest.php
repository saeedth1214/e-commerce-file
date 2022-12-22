<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class EnsureUserHasFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $fileId = $this->file->id;
        $count = User::query()->whereHas('files', function ($query) use ($fileId) {
            $query->where('file_id', $fileId);
        })->where('id', auth()->id())
            ->count();

        $active_plan = User::query()->when(!$count, fn ($query) => $query->activePlan()->first());
        return $count || $active_plan;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
