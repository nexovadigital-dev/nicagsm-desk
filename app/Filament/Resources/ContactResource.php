<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Support\NxNotification;

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
                ViewAction::make()
                    ->label('Ver')
                    ->slideOver()
                    ->modalContent(fn ($record) => view(
                        'filament.modals.contact-detail',
                        ['record' => $record]
                    ))
                    ->modalFooterActions([]),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->modalHeading(fn ($record) => 'Eliminar a ' . ($record->name ?: $record->email ?: 'este contacto'))
                    ->modalDescription(fn ($record) => 'Se eliminará permanentemente el contacto y todo su historial de conversaciones. Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Eliminar contacto')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('sm')
                    ->color('danger')
                    ->successNotification(
                        NxNotification::makeDanger(
                            'Contacto eliminado',
                            'El contacto fue eliminado correctamente.'
                        )
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->modalHeading('Eliminar contactos seleccionados')
                        ->modalDescription('Se eliminarán permanentemente los contactos seleccionados y todo su historial. Esta acción no se puede deshacer.')
                        ->modalSubmitActionLabel('Eliminar contactos')
                        ->modalCancelActionLabel('Cancelar')
                        ->modalWidth('sm')
                        ->color('danger')
                        ->successNotification(
                            NxNotification::makeDanger(
                                'Contactos eliminados',
                                'Los contactos seleccionados fueron eliminados correctamente.'
                            )
                        ),
                ]),
            ])
            ->emptyStateHeading('Sin contactos aún')
            ->emptyStateDescription('Los contactos se crean automáticamente cuando un visitante deja su email.');
    }

    public static function getPages(): array
    {
        return [
            // Only list — no 'view' route so ViewAction opens as modal, not /contacts/{id}
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
