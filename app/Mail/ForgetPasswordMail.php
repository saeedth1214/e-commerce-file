<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $token;
    private $email;
  
    
    public function __construct($token, $email)
    {
        $this->token=$token;
        $this->email=$email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.forgetPassword')->with([
            'token'=>$this->token,
            'email'=>$this->email,
        ]);
    }
}
