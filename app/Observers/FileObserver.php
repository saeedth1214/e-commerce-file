<?php

namespace App\Observers;

use App\Models\File;
use Illuminate\Support\Facades\Redis;

class FileObserver
{
    public function updated(File $file)
    {
        $categoryName = $file->category->name;
        $key = $file->id;
        Redis::hSet($key, 'title', $file->title);
        Redis::hSet($key, 'category_name', $categoryName);
    }

    public function deleted(File $file)
    {
        $file->comments()->delete();
        $this->removeRedisKey($file->id);
    }

    public function forceDeleted(File $file)
    {
        $file->comments()->delete();
        $this->removeRedisKey($file->id);
    }

    private function removeRedisKey($id)
    {
        Redis::hDel($id);
        Redis::zRem('view-counter', $id);
    }
}
