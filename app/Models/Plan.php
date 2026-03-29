<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'slug', 'name', 'description', 'price_usd',
        'max_agents', 'max_widgets', 'max_sessions_per_day', 'max_messages_per_session',
        'features', 'is_active', 'ai_blocked', 'sort',
    ];

    protected $casts = [
        'features'                 => 'array',
        'is_active'                => 'boolean',
        'ai_blocked'               => 'boolean',
        'price_usd'                => 'decimal:2',
        'max_agents'               => 'integer',
        'max_widgets'              => 'integer',
        'max_sessions_per_day'     => 'integer',
        'max_messages_per_session' => 'integer',
        'sort'                     => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function isFree(): bool
    {
        return $this->price_usd == 0;
    }

    public function isAiBlocked(): bool
    {
        return (bool) $this->ai_blocked;
    }
}
