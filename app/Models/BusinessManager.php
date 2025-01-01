<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessManager extends Model
{
    protected $fillable = ['name', 'platform', 'portfolio_id', 'token', 'metadata'];

    protected $casts = [
        'metadata' => 'array'
    ];
}
