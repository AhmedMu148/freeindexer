<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsApiToken extends Model
{
    protected $table = 'cms_api_tokens';

    protected $fillable = [
        'name',
        'token_hash',
        'token_prefix',
        'abilities',
        'is_active',
        'expires_at',
        'last_used_at',
        'last_used_ip',
        'last_used_user_agent',
        'revoked_at',
        'created_by_id',
    ];

    protected $casts = [
        'abilities' => 'array',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
