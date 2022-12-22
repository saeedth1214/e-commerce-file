<?php

/**
 * Created by PhpStorm.
 * User: Saeedth1214
 * Date: 4/10/2022
 * Time: 16:32 PM
 */

namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'files'
    ];

    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'media' => $this->getMediaUrl($category),
        ];
    }

    private function getMediaUrl(Category $category)
    {
        return $category->getFirstMediaUrl('category-image');
    }
    public function IncludeFiles(Category $category)
    {
        return $this->collection($category->files, fn ($file) => ['id' => $file->id, 'title' => $file->title, 'media' => $file->getFirstMediaUrl('file-image')]);
    }
}
