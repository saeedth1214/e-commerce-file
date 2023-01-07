<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DailyFileDownloadEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }
}
