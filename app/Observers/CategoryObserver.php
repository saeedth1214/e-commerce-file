<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{

    public function created(Category $category)
    {
        Cache::forget('category-menubar');
    }


    public function deleted(Category $Category)
    {
        Cache::forget('category-menubar');
    }
}
