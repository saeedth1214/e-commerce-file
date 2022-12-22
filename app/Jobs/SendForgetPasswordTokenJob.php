<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\Mail;

class SendForgetPasswordTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $email;
    private $token;
    public function __construct($email, $token)
    {
        $this->email=$email;
        $this->token=$token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return Mail::to($this->email)->send(new ForgetPasswordMail($this->token,$this->email));
    }
}
