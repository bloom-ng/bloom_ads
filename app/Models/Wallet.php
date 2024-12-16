<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    const NGN_TO_USD_RATE = 1300.00;  // 1 USD = 1300 NGN
    const NGN_TO_GBP_RATE = 1650.00;  // 1 GBP = 1650 NGN

    protected $fillable = ['organization_id', 'currency', 'balance'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function getConversionRate($toCurrency)
    {
        if ($this->currency === $toCurrency) {
            return 1;
        }

        if ($this->currency === 'NGN' && $toCurrency === 'USD') {
            return 1 / self::NGN_TO_USD_RATE;
        }

        if ($this->currency === 'NGN' && $toCurrency === 'GBP') {
            return 1 / self::NGN_TO_GBP_RATE;
        }

        if ($this->currency === 'USD' && $toCurrency === 'NGN') {
            return self::NGN_TO_USD_RATE;
        }

        if ($this->currency === 'GBP' && $toCurrency === 'NGN') {
            return self::NGN_TO_GBP_RATE;
        }

        if ($this->currency === 'USD' && $toCurrency === 'GBP') {
            return self::NGN_TO_USD_RATE / self::NGN_TO_GBP_RATE;
        }

        if ($this->currency === 'GBP' && $toCurrency === 'USD') {
            return self::NGN_TO_GBP_RATE / self::NGN_TO_USD_RATE;
        }

        throw new \Exception('Invalid currency conversion');
    }
}
