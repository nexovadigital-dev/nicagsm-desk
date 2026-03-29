<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\ChatWidget;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class WidgetsOverview extends Page
{
    protected string $view = 'filament.superadmin.pages.widgets-overview';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Widgets';
    protected static ?int    $navigationSort  = 11;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-chat-bubble-oval-left-ellipsis';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Clientes';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public string $search = '';

    public function getWidgetsProperty()
    {
        return ChatWidget::with('organization')
            ->when(trim($this->search), fn ($q) =>
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhereHas('organization', fn ($o) =>
                        $o->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhere('allowed_domains', 'like', '%'.$this->search.'%')
            )
            ->orderByDesc('created_at')
            ->paginate(30);
    }
}
