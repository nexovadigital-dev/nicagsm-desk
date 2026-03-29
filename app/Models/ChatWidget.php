<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChatWidget extends Model
{
    protected $fillable = [
        'organization_id', 'name', 'token', 'is_active',
        'bot_name', 'welcome_message', 'accent_color',
        'widget_position', 'widget_size', 'attention_effect', 'default_screen', 'show_on',
        'preview_message_enabled', 'preview_message',
        'faq_enabled', 'faq_items',
        'social_channels',
        'working_hours_enabled', 'working_hours', 'offline_message',
        'show_branding', 'sound_enabled', 'require_rating', 'rating_message',
        'pre_chat_enabled', 'pre_chat_fields',
        'button_style', 'button_icon', 'button_text', 'button_text_color', 'button_image',
        'agent_call_timeout', 'agent_no_response', 'bot_enabled', 'bot_avatar',
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
    ];

    protected static function booted(): void
    {
        static::creating(function (self $widget) {
            if (empty($widget->token)) {
                $widget->token = Str::random(32);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function defaultWorkingHours(): array
    {
        return [
            'mon' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Lunes'],
            'tue' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Martes'],
            'wed' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Miércoles'],
            'thu' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Jueves'],
            'fri' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Viernes'],
            'sat' => ['enabled' => false, 'from' => '09:00', 'to' => '14:00', 'label' => 'Sábado'],
            'sun' => ['enabled' => false, 'from' => '09:00', 'to' => '14:00', 'label' => 'Domingo'],
        ];
    }
}
