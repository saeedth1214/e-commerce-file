<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FilterBySpecialValue implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if ($value['operator'] === 'between') {
            $query->where($property, '>=', $value['value']['from'])
                ->where($property, '<=', $value['value']['to']);
        } else {
            $query->whereNotNull($property)->where($property, $value['operator'], $value['value']);
        }
    }
}
