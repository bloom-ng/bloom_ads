<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $amount;
    public $currency;
    public $description;

    public function __construct(User $user, $amount, $currency, $description)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
    }

    public function build()
    {
        return $this->subject('New Invoice Request')
                    ->view('emails.invoice-request')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'businessName' => $this->user->business_name,
                        'amount' => $this->amount,
                        'currency' => $this->currency,
                        'description' => $this->description
                    ]);
    }
}
