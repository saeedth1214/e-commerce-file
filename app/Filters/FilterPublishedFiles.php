<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

use Spatie\QueryBuilder\Filters\Filter;

class FilterPublishedFiles implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {

        $value === 1 && ($query->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()]));
        $value === 3 && ($query->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()]));
        $value === 6 && ($query->whereBetween('created_at', [Carbon::now()->subMonths(6), Carbon::now()]));
    }
}
