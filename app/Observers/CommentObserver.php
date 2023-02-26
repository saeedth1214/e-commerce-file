<?php

namespace App\Observers;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{

    public function created(Comment $comment)
    {
        Cache::forget('latest-comments');
    }


    public function deleted(Comment $comment)
    {
        Cache::forget('latest-comments');
    }
}
