<?php

namespace App\Traits;

use App\Models\File;
use App\Observers\FileObserver;

trait ObservFile
{
    public static function bootObservFile(): void
    {
        File::observe(FileObserver::class);
    }
}
