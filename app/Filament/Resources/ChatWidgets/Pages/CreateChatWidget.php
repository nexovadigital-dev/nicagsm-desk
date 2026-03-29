<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatWidgets\Pages;

use App\Filament\Resources\ChatWidgetResource;
use App\Models\ChatWidget;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\WithFileUploads;

class CreateChatWidget extends Page
{
    use WithFileUploads;
    protected static string $resource = ChatWidgetResource::class;
    protected string $view            = 'filament.resources.chat-widgets.pages.edit-chat-widget';

    protected Width|string|null $maxContentWidth = 'full';

    // ── Form fields ──────────────────────────────────────────────────────────
    public string $name           = '';
    public string $botName        = 'Nexova IA';
    public string $welcomeMessage = 'Hola, ¿en qué te puedo ayudar?';
    public string $accentColor    = '#7c3aed';
    public string $widgetPosition = 'right';
    public string $widgetSize     = 'md';
    public string $attentionEffect = 'none';
    public string $defaultScreen  = 'home';
    public string $showOn         = 'both';
    public bool   $previewMessageEnabled = false;
    public string $previewMessage  = '';
    public bool   $faqEnabled      = true;
    public array  $faqItems        = [
        ['question' => '¿Cuál es el horario de atención?',      'answer' => 'Nuestro horario de atención es de lunes a viernes de 9:00 a 18:00 hrs.'],
        ['question' => '¿Cómo puedo hacer un pedido?',          'answer' => 'Puedes hacer tu pedido directamente desde nuestra tienda en línea o contactarnos por este chat.'],
        ['question' => '¿Cuánto tarda el envío?',               'answer' => 'El tiempo de entrega estándar es de 3 a 5 días hábiles dependiendo de tu ubicación.'],
        ['question' => '¿Aceptan devoluciones?',                'answer' => 'Sí, aceptamos devoluciones dentro de los 30 días posteriores a la compra. Contáctanos para iniciar el proceso.'],
        ['question' => '¿Tienen soporte técnico?',              'answer' => 'Sí, contamos con soporte técnico disponible por este chat y por correo electrónico.'],
    ];
    public array  $socialChannels  = [];
    public bool   $workingHoursEnabled = false;
    public array  $workingHours    = [];
    public string $offlineMessage  = 'Estamos fuera de horario. Te responderemos pronto.';
    public bool   $showBranding    = true;
    public bool   $soundEnabled    = true;
    public bool   $requireRating   = false;
    public string $ratingMessage   = '¿Cómo fue tu experiencia?';
    public bool   $preChatEnabled  = false;
    public array  $preChatFields   = [];

    // ── Botón / icono del FAB ────────────────────────────────────────────────
    public string $buttonStyle        = 'icon';
    public string $buttonIcon         = 'chat';
    public string $buttonText         = '';
    public string $buttonTextColor    = '#ffffff';
    public string $buttonImage        = '';
    public string $buttonImagePreview = '';
    public        $buttonImageFile    = null;

    public ?int $widgetId = null; // null = creating

    public function getTitle(): string|Htmlable
    {
        return 'Nuevo Widget';
    }

    public function mount(): void
    {
        $this->workingHours = (new ChatWidget)->defaultWorkingHours();
    }

    public function save(): void
    {
        if (trim($this->name) === '') {
            $this->dispatch('nexova-toast', type: 'error', message: 'El nombre del widget es obligatorio');
            return;
        }

        $widget = ChatWidget::create([
            'organization_id'         => auth()->user()?->organization_id,
            'name'                    => $this->name,
            'bot_name'                => $this->botName,
            'welcome_message'         => $this->welcomeMessage,
            'accent_color'            => $this->accentColor,
            'widget_position'         => $this->widgetPosition,
            'widget_size'             => $this->widgetSize,
            'attention_effect'        => $this->attentionEffect,
            'default_screen'          => $this->defaultScreen,
            'show_on'                 => $this->showOn,
            'preview_message_enabled' => $this->previewMessageEnabled,
            'preview_message'         => $this->previewMessage,
            'faq_enabled'             => $this->faqEnabled,
            'faq_items'               => $this->faqItems,
            'social_channels'         => $this->socialChannels,
            'working_hours_enabled'   => $this->workingHoursEnabled,
            'working_hours'           => $this->workingHours,
            'offline_message'         => $this->offlineMessage,
            'show_branding'           => $this->showBranding,
            'sound_enabled'           => $this->soundEnabled,
            'require_rating'          => $this->requireRating,
            'rating_message'          => $this->ratingMessage,
            'pre_chat_enabled'        => $this->preChatEnabled,
            'pre_chat_fields'         => $this->preChatFields,
            'button_style'            => $this->buttonStyle,
            'button_icon'             => $this->buttonIcon,
            'button_text'             => $this->buttonText,
            'button_text_color'       => $this->buttonTextColor,
            'button_image'            => $this->buttonImage ?: null,
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: 'Widget creado correctamente');

        $this->redirect(ChatWidgetResource::getUrl('edit', ['record' => $widget->id]));
    }

    // ── Button image upload ───────────────────────────────────────────────────
    public function updatedButtonImageFile(): void
    {
        if (! $this->buttonImageFile) return;
        $this->buttonImagePreview = $this->buttonImageFile->temporaryUrl();
        $this->buttonStyle        = 'image';
    }

    // ── FAQ helpers ──────────────────────────────────────────────────────────
    public function addFaq(): void    { $this->faqItems[] = ['question' => '', 'answer' => '']; }
    public function removeFaq(int $i): void { array_splice($this->faqItems, $i, 1); }

    // ── Social helpers ───────────────────────────────────────────────────────
    public function addSocialChannel(): void    { $this->socialChannels[] = ['type' => 'whatsapp', 'label' => '', 'url' => '']; }
    public function removeSocialChannel(int $i): void { array_splice($this->socialChannels, $i, 1); }

    // ── Pre-chat helpers ─────────────────────────────────────────────────────
    public function addField(): void    { $this->preChatFields[] = ['label' => '', 'type' => 'text', 'required' => false, 'enabled' => true]; }
    public function removeField(int $i): void { array_splice($this->preChatFields, $i, 1); }
}
