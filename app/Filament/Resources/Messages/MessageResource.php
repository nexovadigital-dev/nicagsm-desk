<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationLabel = 'Historial de Chats';
    protected static string | \UnitEnum | null $navigationGroup = 'Conversaciones';
    protected static ?int    $navigationSort  = 20;
    protected static ?string $pluralModelLabel = 'Historial de Chats';
    protected static ?string $modelLabel = 'Mensaje';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'client_name')
                    ->label('Perteneciente al Chat de:')
                    ->required(),
                Forms\Components\Select::make('sender_type')
                    ->label('Remitente')
                    ->options([
                        'user' => 'Usuario',
                        'bot' => 'Bot IA',
                        'agent' => 'Agente Humano',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('content')
                    ->label('Contenido del Mensaje')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket.client_name')
                    ->label('Cliente')
                    ->searchable()
                    ->default('Anónimo'),
                    
                TextColumn::make('sender_type')
                    ->label('Enviado por')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'user' => 'success',
                        'bot' => 'info',
                        'agent' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'user'   => 'Usuario',
                        'bot'    => 'Bot IA',
                        'agent'  => 'Agente',
                        'system' => 'Sistema',
                        default  => $state,
                    }),

                TextColumn::make('content')
                    ->label('Mensaje')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Hora')
                    ->dateTime('d M H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
        ];
    }
}