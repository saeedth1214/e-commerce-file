<?php

namespace App\Listeners;

use Intervention\Image\Facades\Image;

class MediaOptimizerListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $media = $event->media;
        $path = $media->getPath();
        $img = Image::make($path)->resize(300, 200);
        $img->save($path, 60);
    }
}
