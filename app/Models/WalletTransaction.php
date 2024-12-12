<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'amount',
        'currency',
        'type', // credit or debit
        'description',
        'reference',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
