<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetSetting extends Model
{
    protected $fillable = [
        'bot_name',
        'welcome_message',
        'accent_color',
        'pre_chat_enabled',
        'pre_chat_fields',
        'show_branding',
        'require_rating',
        'rating_message',
        'sound_enabled',
        // New Chatway-parity fields
        'widget_position',
        'widget_size',
        'preview_message_enabled',
        'preview_message',
        'attention_effect',
        'show_on',
        'default_screen',
        'faq_enabled',
        'faq_items',
        'social_channels',
        'working_hours_enabled',
        'working_hours',
        'offline_message',
    ];

    protected $casts = [
        'pre_chat_enabled'        => 'boolean',
        'pre_chat_fields'         => 'array',
        'show_branding'           => 'boolean',
        'require_rating'          => 'boolean',
        'sound_enabled'           => 'boolean',
        'preview_message_enabled' => 'boolean',
        'faq_enabled'             => 'boolean',
        'faq_items'               => 'array',
        'social_channels'         => 'array',
        'working_hours_enabled'   => 'boolean',
        'working_hours'           => 'array',
    ];

    public static function instance(): self
    {
        return self::firstOrCreate([], [
            'bot_name'                => 'Nexova IA',
            'welcome_message'         => 'Hola, ¿en qué te puedo ayudar?',
            'accent_color'            => '#7c3aed',
            'pre_chat_enabled'        => false,
            'show_branding'           => true,
            'require_rating'          => false,
            'rating_message'          => '¿Cómo fue tu experiencia?',
            'sound_enabled'           => true,
            'widget_position'         => 'right',
            'widget_size'             => 'md',
            'preview_message_enabled' => false,
            'attention_effect'        => 'none',
            'show_on'                 => 'both',
            'default_screen'          => 'home',
            'faq_enabled'             => false,
            'faq_items'               => [],
            'social_channels'         => [],
            'preview_message'         => '',
            'working_hours_enabled'   => false,
            'working_hours'           => [
                'mon' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Lunes'],
                'tue' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Martes'],
                'wed' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Miércoles'],
                'thu' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Jueves'],
                'fri' => ['enabled' => true,  'from' => '09:00', 'to' => '18:00', 'label' => 'Viernes'],
                'sat' => ['enabled' => false, 'from' => '09:00', 'to' => '14:00', 'label' => 'Sábado'],
                'sun' => ['enabled' => false, 'from' => '09:00', 'to' => '14:00', 'label' => 'Domingo'],
            ],
            'offline_message'         => 'Estamos fuera de horario. Te responderemos pronto.',
        ]);
    }
}
