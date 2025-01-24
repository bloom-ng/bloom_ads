<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RockAdsTimezone extends Model
{
    protected $fillable = [
        'timezone_id',
        'name',
        'offset_str',
        'offset'
    ];

    protected $casts = [
        'offset' => 'integer',
    ];
}
