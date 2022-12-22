<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendVerificationCodeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $user;
    public $verifyCode;

    public function __construct($user, $verifyCode)
    {
        $this->user=$user;
        $this->verifyCode = $verifyCode;
    }
}
