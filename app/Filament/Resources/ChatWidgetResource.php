<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ChatWidgets\Pages;
use App\Models\ChatWidget;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;

class ChatWidgetResource extends Resource
{
    protected static ?string $model = ChatWidget::class;

    protected static ?string $navigationLabel = 'Mis Widgets';
    protected static string|\UnitEnum|null $navigationGroup = 'Widget';
    protected static ?int $navigationSort = 5;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-squares-plus';
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListChatWidgets::route('/'),
            'create' => Pages\CreateChatWidget::route('/create'),
            'edit'   => Pages\EditChatWidget::route('/{record}/edit'),
        ];
    }
}
