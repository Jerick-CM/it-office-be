<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($firstName, $token)
    {
        $this->firstName = $firstName;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.verification', [
            'firstName' => $this->firstName,
            'token' => $this->token
        ]);
    }
}
