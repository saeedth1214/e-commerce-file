<?php

namespace App\Traits;

use App\Models\Tag;
use App\Observers\TagObserver;

trait ObservTag
{
    public static function bootObservTag(): void
    {
        Tag::observe(TagObserver::class);
    }
}
