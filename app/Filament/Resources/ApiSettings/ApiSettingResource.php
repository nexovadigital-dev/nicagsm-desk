<?php

namespace App\Filament\Resources\ApiSettings;

use App\Filament\Resources\ApiSettings\Pages;
use App\Models\ApiSetting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ApiSettingResource extends Resource
{
    protected static ?string $model = ApiSetting::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Llaves API';
    protected static ?string $modelLabel = 'API';
    protected static ?string $pluralModelLabel = 'Configuración de APIs';

    // Ocultamos este resource del nav — usamos la página ApiKeysSettings en su lugar
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('provider')
                    ->label('Proveedor del Servicio')
                    ->options([
                        'groq' => 'Groq (Llama 3)',
                        'gemini' => 'Google Gemini',
                        'meta_whatsapp' => 'WhatsApp Cloud API',
                        'telegram' => 'Telegram Bot API',
                        'woocommerce' => 'WooCommerce REST API',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('api_key')
                    ->label('Llave de Acceso (API Key / Token)')
                    ->password() // Oculta los caracteres al escribir
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('webhook_verify_token')
                    ->label('Token de Verificación Webhook (Solo Meta)')
                    ->maxLength(255),
                Forms\Components\TextInput::make('priority')
                    ->label('Prioridad (1 = Principal, 2 = Respaldo)')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('API Activa')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider')
                    ->label('Proveedor')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                
                IconColumn::make('is_active')
                    ->label('Estado')
                    ->boolean(),

                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Última actualización')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('priority', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiSettings::route('/'),
            'create' => Pages\CreateApiSetting::route('/create'),
            'edit' => Pages\EditApiSetting::route('/{record}/edit'),
        ];
    }
}