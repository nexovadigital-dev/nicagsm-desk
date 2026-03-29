<?php

namespace App\Filament\Resources\ContactResource\Pages;

use App\Filament\Resources\ContactResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)->schema([

                Section::make('Identidad')
                    ->columnSpan(2)
                    ->schema([
                        Grid::make(2)->schema([
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
                            TextEntry::make('created_at')->label('Registro')->dateTime('d M Y, H:i'),
                        ]),
                    ]),

                Section::make('Actividad')
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('total_conversations')->label('Conversaciones'),
                        TextEntry::make('last_seen_at')->label('Última visita')->dateTime('d M Y, H:i')->default('—'),
                        TextEntry::make('notes')->label('Notas internas')->default('—'),
                    ]),

            ]),

            Section::make('Historial de conversaciones')
                ->schema([
                    RepeatableEntry::make('tickets')
                        ->label('')
                        ->schema([
                            Grid::make(5)->schema([
                                TextEntry::make('conversation_name')->label('Conversación')->weight('medium'),
                                TextEntry::make('status')->label('Estado')->badge()
                                    ->formatStateUsing(fn ($s) => match($s) {
                                        'bot'    => 'Bot',
                                        'human'  => 'Agente',
                                        'closed' => 'Cerrado',
                                        default  => $s,
                                    })
                                    ->color(fn ($s) => match($s) {
                                        'bot'    => 'info',
                                        'human'  => 'success',
                                        'closed' => 'gray',
                                        default  => 'gray',
                                    }),
                                TextEntry::make('platform')->label('Canal')
                                    ->formatStateUsing(fn ($s) => ucfirst($s)),
                                TextEntry::make('survey_rating')->label('CSAT')
                                    ->formatStateUsing(fn ($s) => $s ? str_repeat('★', $s) . str_repeat('☆', 5 - $s) . " ({$s}/5)" : '—')
                                    ->default('—'),
                                TextEntry::make('created_at')->label('Fecha')->dateTime('d M Y, H:i'),
                            ]),
                        ])
                        ->contained(false),
                ]),
        ]);
    }
}
