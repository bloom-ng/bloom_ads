<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Wallet;

class WalletFundedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $wallet;
    public $amount;
    public $description;

    public function __construct(Wallet $wallet, $amount, $description)
    {
        $this->wallet = $wallet;
        $this->amount = $amount;
        $this->description = $description;
    }

    public function build()
    {
        return $this->subject('Your Wallet Has Been Funded')
                    ->markdown('emails.wallet.funded');
    }
}
