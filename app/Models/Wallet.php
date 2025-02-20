<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Wallet extends Model
{
    protected $fillable = ['organization_id', 'currency'];

    const WITHDRAW_VAT_0_TO_5_000 = 0.75;
    const WITHDRAW_VAT_5_001_TO_50_000 = 1.875;
    const WITHDRAW_VAT_50_001_ABOVE = 3.75;

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
            $key = strtolower($currency) . '_rate';
            return AdminSetting::where('key', $key)->value('value') ?? 0;
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
                        AND type IN ("debit", "processing_fee", "vat", "service_charge") 
                        AND status IN ("completed", "pending"), 
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
            ->whereIn('type', ['debit', 'processing_fee', 'vat', 'service_charge'])
            ->where('status', ['completed', 'pending'])
            ->sum('amount');

        return $credits - $debits;
    }

    public function calculateWithdrawalFees($amount)
    {
        $processingFee = 0;
        $vat = 0;

        if ($amount <= 5000) {
            $processingFee = 100;
            $vat = self::WITHDRAW_VAT_0_TO_5_000;
        } elseif ($amount <= 50000) {
            $processingFee = 150;
            $vat = self::WITHDRAW_VAT_5_001_TO_50_000;
        } else {
            $processingFee = 200;
            $vat = self::WITHDRAW_VAT_50_001_ABOVE;
        }

        return [
            'processing_fee' => $processingFee,
            'vat' => $vat,
            'total_amount' => $amount + $processingFee + $vat
        ];
    }
}
