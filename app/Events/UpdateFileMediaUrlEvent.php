<?php

namespace App\Events;

use App\Models\File;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class UpdateFileMediaUrlEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;

        $this->updateMediaUrlInRedis();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    private function updateMediaUrlInRedis()
    {
        if (Redis::hEXISTS($this->file->id, 'media_url')) {
            Redis::hset($this->file->id, 'media_url', $this->file->getFirstMediaUrl('file-image') ?? '');
        }
    }
}
