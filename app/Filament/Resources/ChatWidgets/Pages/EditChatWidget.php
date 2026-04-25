<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatWidgets\Pages;

use App\Filament\Resources\ChatWidgetResource;
use App\Models\ChatWidget;
use App\Models\Department;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class EditChatWidget extends Page
{
    use WithFileUploads;

    protected static string $resource = ChatWidgetResource::class;
    protected string $view            = 'filament.resources.chat-widgets.pages.edit-chat-widget';

    protected Width|string|null $maxContentWidth = 'full';

    public ?int $widgetId = null;

    // ── Form fields ──────────────────────────────────────────────────────────
    public string $name           = '';
    public string $botName        = 'Nexova IA';
    public bool   $botEnabled     = true;
    public bool   $aiEnabled              = true;
    public bool   $wooIntegrationEnabled  = false;
    public bool   $wooOrdersEnabled       = false;
    public string $wpPluginSiteUrl        = '';
    public string $telegramBotUsername = "";
    public string $telegramBotName     = "";
    public string $botAvatar      = '';
    public string $botSystemPrompt = '';
    public $botAvatarFile         = null;
    public string $botAvatarPreview = '';
    public string $welcomeMessage = 'Hola, ¿en qué te puedo ayudar?';
    public string $accentColor    = '#7c3aed';
    public string $widgetPosition = 'right';
    public string $widgetSize     = 'md';
    public string $attentionEffect = 'none';
    public string $defaultScreen  = 'home';
    public string $showOn         = 'both';
    public bool   $previewMessageEnabled = false;
    public string $previewMessage  = '';
    public bool   $faqEnabled      = false;
    public bool   $faqQuickReply   = true;
    public array  $faqItems        = [];
    public array  $socialChannels  = [];
    public bool   $workingHoursEnabled = false;
    public array  $workingHours    = [];
    public string $offlineMessage  = 'Estamos fuera de horario. Te responderemos pronto.';
    public bool   $showBranding    = true;
    public bool   $showBrandingModal = false; // modal de confirmacion al desactivar
    public bool   $soundEnabled    = true;
    public bool   $requireRating   = false;
    public string $ratingMessage   = '¿Cómo fue tu experiencia?';
    public bool   $preChatEnabled  = false;
    public array  $preChatFields   = [];

    public ?int   $defaultDepartmentId = null;

    public int    $agentCallTimeout = 10;
    public string $agentNoResponse  = 'bot';

    // Name of the other widget in this org that currently has woo enabled (empty = none)
    public string $wooOtherWidgetName = '';

    public string $buttonStyle     = 'icon';   // 'icon' | 'image'
    public string $buttonIcon      = 'chat';   // 'chat' | 'chat_dots' | 'headset' | 'help'
    public string $buttonText      = '';       // optional label (shown if not empty)
    public string $buttonTextColor = '#ffffff';
    public string $buttonImage     = '';
    public $buttonImageFile    = null; // Livewire temp upload (not saved until save())
    public string $buttonImagePreview = ''; // temporary URL for live preview

    public string $widgetToken    = '';
    public string $allowedDomain = '';

    public function getTitle(): string|Htmlable
    {
        return 'Editar Widget';
    }

    public function mount(int|string $record): void
    {
        $w = ChatWidget::findOrFail($record);

        $this->widgetId            = $w->id;
        $this->widgetToken         = $w->token;
        $this->name                = $w->name;
        $this->botName             = $w->bot_name;
        $this->botEnabled              = (bool) ($w->bot_enabled ?? true);
        $this->aiEnabled               = (bool) ($w->ai_enabled ?? true);
        $this->wooIntegrationEnabled   = (bool) ($w->woo_integration_enabled ?? false);
        $this->wooOrdersEnabled        = (bool) ($w->woo_orders_enabled ?? false);

        // Buscar si la organización tiene un plugin WP conectado (site_url puede ser NULL)
        $wpToken = \App\Models\WpPluginToken::where('organization_id', $w->organization_id)
            ->latest('last_used_at')
            ->first();
        $this->wpPluginSiteUrl = $wpToken?->site_url ?? '';
        $this->botAvatar           = $w->bot_avatar ?? '';
        $this->botSystemPrompt     = $w->bot_system_prompt ?? '';
        $this->welcomeMessage      = $w->welcome_message;
        $this->accentColor         = $w->accent_color;
        $this->widgetPosition      = $w->widget_position;
        $this->widgetSize          = $w->widget_size;
        $this->attentionEffect     = $w->attention_effect;
        $this->defaultScreen       = $w->default_screen;
        $this->showOn              = $w->show_on;
        $this->previewMessageEnabled = (bool) $w->preview_message_enabled;
        $this->previewMessage      = $w->preview_message ?? '';
        $this->faqEnabled          = (bool) $w->faq_enabled;
        $this->faqQuickReply       = (bool) ($w->faq_quick_reply ?? true);
        $this->faqItems            = $w->faq_items ?? [];
        $this->socialChannels      = $w->social_channels ?? [];
        $this->workingHoursEnabled = (bool) $w->working_hours_enabled;
        $this->workingHours        = $w->working_hours ?? $w->defaultWorkingHours();
        $this->offlineMessage      = $w->offline_message;
        $this->showBranding        = (bool) $w->show_branding;
        $this->soundEnabled        = (bool) $w->sound_enabled;
        $this->requireRating       = (bool) $w->require_rating;
        $this->ratingMessage       = $w->rating_message;
        $this->preChatEnabled      = (bool) $w->pre_chat_enabled;
        $this->preChatFields       = $w->pre_chat_fields ?? [];
        $this->agentCallTimeout      = $w->agent_call_timeout  ?? 10;
        $this->agentNoResponse       = 'bot'; // tickets desde widget no soportados en esta versión
        $this->defaultDepartmentId   = $w->department_id;
        $this->buttonStyle           = $w->button_style        ?? 'icon';
        $this->buttonIcon          = $w->button_icon       ?? 'chat';
        $this->buttonText          = $w->button_text       ?? '';
        $this->buttonTextColor     = $w->button_text_color ?? '#ffffff';
        $this->buttonImage         = $w->button_image      ?? '';

        if (empty($this->workingHours)) {
            $this->workingHours = $w->defaultWorkingHours();
        }

        $this->allowedDomain = $w->allowed_domain ?? '';

        // Detect if another widget in this org already has WooCommerce enabled
        $other = ChatWidget::where('organization_id', $w->organization_id)
            ->where('id', '!=', $w->id)
            ->where('woo_integration_enabled', true)
            ->value('name');
        $this->wooOtherWidgetName = $other ?? '';
    }

    public function updatedBotAvatarFile(): void
    {
        if ($this->botAvatarFile) {
            $this->botAvatarPreview = $this->botAvatarFile->temporaryUrl();
        }
    }

    public function updatedButtonImageFile(): void
    {
        if (! $this->buttonImageFile) {
            return;
        }

        // Only generate a temporary preview — file is saved to disk in save()
        $this->buttonImagePreview = $this->buttonImageFile->temporaryUrl();
        $this->buttonStyle        = 'image';
    }

    public function save(): void
    {
        if (trim($this->name) === '') {
            $this->dispatch('nexova-toast', type: 'error', message: 'El nombre del widget es obligatorio');
            return;
        }

        // Save bot avatar if uploaded
        if ($this->botAvatarFile) {
            $widget = ChatWidget::find($this->widgetId);
            if ($widget?->bot_avatar && str_starts_with((string) $widget->bot_avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $widget->bot_avatar));
            }
            $path = $this->botAvatarFile->store('bot-avatars', 'public');
            $this->botAvatar     = Storage::url($path);
            $this->botAvatarFile = null;
            $this->botAvatarPreview = '';
        }

        // If there's a pending image upload, persist it now
        if ($this->buttonImageFile) {
            $widget = ChatWidget::find($this->widgetId);
            // Delete old stored image if any
            if ($widget?->button_image && str_starts_with((string) $widget->button_image, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $widget->button_image));
            }
            $path = $this->buttonImageFile->store('widget-logos', 'public');
            $this->buttonImage     = Storage::url($path);
            $this->buttonImageFile = null;
            $this->buttonImagePreview = '';
        }

        $widget = ChatWidget::findOrFail($this->widgetId);

        $result = $widget->update([
            'name'                    => $this->name,
            'bot_name'                => $this->botName,
            'bot_enabled'             => $this->botEnabled,
            'ai_enabled'              => $this->aiEnabled,
            'woo_integration_enabled' => $this->wooIntegrationEnabled,
            'woo_orders_enabled'      => $this->wooOrdersEnabled,
            'bot_avatar'              => $this->botAvatar ?: null,
            'bot_system_prompt'       => trim($this->botSystemPrompt) ?: null,
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
            'faq_quick_reply'         => $this->faqQuickReply,
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
            'agent_call_timeout'      => $this->agentCallTimeout,
            'agent_no_response'       => 'bot',
            'department_id'           => $this->defaultDepartmentId,
            'allowed_domain'          => trim($this->allowedDomain) ?: null,
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: 'Widget guardado correctamente');
    }

    public function getAvailableDepartmentsProperty()
    {
        $orgId = ChatWidget::find($this->widgetId)?->organization_id;
        if (! $orgId) return collect();
        return Department::where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    // ── FAQ helpers ──────────────────────────────────────────────────────────
    public function addFaq(): void    { $this->faqItems[] = ['question' => '', 'answer' => '']; }
    public function removeFaq(int $i): void { array_splice($this->faqItems, $i, 1); }

    // ── Branding modal helpers ────────────────────────────────────────────────
    public function toggleBranding(): void
    {
        if ($this->showBranding) {
            $this->showBrandingModal = true; // pide confirmación para apagar
        } else {
            $this->showBranding = true; // enciende directo
        }
    }

    public function requestDisableBranding(): void
    {
        if (! $this->showBranding) return;
        $this->showBrandingModal = true;
    }

    public function confirmDisableBranding(): void
    {
        $this->showBranding      = false;
        $this->showBrandingModal = false;
    }

    public function cancelDisableBranding(): void
    {
        $this->showBranding      = true; // asegurar que queda encendido
        $this->showBrandingModal = false;
    }

    // ── WooCommerce toggles ───────────────────────────────────────────────────
    public function toggleWoo(string $field): void
    {
        if (! in_array($field, ['wooIntegrationEnabled', 'wooOrdersEnabled'], true)) return;

        $enabling = ! $this->{$field};
        $this->{$field} = $enabling;

        if ($enabling && $field === 'wooIntegrationEnabled') {
            // Enforce one-widget-per-org: disable woo on all other widgets immediately
            $widget = ChatWidget::find($this->widgetId);
            if ($widget) {
                ChatWidget::where('organization_id', $widget->organization_id)
                    ->where('id', '!=', $this->widgetId)
                    ->where('woo_integration_enabled', true)
                    ->update(['woo_integration_enabled' => false, 'woo_orders_enabled' => false]);
            }
            $this->wooOtherWidgetName = '';
            $this->dispatch('nexova-toast', type: 'info', message: 'WooCommerce movido a este widget. Guarda para aplicar.');
        } elseif (! $enabling) {
            $msg = $field === 'wooIntegrationEnabled'
                ? 'Productos y precios desactivados. Guarda los cambios para aplicar.'
                : 'Estado de pedidos desactivado. Guarda los cambios para aplicar.';
            if ($field === 'wooIntegrationEnabled') {
                $this->wooOrdersEnabled = false; // turn off orders too when disabling integration
            }
            $this->dispatch('nexova-toast', type: 'warning', message: $msg);
        }
    }

    // ── Social helpers ───────────────────────────────────────────────────────
    public function addSocialChannel(): void    { $this->socialChannels[] = ['type' => 'whatsapp', 'label' => '', 'url' => '']; }
    public function removeSocialChannel(int $i): void { array_splice($this->socialChannels, $i, 1); }

    // ── Pre-chat helpers ─────────────────────────────────────────────────────
    public function addField(): void {
        $this->preChatFields[] = ['name' => 'campo_' . (count($this->preChatFields) + 1), 'label' => '', 'type' => 'text', 'required' => false, 'enabled' => true];
    }

    public function addDefaultFields(): void {
        $this->preChatFields = [
            ['name' => 'name',  'label' => 'Nombre',   'type' => 'text',  'required' => true,  'enabled' => true, 'placeholder' => 'Tu nombre completo'],
            ['name' => 'email', 'label' => 'Email',    'type' => 'email', 'required' => false, 'enabled' => true, 'placeholder' => 'correo@ejemplo.com'],
            ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'tel',   'required' => false, 'enabled' => true, 'placeholder' => '+57 300 000 0000'],
        ];
    }

    public function removeField(int $i): void { array_splice($this->preChatFields, $i, 1); }
}