<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
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

    /**
     * Infolist used by the modal ViewAction.
     * Filament v3: when 'view' is absent from getPages(), ViewAction opens this as a modal.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Grid::make(2)->schema([

                Section::make('Identidad')->columnSpan(1)->schema([
                    TextEntry::make('name')->label('Nombre')->default('—'),
                    TextEntry::make('email')->label('Email')->copyable()->default('—'),
                    TextEntry::make('phone')->label('Teléfono')->default('—'),
                    TextEntry::make('source')->label('Origen')->badge()
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
                    TextEntry::make('woo_customer_id')->label('WooCommerce ID')->default('—'),
                    TextEntry::make('notes')->label('Notas internas')->default('—'),
                ]),

                Section::make('Actividad')->columnSpan(1)->schema([
                    TextEntry::make('total_conversations')->label('Conversaciones'),
                    TextEntry::make('last_seen_at')->label('Última visita')
                        ->dateTime('d M Y, H:i')->default('—'),
                    TextEntry::make('created_at')->label('Registro')
                        ->dateTime('d M Y, H:i'),
                ]),

            ]),

            Section::make('Historial de tickets')->schema([
                RepeatableEntry::make('tickets')->label('')->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('conversation_name')->label('Conversación')
                            ->weight('medium')->default('—'),
                        TextEntry::make('status')->label('Estado')->badge()
                            ->formatStateUsing(fn ($s) => match($s) {
                                'bot'    => 'Bot',
                                'human'  => 'Agente',
                                'closed' => 'Cerrado',
                                'open'   => 'Abierto',
                                default  => $s,
                            })
                            ->color(fn ($s) => match($s) {
                                'bot'    => 'info',
                                'human'  => 'success',
                                'closed' => 'gray',
                                default  => 'warning',
                            }),
                        TextEntry::make('survey_rating')->label('CSAT')
                            ->formatStateUsing(fn ($s) => $s
                                ? str_repeat('★', $s) . str_repeat('☆', 5 - $s) . " ({$s}/5)"
                                : '—')
                            ->default('—'),
                        TextEntry::make('created_at')->label('Fecha')
                            ->dateTime('d M Y'),
                    ]),
                ])->contained(false),
            ]),
        ]);
    }

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
                // No 'view' in getPages() → this opens as a slide-over modal automatically
                ViewAction::make()->label('Ver')->modalWidth('4xl'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar contacto')
                    ->modalDescription('Esta acción no se puede deshacer. Las conversaciones asociadas quedarán sin contacto vinculado.')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Eliminar seleccionados'),
                ]),
            ])
            ->emptyStateHeading('Sin contactos aún')
            ->emptyStateDescription('Los contactos se crean automáticamente cuando un visitante deja su email.');
    }

    public static function getPages(): array
    {
        return [
            // Only the list — no 'view' page so ViewAction opens as modal
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
