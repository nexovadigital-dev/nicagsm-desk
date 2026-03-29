<?php

namespace App\Filament\Resources\Tickets; // <-- El Namespace correcto para tu estructura

use App\Filament\Resources\Tickets\Pages; // <-- Importación de páginas corregida
use App\Models\Ticket;
use Filament\Forms;
use Filament\Schemas\Schema; // <-- El nuevo motor de Filament
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    // Ocultamos del menú: LiveInbox es ahora la interfaz oficial de chats
    protected static bool $shouldRegisterNavigation = false;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Bandeja de Entrada';
    protected static ?string $modelLabel = 'Chat';
    protected static ?string $pluralModelLabel = 'Bandeja de Entrada';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('client_name')
                    ->label('Nombre del Cliente')
                    ->maxLength(255),
                Forms\Components\Select::make('platform')
                    ->label('Plataforma')
                    ->options([
                        'web' => 'Web Chat',
                        'whatsapp' => 'WhatsApp',
                        'telegram' => 'Telegram',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estado Actual')
                    ->options([
                        'bot' => 'Atendido por Bot',
                        'human' => 'Esperando Humano',
                        'closed' => 'Cerrado',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('session_id')
                    ->label('ID de Sesión (Web)')
                    ->disabled(),
                Forms\Components\TextInput::make('whatsapp_number')
                    ->label('Número de WhatsApp')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->default('Anónimo')
                    ->weight('bold'),
                
                TextColumn::make('platform')
                    ->label('Canal')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'web' => 'info',
                        'whatsapp' => 'success',
                        'telegram' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bot' => 'gray',
                        'human' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bot' => '🤖 Bot',
                        'human' => '👤 Humano',
                        'closed' => '✅ Cerrado',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Iniciado el')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}