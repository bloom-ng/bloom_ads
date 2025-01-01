<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'current_organization_id',
        'preferences',
        'two_factor_enabled',
    ];

    protected $casts = [
        'preferences' => 'array',
        'two_factor_enabled' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($settings) {
            if (is_null($settings->two_factor_enabled)) {
                $settings->two_factor_enabled = false;
            }
        });
    }

    public static function getPreferences(): array
    {
        return [
            'notifications_enabled' => true,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currentOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }
}
