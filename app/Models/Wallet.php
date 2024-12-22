<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Wallet extends Model
{
    protected $fillable = ['organization_id', 'currency'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public static function getRate($currency)
    {
        // Cache the rate for 5 minutes to avoid frequent DB hits
        return Cache::remember("currency_rate_{$currency}", 300, function () use ($currency) {
            return AdminSetting::where('key', "{$currency}_rate")->value('value') ?? 0;
        });
    }

    public function getConversionRate($fromCurrency)
    {
        if ($this->currency === $fromCurrency) {
            return 1;
        }

        // When converting FROM NGN TO other currencies
        if ($fromCurrency === 'NGN') {
            if ($this->currency === 'USD') {
                return 1 / self::getRate('usd');
            }
            if ($this->currency === 'GBP') {
                return 1 / self::getRate('gbp');
            }
        }

        // When converting TO NGN FROM other currencies
        if ($this->currency === 'NGN') {
            if ($fromCurrency === 'USD') {
                return self::getRate('usd');
            }
            if ($fromCurrency === 'GBP') {
                return self::getRate('gbp');
            }
        }

        // For USD to GBP and vice versa
        if ($fromCurrency === 'USD' && $this->currency === 'GBP') {
            return self::getRate('usd') / self::getRate('gbp');
        }
        if ($fromCurrency === 'GBP' && $this->currency === 'USD') {
            return self::getRate('gbp') / self::getRate('usd');
        }

        throw new \Exception('Invalid currency conversion');
    }

    // Add this method for better query performance when loading multiple wallets
    public static function withBalances()
    {
        return static::select('wallets.*')
            ->addSelect(DB::raw('
                (
                    COALESCE(
                        (SELECT SUM(amount) 
                        FROM wallet_transactions 
                        WHERE wallet_transactions.wallet_id = wallets.id 
                        AND type = "credit" 
                        AND status = "completed"), 
                        0
                    ) - 
                    COALESCE(
                        (SELECT SUM(amount) 
                        FROM wallet_transactions 
                        WHERE wallet_transactions.wallet_id = wallets.id 
                        AND type = "debit" 
                        AND status = "completed"), 
                        0
                    )
                ) as calculated_balance
            '));
    }

    public function getBalance()
    {
        $credits = $this->transactions()
            ->where('type', 'credit')
            ->where('status', 'completed')
            ->sum('amount');

        $debits = $this->transactions()
            ->where('type', 'debit')
            ->where('status', 'completed')
            ->sum('amount');

        return $credits - $debits;
    }
}
