<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterFreeFiles implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if ($value === 'free') {
            $query->where($property, 0);
        } else {
            $query->where($property, '>', 0);
        }
    }
}
