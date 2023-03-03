<?php

namespace App\Traits;

use App\Models\Category;

use App\Observers\CategoryObserver;


trait ObservCategory
{
    public static function bootObservComment(): void
    {
        Category::observe(CategoryObserver::class);
    }
}
