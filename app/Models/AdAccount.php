<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AdminSetting;

class AdAccount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'timezone',
        'currency',
        'provider',
        'provider_id',
        'status',
        'business_manager_id',
        'landing_page',
        'user_id',
        'organization_id',
        'provider_bm_id'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_BANNED = 'banned';
    public const STATUS_DELETED = 'deleted';
    public const STATUS_REJECTED = 'rejected';

    public const VAT_RATE = 7.5; // 7.5%
    public const SERVICE_FEE_RATE = 2.5; // 2.5%

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions()
    {
        return $this->hasMany(AdAccountTransaction::class);
    }

    public function getVatRate()
    {
        $vatRate = AdminSetting::where('key', 'ad_account_vat')->first();
        return $vatRate ? floatval($vatRate->value) : self::VAT_RATE; // fallback to constant if not found
    }

    public function getServiceFeeRate()
    {
        $serviceFeeRate = AdminSetting::where('key', 'ad_account_service_charge')->first();
        return $serviceFeeRate ? floatval($serviceFeeRate->value) : self::SERVICE_FEE_RATE; // fallback to constant
    }

    public function calculateFees($amount)
    {
        $vat = ($amount * $this->getVatRate()) / 100;
        $serviceFee = ($amount * $this->getServiceFeeRate()) / 100;
        $totalAmount = $amount + $vat + $serviceFee;

        return [
            'vat' => $vat,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount
        ];
    }

    public function getBalance()
    {
        $credits = $this->transactions()
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');

        $debits = $this->transactions()
            ->where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');

        return $credits - $debits;
    }

    // For better performance when loading multiple ad accounts
    public static function withBalances()
    {
        return static::select('ad_accounts.*')
            ->selectRaw('
                (
                    COALESCE(
                        (SELECT SUM(amount) 
                        FROM ad_account_transactions 
                        WHERE ad_account_transactions.ad_account_id = ad_accounts.id 
                        AND type = "deposit" 
                        AND status = "completed"), 
                        0
                    ) - 
                    COALESCE(
                        (SELECT SUM(amount) 
                        FROM ad_account_transactions 
                        WHERE ad_account_transactions.ad_account_id = ad_accounts.id 
                        AND type = "withdrawal" 
                        AND status = "completed"), 
                        0
                    )
                ) as calculated_balance
            ');
    }
}
