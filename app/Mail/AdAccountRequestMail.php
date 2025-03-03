<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdAccountRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $adAccountDetails;

    public function __construct(User $user, array $adAccountDetails)
    {
        $this->user = $user;
        $this->adAccountDetails = $adAccountDetails;
    }

    public function build()
    {
        return $this->subject('New Ad Account Request')
                    ->view('emails.ad-account-request')
                    ->with([
                        'userName' => $this->user->name,
                        'adAccountDetails' => $this->adAccountDetails
                    ]);
    }
}