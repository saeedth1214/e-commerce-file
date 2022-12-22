<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortBySelling implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $query->withCount('users')->orderByDesc('users_count');
    }
}
