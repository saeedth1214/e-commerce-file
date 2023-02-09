<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Models\File;
use League\Fractal\TransformerAbstract;
use App\Traits\ConvertDateTime;
use App\Traits\AmountAfterModelRebate;
use Illuminate\Support\Facades\Redis;

class FileTransformer extends TransformerAbstract
{
    use ConvertDateTime;
    use AmountAfterModelRebate;

    protected array $availableIncludes = [
        'category',
        'tags',
        'users',
        'comments',
        'mainComments'
    ];

    public function transform(File $file)
    {
        $data = [
            'id' => $file->id,
            'description' => $file->description,
            'sale_as_single' => $file->sale_as_single,
            'percentage' => $file->percentage,
            'amount' => $file->amount,
            'rebate' => $file->rebate,
            'download_count' => $file->download_count,
            'views' => Redis::get($file->title . ':count'),
            'media_url' => $this->getMediaUrl($file),
            'title' => $file->title,
            'reaction_summary' => $file->reactionSummary(),
            'category_id' => $file->category_id,
            'category_name' => $file->category?->name,
            'amount_after_rebate' => $this->calculateRebate($file),
            'amount_after_rebate_code' => optional($file->pivot)->amount,
            'bought_at' => $this->convertToMilai(optional($file->pivot)->bought_at),
            'created_at' => $this->convertToMilai($file->created_at)
        ];

        return auth('sanctum')->check() ? $data + ['is_reacted' => $file->is_reacted] : $data;
    }

    public function IncludeCategory(File $file)
    {
        if (!$file->category) {
            return $this->null();
        }
        return $this->item($file->category, new CategoryTransformer());
    }

    private function getMediaUrl(File $file)
    {
        return $file->getFirstMediaUrl('file-image');
    }


    public function getFileTitle($file)
    {
        return optional($file->getMedia())[0];
    }

    public function IncludeTags(File $file)
    {
        return $this->collection($file->tags, new TagTransformer());
    }
    public function IncludeUsers(File $file)
    {
        return $this->collection(
            $file->users,
            fn ($file)
            => [
                'user_id' => optional($file->pivot)->user_id,
                'amount_after_rebate' => optional($file->pivot)->amount,
                'bought_at' => $this->convertToMilai(optional($file->pivot)->bought_at)
            ]
        );
    }

    public function IncludeComments(File $file)
    {
        return $this->collection($file->comments, new CommentTransformer());
    }
    public function IncludeMainComments(File $file)
    {
        return $this->collection($file->mainComments, new CommentTransformer());
    }
}
