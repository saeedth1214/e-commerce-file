<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Support\Arr;

class FilterUniqueValue implements Filter
{
    public function __invoke(Builder $query, $item, string $property)
    {
        
        if (Arr::has($item, ['mobile'])) {
            $item['mobile'] = $this->replaceCorrectMobileFormat($item['mobile']);
        }
        $items= array_chunk($item, 1, true);
        $query->where($items[0]);
        foreach (Arr::except($items, 0) as $value) {
            $query->orWhere($value);
        }
    }

    protected function replaceCorrectMobileFormat($value)
    {
        return preg_replace('/^(\+98|0)?9(\d{9})$/', '9$2', trim($value));
    }
}
