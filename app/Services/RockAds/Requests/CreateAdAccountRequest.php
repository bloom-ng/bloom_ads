<?php

namespace App\Services\RockAds\Requests;

use App\Services\RockAds\Data\DataTransferObject;

class CreateAdAccountRequest extends DataTransferObject
{
    public int $wallet_id;
    public int $platform_id;
    public string $currency_code;
    public string $website_url;
    public string $timezone;
} 