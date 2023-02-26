<?php

namespace App\Observers;

use App\Models\File;

class FileObserver
{

    /**
     * Handle the File "deleted" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function deleted(File $file)
    {
        $file->comments()->delete();
    }
    /**
     * 
     * Handle the File "force deleted" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function forceDeleted(File $file)
    {
        $file->comments()->delete();
    }
}
