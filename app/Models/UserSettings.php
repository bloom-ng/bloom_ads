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
    ];

    

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
