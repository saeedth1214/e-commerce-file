<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Traits\DownloadKey;
use Illuminate\Foundation\Http\FormRequest;

class EnsureUserHasFileRequest extends FormRequest
{

    use DownloadKey;
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
        // the user have bought this file .
        if ($count) {
            return $count;
        }

        // check the user have got a plan.
        /**
         * @var User $user
         */
        $user = auth()->user();
        $active_plan = $user->activePlan();
        return $active_plan;
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
