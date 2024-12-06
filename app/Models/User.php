<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'phone_country_code',
        'password',
        'user_type',
        'business_name',
        'weblink',
        'country',
        'oauth_id',
        'oauth_provider',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'oauth_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function hasRole($organizationId, $role)
    {
        return $this->organizations()
            ->wherePivot('organization_id', $organizationId)
            ->wherePivot('role', $role)
            ->exists();
    }

    public function getFullPhoneAttribute()
    {
        return $this->phone_country_code . $this->phone;
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSettings::class);
    }

    public function currentOrganization(): BelongsTo
    {
        return $this->settings->currentOrganization();
    }
}
