<?php

namespace App\Filament\Resources\KnowledgeBases;

use App\Filament\Resources\KnowledgeBases\Pages\CreateKnowledgeBase;
use App\Filament\Resources\KnowledgeBases\Pages\EditKnowledgeBase;
use App\Filament\Resources\KnowledgeBases\Pages\ListKnowledgeBases;
use App\Filament\Resources\KnowledgeBases\Schemas\KnowledgeBaseForm;
use App\Filament\Resources\KnowledgeBases\Tables\KnowledgeBasesTable;
use App\Models\KnowledgeBase;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class KnowledgeBaseResource extends Resource
{
    protected static ?string $model = KnowledgeBase::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Base de Conocimiento';
    protected static string|\UnitEnum|null $navigationGroup = 'Inteligencia';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-book-open';
    }

    public static function form(Schema $schema): Schema
    {
        return KnowledgeBaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KnowledgeBasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListKnowledgeBases::route('/'),
            'create' => CreateKnowledgeBase::route('/create'),
            'edit'   => EditKnowledgeBase::route('/{record}/edit'),
        ];
    }
}
