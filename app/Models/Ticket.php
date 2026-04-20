<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'widget_id',
        'contact_id',
        'session_id',
        'conversation_name',
        'whatsapp_number',
        'telegram_id',
        'telegram_username',
        'platform',
        'status',
        'client_name',
        'client_email',
        'client_phone',
        'assigned_agent',
        'priority',
        'category',
        'internal_notes',
        'visitor_ip',
        'visitor_country',
        'visitor_city',
        'visitor_device',
        'visitor_os',
        'visitor_browser',
        'visitor_referrer',
        'visitor_page',
        'rating',
        'rating_comment',
        'is_support_ticket',
        'ticket_number',
        'ticket_subject',
        'ticket_reply_token',
        'ticket_opened_at',
        'survey_rating',
        'survey_comment',
        'survey_responded_at',
        'store_context',
        'agent_called_at',
        'department_id',
    ];

    protected $casts = [
        'is_support_ticket'   => 'boolean',
        'ticket_opened_at'    => 'datetime',
        'survey_responded_at' => 'datetime',
        'store_context'       => 'array',
        'agent_called_at'     => 'datetime',
    ];

    /**
     * Obtiene los mensajes asociados a este ticket.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function contact(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(ChatWidget::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_ticket');
    }

    /**
     * Nombre visible del visitante.
     * Si client_name es real → lo devuelve tal cual.
     * Si es genérico ("Visitante") o vacío → genera "Visitante XXXXXX" usando el ID del ticket.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = trim($this->client_name ?? '');
        if ($name !== '' && strtolower($name) !== 'visitante') {
            return $name;
        }
        return 'Visitante ' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}