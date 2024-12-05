<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['organization_id', 'currency', 'balance'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
