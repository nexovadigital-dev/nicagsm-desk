<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChatWidgets\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Filament\Resources\ChatWidgetResource;
use App\Models\ChatWidget;
use App\Models\WidgetSetting;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ListChatWidgets extends Page
{
    use ScopedToOrganization;

    protected static string $resource = ChatWidgetResource::class;
    protected string $view            = 'filament.resources.chat-widgets.pages.list-chat-widgets';

    protected Width|string|null $maxContentWidth = 'full';

    public function getTitle(): string|Htmlable
    {
        return 'Widgets de Chat';
    }

    public function mount(): void
    {
        $orgId = $this->orgId();

        // Auto-migrate: si no hay widgets para esta org, crear uno desde el WidgetSetting global
        $orgWidgetCount = $orgId
            ? ChatWidget::where('organization_id', $orgId)->count()
            : ChatWidget::count();

        if ($orgWidgetCount === 0) {
            $s = WidgetSetting::instance();
            ChatWidget::create([
                'organization_id'         => $orgId,
                'name'                    => 'Widget Principal',
                'bot_name'                => $s->bot_name                ?? 'Nexova IA',
                'welcome_message'         => $s->welcome_message         ?? 'Hola, ¿en qué te puedo ayudar?',
                'accent_color'            => $s->accent_color            ?? '#7c3aed',
                'widget_position'         => $s->widget_position         ?? 'right',
                'widget_size'             => $s->widget_size             ?? 'md',
                'attention_effect'        => $s->attention_effect        ?? 'none',
                'default_screen'          => $s->default_screen          ?? 'home',
                'show_on'                 => $s->show_on                 ?? 'both',
                'preview_message_enabled' => (bool) ($s->preview_message_enabled ?? false),
                'preview_message'         => $s->preview_message         ?? '',
                'faq_enabled'             => (bool) ($s->faq_enabled     ?? false),
                'faq_items'               => $s->faq_items               ?? [],
                'social_channels'         => $s->social_channels         ?? [],
                'working_hours_enabled'   => (bool) ($s->working_hours_enabled ?? false),
                'working_hours'           => $s->working_hours           ?? [],
                'offline_message'         => $s->offline_message         ?? 'Estamos fuera de horario.',
                'show_branding'           => (bool) ($s->show_branding   ?? true),
                'sound_enabled'           => (bool) ($s->sound_enabled   ?? true),
                'require_rating'          => (bool) ($s->require_rating  ?? false),
                'rating_message'          => $s->rating_message          ?? '¿Cómo fue tu experiencia?',
                'pre_chat_enabled'        => (bool) ($s->pre_chat_enabled ?? false),
                'pre_chat_fields'         => $s->pre_chat_fields         ?? [],
                'is_active'               => true,
            ]);
        }
    }

    public function getWidgets(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->scopeToOrg(ChatWidget::query())->orderByDesc('created_at')->get();
    }

    public function delete(int $id): void
    {
        $this->scopeToOrg(ChatWidget::where('id', $id))->delete();
        $this->dispatch('nexova-toast', type: 'success', message: 'Widget eliminado');
    }

    public function toggleActive(int $id): void
    {
        $w = $this->scopeToOrg(ChatWidget::where('id', $id))->first();
        if (! $w) return;
        $w->update(['is_active' => ! $w->is_active]);
        $this->dispatch('nexova-toast', type: 'success', message: $w->is_active ? 'Widget activado' : 'Widget desactivado');
    }
}
