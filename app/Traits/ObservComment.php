<?php

namespace App\Traits;

use App\Models\Comment;

use App\Observers\CommentObserver;

trait ObservComment
{
    public static function bootObservComment(): void
    {
        Comment::observe(CommentObserver::class);
    }
}
