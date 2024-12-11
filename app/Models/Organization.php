<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'organization_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function invites()
    {
        return $this->hasMany(OrganizationInvite::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
}
