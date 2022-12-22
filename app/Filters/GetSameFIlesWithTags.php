<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class GetSameFilesWithTags implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $tags= $value['tags'];
        if (!is_array($tags)) {
            $tags=array($tags);
        }
        return $query->whereHas('tags', fn ($query) =>$query->whereIn('tag_id', $tags)->where('file_id', '<>', $value['exceptedFile']));
    }
}
