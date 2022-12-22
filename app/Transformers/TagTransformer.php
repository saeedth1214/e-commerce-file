<?php
/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Tag;

class TagTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'files'
    ];

    public function transform(Tag $tag)
    {
        return [
            'id'=>$tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
        ];
    }

    public function IncludeFiles(Tag $tag)
    {
        return $this->collection($tag->files, new FileTransformer());
    }
}
