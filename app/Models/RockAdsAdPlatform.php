<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RockAdsAdPlatform extends Model
{
    protected $fillable = [
        'platform_id',
        'name',
        'code'
    ];

    public function getCode(): string
    {
        return $this->code;
    }
}
