<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('session_id')
                    ->default(null),
                TextInput::make('whatsapp_number')
                    ->default(null),
                TextInput::make('telegram_id')
                    ->tel()
                    ->default(null),
                Select::make('platform')
                    ->options(['web' => 'Web', 'whatsapp' => 'Whatsapp', 'telegram' => 'Telegram'])
                    ->default('web')
                    ->required(),
                Select::make('status')
                    ->options(['bot' => 'Bot', 'human' => 'Human', 'closed' => 'Closed'])
                    ->default('bot')
                    ->required(),
                TextInput::make('client_name')
                    ->default(null),
            ]);
    }
}
