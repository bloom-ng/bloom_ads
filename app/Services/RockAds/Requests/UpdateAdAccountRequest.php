<?php

namespace App\Services\RockAds\Requests;

use App\Services\RockAds\Data\DataTransferObject;

class UpdateAdAccountRequest extends DataTransferObject
{
    public int $wallet_id;
    public string $alias_name;
} 