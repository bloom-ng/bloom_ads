<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdAccount extends Model
{
    protected $fillable = [
        'name',
        'type',
        'timezone',
        'currency',
        'provider',
        'provider_id',
        'status',
        'business_manager_id',
        'landing_page',
        'user_id',
        'organization_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_BANNED = 'banned';
    public const STATUS_DELETED = 'deleted';
    public const STATUS_REJECTED = 'rejected';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
} 