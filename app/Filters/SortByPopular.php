<?php

namespace App\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByPopular implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {

        $query->withCount(['reactions' => fn ($query) => $query->where('type', 'like')])->orderByDesc('reactions_count');
    }
}
