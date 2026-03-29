<?php

namespace App\Models;

use App\Models\ChatWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Organization extends Model
{
    protected $fillable = [
        'name', 'slug', 'website', 'support_email', 'support_name',
        'ai_groq_key', 'ai_gemini_key', 'ai_use_own_keys',
        'max_messages_per_session', 'max_bot_sessions_per_day',
        'bot_sessions_today', 'usage_date',
        'plan', 'trial_ends_at', 'is_active', 'accent_color', 'logo_path',
    ];

    protected $casts = [
        'is_active'               => 'boolean',
        'ai_use_own_keys'         => 'boolean',
        'trial_ends_at'           => 'datetime',
        'usage_date'              => 'date',
        'max_messages_per_session'=> 'integer',
        'max_bot_sessions_per_day'=> 'integer',
        'bot_sessions_today'      => 'integer',
    ];

    protected $hidden = ['ai_groq_key', 'ai_gemini_key'];

    protected static function booted(): void
    {
        static::creating(function (self $org) {
            if (empty($org->slug)) {
                $org->slug = Str::slug($org->name) . '-' . Str::random(6);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function chatWidgets(): HasMany
    {
        return $this->hasMany(ChatWidget::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany('starts_at');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function isOnTrial(): bool
    {
        return $this->plan === 'trial' && $this->trial_ends_at?->isFuture();
    }

    public function isAiBlocked(): bool
    {
        return $this->plan === 'free' || $this->plan === 'trial';
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro';
    }

    /**
     * Get the effective Groq API key: org's own key (if enabled) or null (fallback to platform).
     */
    public function effectiveGroqKey(): ?string
    {
        if ($this->ai_use_own_keys && $this->ai_groq_key) {
            return decrypt($this->ai_groq_key);
        }
        return null;
    }

    public function effectiveGeminiKey(): ?string
    {
        if ($this->ai_use_own_keys && $this->ai_gemini_key) {
            return decrypt($this->ai_gemini_key);
        }
        return null;
    }

    /**
     * Check if the org can still start a new bot session today.
     */
    public function canStartBotSession(): bool
    {
        $today = now()->toDateString();

        // Reset counter if it's a new day
        if ($this->usage_date?->toDateString() !== $today) {
            $this->update(['bot_sessions_today' => 0, 'usage_date' => $today]);
        }

        return $this->bot_sessions_today < $this->max_bot_sessions_per_day;
    }

    public function incrementBotSessions(): void
    {
        $today = now()->toDateString();
        if ($this->usage_date?->toDateString() !== $today) {
            $this->update(['bot_sessions_today' => 1, 'usage_date' => $today]);
        } else {
            $this->increment('bot_sessions_today');
        }
    }
}
