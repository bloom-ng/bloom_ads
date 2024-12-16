<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdAccountTransaction extends Model
{
    protected $fillable = [
        'ad_account_id',
        'wallet_id',
        'amount',
        'type',
        'vat',
        'service_fee',
        'total_amount',
        'reference',
        'status',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'vat' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function adAccount()
    {
        return $this->belongsTo(AdAccount::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
