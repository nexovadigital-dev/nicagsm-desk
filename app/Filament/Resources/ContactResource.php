<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationLabel = 'Contactos';
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-user-group';
    protected static string|\UnitEnum|null   $navigationGroup = 'Conversaciones';
    protected static ?int    $navigationSort  = 30;
    protected static ?string $slug            = 'contacts';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->default('—')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->default('—'),

                Tables\Columns\TextColumn::make('source')
                    ->label('Origen')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'woocommerce' => 'WooCommerce',
                        'pre_chat'    => 'Pre-chat',
                        'widget'      => 'Widget',
                        'manual'      => 'Manual',
                        default       => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'woocommerce' => 'success',
                        'pre_chat'    => 'info',
                        'manual'      => 'warning',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_conversations')
                    ->label('Conversaciones')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('last_seen_at')
                    ->label('Última visita')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->default('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registro')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('last_seen_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Origen')
                    ->options([
                        'woocommerce' => 'WooCommerce',
                        'pre_chat'    => 'Pre-chat',
                        'widget'      => 'Widget',
                        'manual'      => 'Manual',
                    ]),
            ])
            ->actions([
                // View as modal (infolist) — avoids the 500 on /contacts/{id}
                Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (Contact $record) => $record->name ?: $record->email ?: 'Contacto')
                    ->modalWidth('4xl')
                    ->modalContent(fn (Contact $record) => view(
                        'filament.modals.contact-detail',
                        ['contact' => $record]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar contacto')
                    ->modalDescription('¿Estás seguro de que deseas eliminar este contacto? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ])
            ->emptyStateHeading('Sin contactos aún')
            ->emptyStateDescription('Los contactos se crean automáticamente cuando un visitante deja su email o se identifica a través de WooCommerce.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (! $user?->organization_id) return null;
        $count = Contact::where('organization_id', $user->organization_id)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();
        if ($user?->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        return $query;
    }
}
