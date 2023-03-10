<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Enums\AttributeTypeEnum;
use App\Enums\FileFormatEnum;
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
        'mainComments',
        'attributes'
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
            'views' => $this->getViews($file->id),
            'media_url' => $this->getMediaUrl($file),
            'title' => $file->title,
            'reaction_summary' => $file->reactionSummary(),
            'category_id' => $file->category_id,
            'category_name' => $file->category?->name,
            'amount_after_rebate' => $this->calculateRebate($file),
            'amount_after_voucher_code' => optional($file->pivot)->total_amount,
            'bought_at' => $this->shamsiDate(optional($file->pivot)->bought_at),
            'created_at' => $this->shamsiDate($file->created_at)
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

    public function getViews($id)
    {
        return Redis::hGet($id, 'views');
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
                'amount_after_rebate' => optional($file->pivot)->total_amount,
                'bought_at' => $this->shamsiDate(optional($file->pivot)->bought_at)
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

    public function IncludeAttributes(File $file)
    {

        $attributes = $file->attributes;
        return $this->collection(
            $attributes,
            fn ($attribute) => [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'type' => AttributeTypeEnum::getKey($attribute->type),
                'value' => $attribute->pivot->value,
                'value_desc' => $attribute->type === AttributeTypeEnum::FORMAT ? FileFormatEnum::asString($attribute->pivot->value) : '',
            ]

        );
    }
}
