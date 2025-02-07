<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('New User Registration')
                    ->view('emails.new-user-registration')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'businessName' => $this->user->business_name,
                        'userType' => $this->user->user_type,
                        'country' => $this->user->country
                    ]);
    }
}
