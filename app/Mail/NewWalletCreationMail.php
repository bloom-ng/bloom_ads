<?php

namespace App\Mail;

use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewWalletCreationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $wallet;
    public $user;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
        $this->user = $wallet->organization->users()->first();
    }

    public function build()
    {
        return $this->subject('New Wallet Created')
                    ->view('emails.new-wallet-creation')
                    ->with([
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'businessName' => $this->user->business_name,
                        'walletCurrency' => $this->wallet->currency,
                        'organizationName' => $this->wallet->organization->name
                    ]);
    }
}