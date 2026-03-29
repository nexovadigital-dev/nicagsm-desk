<?php
declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\WidgetSetting;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class WidgetConfig extends Page
{
    protected string $view = 'filament.pages.widget-config';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Widget de Chat';
    protected static string|\UnitEnum|null $navigationGroup = 'Widget';
    protected static ?int $navigationSort = 10;
    protected static bool $shouldRegisterNavigation = false; // Reemplazado por "Mis Widgets"

    // ── Identity ──────────────────────────────────────────────────────────
    public string $botName        = 'Nexova IA';
    public string $welcomeMessage = 'Hola, ¿en qué te puedo ayudar?';
    public string $accentColor    = '#7c3aed';

    // ── Appearance & Position ─────────────────────────────────────────────
    public string $widgetPosition  = 'right';
    public string $widgetSize      = 'md';
    public string $attentionEffect = 'none';
    public string $defaultScreen   = 'home';
    public string $showOn          = 'both';

    // ── Preview bubble ────────────────────────────────────────────────────
    public bool   $previewMessageEnabled = false;
    public string $previewMessage        = '';

    // ── FAQ ───────────────────────────────────────────────────────────────
    public bool  $faqEnabled = false;
    public array $faqItems   = [];

    // ── Social channels ───────────────────────────────────────────────────
    public array $socialChannels = [];

    // ── Working hours ─────────────────────────────────────────────────────
    public bool   $workingHoursEnabled = false;
    public array  $workingHours        = [];
    public string $offlineMessage      = 'Estamos fuera de horario. Te responderemos pronto.';

    // ── Behaviour ─────────────────────────────────────────────────────────
    public bool   $showBranding  = true;
    public bool   $soundEnabled  = true;
    public bool   $requireRating = false;
    public string $ratingMessage = '¿Cómo fue tu experiencia?';

    // ── Pre-chat form ─────────────────────────────────────────────────────
    public bool  $preChatEnabled = false;
    public array $preChatFields  = [];

    // ──────────────────────────────────────────────────────────────────────

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Widget de Chat';
    }

    public function mount(): void
    {
        $s = WidgetSetting::instance();

        $this->botName               = $s->bot_name               ?? 'Nexova IA';
        $this->welcomeMessage        = $s->welcome_message         ?? 'Hola, ¿en qué te puedo ayudar?';
        $this->accentColor           = $s->accent_color            ?? '#7c3aed';
        $this->widgetPosition        = $s->widget_position         ?? 'right';
        $this->widgetSize            = $s->widget_size             ?? 'md';
        $this->attentionEffect       = $s->attention_effect        ?? 'none';
        $this->defaultScreen         = $s->default_screen          ?? 'home';
        $this->showOn                = $s->show_on                 ?? 'both';
        $this->previewMessageEnabled = (bool) ($s->preview_message_enabled ?? false);
        $this->previewMessage        = $s->preview_message         ?? '';
        $this->faqEnabled            = (bool) ($s->faq_enabled     ?? false);
        $this->faqItems              = $s->faq_items               ?? [];
        $this->socialChannels        = $s->social_channels         ?? [];
        $this->workingHoursEnabled   = (bool) ($s->working_hours_enabled ?? false);
        $this->workingHours          = $s->working_hours           ?? $this->defaultWorkingHours();
        $this->offlineMessage        = $s->offline_message         ?? 'Estamos fuera de horario. Te responderemos pronto.';
        $this->showBranding          = (bool) ($s->show_branding   ?? true);
        $this->soundEnabled          = (bool) ($s->sound_enabled   ?? true);
        $this->requireRating         = (bool) ($s->require_rating  ?? false);
        $this->ratingMessage         = $s->rating_message          ?? '¿Cómo fue tu experiencia?';
        $this->preChatEnabled        = (bool) ($s->pre_chat_enabled ?? false);
        $this->preChatFields         = $s->pre_chat_fields         ?? [];

        if (empty($this->workingHours)) {
            $this->workingHours = $this->defaultWorkingHours();
        }
    }

    private function defaultWorkingHours(): array
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

    public function save(): void
    {
        $s = WidgetSetting::instance();

        $s->update([
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
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración guardada');
    }

    // ── Pre-chat field builder ────────────────────────────────────────────

    public function addField(): void
    {
        $this->preChatFields[] = [
            'label'    => '',
            'type'     => 'text',
            'required' => false,
            'enabled'  => true,
        ];
    }

    public function removeField(int $index): void
    {
        array_splice($this->preChatFields, $index, 1);
    }

    public function moveUp(int $index): void
    {
        if ($index <= 0) {
            return;
        }

        $fields = $this->preChatFields;
        [$fields[$index - 1], $fields[$index]] = [$fields[$index], $fields[$index - 1]];
        $this->preChatFields = array_values($fields);
    }

    public function moveDown(int $index): void
    {
        if ($index >= count($this->preChatFields) - 1) {
            return;
        }

        $fields = $this->preChatFields;
        [$fields[$index + 1], $fields[$index]] = [$fields[$index], $fields[$index + 1]];
        $this->preChatFields = array_values($fields);
    }

    // ── FAQ ───────────────────────────────────────────────────────────────

    public function addFaq(): void
    {
        $this->faqItems[] = [
            'question' => '',
            'answer'   => '',
        ];
    }

    public function removeFaq(int $index): void
    {
        array_splice($this->faqItems, $index, 1);
    }

    // ── Social channels ───────────────────────────────────────────────────

    public function addSocialChannel(): void
    {
        $this->socialChannels[] = [
            'type'  => 'whatsapp',
            'label' => '',
            'url'   => '',
        ];
    }

    public function removeSocialChannel(int $index): void
    {
        array_splice($this->socialChannels, $index, 1);
    }
}
