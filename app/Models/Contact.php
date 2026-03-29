<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'avatar_url',
        'source',
        'woo_customer_id',
        'last_seen_at',
        'total_conversations',
        'meta',
        'notes',
    ];

    protected $casts = [
        'last_seen_at'        => 'datetime',
        'meta'                => 'array',
        'woo_customer_id'     => 'integer',
        'total_conversations' => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class)->latest();
    }

    // ── Finders ───────────────────────────────────────────────────────────────

    /**
     * Find or create a contact by email (pre-chat form / widget).
     */
    public static function findOrCreateByEmail(
        ?int    $orgId,
        string  $email,
        ?string $name  = null,
        ?string $phone = null,
        string  $source = 'widget'
    ): self {
        $contact = static::where('organization_id', $orgId)
            ->where('email', strtolower(trim($email)))
            ->first();

        if ($contact) {
            // Update name/phone if we now have them and didn't before
            $contact->fill(array_filter([
                'name'  => $contact->name  ?: $name,
                'phone' => $contact->phone ?: $phone,
            ]))->save();
            return $contact;
        }

        return static::create([
            'organization_id' => $orgId,
            'email'           => strtolower(trim($email)),
            'name'            => $name,
            'phone'           => $phone,
            'source'          => $source,
        ]);
    }

    /**
     * Find or create a contact from WooCommerce customer data (after HMAC verified).
     */
    public static function findOrCreateFromWooCommerce(
        ?int    $orgId,
        int     $wooId,
        ?string $email  = null,
        ?string $name   = null,
        ?string $phone  = null,
        ?string $avatar = null
    ): self {
        // Try by woo_customer_id first
        $contact = static::where('organization_id', $orgId)
            ->where('woo_customer_id', $wooId)
            ->first();

        // If not found by woo_id, try by email (merge if same customer)
        if (! $contact && $email) {
            $contact = static::where('organization_id', $orgId)
                ->where('email', strtolower(trim($email)))
                ->first();
        }

        if ($contact) {
            $contact->fill(array_filter([
                'woo_customer_id' => $contact->woo_customer_id ?: $wooId,
                'name'            => $contact->name  ?: $name,
                'phone'           => $contact->phone ?: $phone,
                'avatar_url'      => $contact->avatar_url ?: $avatar,
                'source'          => $contact->source === 'widget' ? 'woocommerce' : $contact->source,
            ]))->save();
            return $contact;
        }

        return static::create([
            'organization_id' => $orgId,
            'woo_customer_id' => $wooId,
            'email'           => $email ? strtolower(trim($email)) : null,
            'name'            => $name,
            'phone'           => $phone,
            'avatar_url'      => $avatar,
            'source'          => 'woocommerce',
        ]);
    }

    /**
     * Touch last_seen_at and increment conversation counter.
     */
    public function recordConversation(): void
    {
        $this->increment('total_conversations');
        $this->update(['last_seen_at' => now()]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getInitialAttribute(): string
    {
        return strtoupper(mb_substr($this->name ?? $this->email ?? '?', 0, 1));
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->name) return $this->name;
        if ($this->email) return $this->email;
        if ($this->phone) return $this->phone;
        return 'Contacto #' . $this->id;
    }
}
