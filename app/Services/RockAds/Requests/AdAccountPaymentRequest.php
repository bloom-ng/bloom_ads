<?php

namespace App\Services\RockAds\Requests;

use App\Services\RockAds\Data\DataTransferObject;

class AdAccountPaymentRequest extends DataTransferObject
{
    public string $amount;
    public string $wallet_id;
} 