<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function calculateFees($amount)
    {
        $vat = ($amount * self::VAT_RATE) / 100;
        $serviceFee = ($amount * self::SERVICE_FEE_RATE) / 100;
        $totalAmount = $amount + $vat + $serviceFee;

        return [
            'vat' => $vat,
            'service_fee' => $serviceFee,
            'total_amount' => $totalAmount
        ];
    }
}
