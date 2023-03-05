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
        'files',
        'subCategories'
    ];

    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'parent_name' => $category->parent?->name,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'slug' => $category->slug,
        ];
    }
    public function IncludeFiles(Category $category)
    {
        return $this->collection(
            $category->files,
            fn ($file) => [
                'id' => $file->id,
                'title' => $file->title,
                'media' => $file->getFirstMediaUrl('file-image')
            ]
        );
    }

    public function IncludeSubCategories(Category $category)
    {
        return $this->collection($category->subCategories, new self());
    }
}
