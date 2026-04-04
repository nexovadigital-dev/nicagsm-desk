<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveVisitor extends Model
{
    protected $fillable = [
        'organization_id', 'visitor_key', 'widget_token',
        'current_url', 'page_title', 'referrer', 'pages_visited',
        'ip', 'country', 'city', 'device', 'os', 'browser',
        'is_idle', 'tab_visible', 'session_id', 'chat_open',
        'proactive_open', 'proactive_message',
        'first_seen_at', 'last_ping_at',
    ];

    protected $casts = [
        'pages_visited'  => 'array',
        'is_idle'        => 'boolean',
        'tab_visible'    => 'boolean',
        'chat_open'      => 'boolean',
        'proactive_open' => 'boolean',
        'first_seen_at'  => 'datetime',
        'last_ping_at'   => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** Friendly deterministic name from visitor_key, e.g. "Azul-4291" */
    public function getFriendlyNameAttribute(): string
    {
        static $colors = ['Azul','Verde','Rojo','Dorado','Coral','Lima','Jade','Turquesa','Índigo','Ámbar','Gris','Rosa','Teal','Bronce','Oliva'];
        $hash = abs(crc32($this->visitor_key));
        return $colors[$hash % count($colors)] . '-' . ($hash % 9000 + 1000);
    }

    /** Status: active | idle | hidden */
    public function getStatusAttribute(): string
    {
        if ($this->is_idle || !$this->tab_visible) return 'idle';
        return 'active';
    }

    /** Seconds since first_seen */
    public function getTimeOnSiteAttribute(): int
    {
        return (int) ($this->first_seen_at?->diffInSeconds(now()) ?? 0);
    }
}
