<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChatWidget extends Model
{
    protected $fillable = [
        'organization_id', 'department_id', 'name', 'token', 'allowed_domain', 'is_active',
        'bot_name', 'welcome_message', 'accent_color',
        'widget_position', 'widget_size', 'attention_effect', 'default_screen', 'show_on',
        'preview_message_enabled', 'preview_message',
        'faq_enabled', 'faq_items',
        'social_channels',
        'working_hours_enabled', 'working_hours', 'offline_message',
        'show_branding', 'sound_enabled', 'require_rating', 'rating_message',
        'pre_chat_enabled', 'pre_chat_fields',
        'button_style', 'button_icon', 'button_text', 'button_text_color', 'button_image',
        'agent_call_timeout', 'agent_no_response', 'bot_enabled', 'bot_avatar', 'bot_system_prompt',
        'ai_enabled', 'faq_direct', 'woo_integration_enabled', 'woo_orders_enabled',
    ];

    protected $casts = [
        'is_active'               => 'boolean',
        'preview_message_enabled' => 'boolean',
        'faq_enabled'             => 'boolean',
        'faq_items'               => 'array',
        'social_channels'         => 'array',
        'working_hours_enabled'   => 'boolean',
        'working_hours'           => 'array',
        'show_branding'           => 'boolean',
        'sound_enabled'           => 'boolean',
        'require_rating'          => 'boolean',
        'pre_chat_enabled'        => 'boolean',
        'pre_chat_fields'  => 'array',
        'bot_enabled'      => 'boolean',
        'ai_enabled'              => 'boolean',
        'faq_direct'              => 'boolean',
        'woo_integration_enabled' => 'boolean',
        'woo_orders_enabled'      => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $widget) {
            if (empty($widget->token)) {
                $widget->token = Str::random(32);
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function knowledgeBases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(KnowledgeBase::class, 'widget_id');
    }

    public function defaultWorkingHours(): array
    {
        return [
            'mon' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Lunes'],
            'tue' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Martes'],
            'wed' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Miercoles'],
            'thu' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Jueves'],
            'fri' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Viernes'],
            'sat' => ['enabled' => false, 'from' => '09:00', 'to' => '14:00', 'label' => 'Sabado'],
            'sun' => ['enabled' => false, 'from' => '09:00', 'to' => '14:00', 'label' => 'Domingo'],
        ];
    }
}