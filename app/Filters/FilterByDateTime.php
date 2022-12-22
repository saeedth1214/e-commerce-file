<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterByDateTime implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $filters = [];

        !key_exists('from', $value) ?: $filters[] = [$property, '>=', $value['from']];

        !key_exists('to', $value) ?: $filters[] = [ $property, '<=', $value['to']];

        $query->where($filters);
    }
}
