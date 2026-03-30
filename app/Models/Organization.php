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
        'bot_sessions_today', 'bot_messages_this_month', 'bot_messages_month_reset', 'usage_date',
        'plan', 'trial_ends_at', 'is_active', 'is_partner', 'accent_color', 'logo_path',
    ];

    protected $casts = [
        'is_active'               => 'boolean',
        'is_partner'              => 'boolean',
        'ai_use_own_keys'         => 'boolean',
        'trial_ends_at'           => 'datetime',
        'usage_date'              => 'date',
        'max_messages_per_session'  => 'integer',
        'max_bot_sessions_per_day'  => 'integer',
        'bot_sessions_today'        => 'integer',
        'bot_messages_this_month'   => 'integer',
        'bot_messages_month_reset'  => 'date',
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

    public function isPartner(): bool
    {
        return (bool) $this->is_partner;
    }

    public function isAiBlocked(): bool
    {
        // Partner orgs always have AI enabled
        if ($this->is_partner) return false;
        return $this->plan === 'free' || $this->plan === 'trial';
    }

    public function hasMonthlyBotQuota(): bool
    {
        if ($this->is_partner) return true; // Partner = unlimited
        $plan = \App\Models\Plan::where('slug', $this->plan)->first();
        if (! $plan || $plan->max_bot_messages_monthly === 0) {
            return true; // unlimited
        }
        $this->resetMonthlyCounterIfNeeded();
        return $this->bot_messages_this_month < $plan->max_bot_messages_monthly;
    }

    public function incrementBotMessageCount(): void
    {
        $this->resetMonthlyCounterIfNeeded();
        $this->increment('bot_messages_this_month');
    }

    private function resetMonthlyCounterIfNeeded(): void
    {
        $now = now()->toDateString();
        $reset = $this->bot_messages_month_reset;
        if (! $reset || $reset->format('Y-m') !== now()->format('Y-m')) {
            $this->update([
                'bot_messages_this_month'  => 0,
                'bot_messages_month_reset' => $now,
            ]);
        }
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
        if ($this->is_partner) return true; // Partner = unlimited sessions
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
