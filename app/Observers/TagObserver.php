<?php

namespace App\Observers;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class TagObserver
{

    public function created(Tag $tag)
    {
        Cache::forget('landingtags');
    }
    public function updated(Tag $tag)
    {
        Cache::forget('landingtags');
    }
    public function deleted(Tag $tag)
    {
        Cache::forget('landingtags');
    }

    public function forceDeleted(Tag $tag)
    {
        Cache::forget('landingtags');
    }
}
