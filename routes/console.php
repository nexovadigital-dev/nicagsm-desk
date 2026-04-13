<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Procesar respuestas de email entrantes (IMAP) — inyecta mensajes de clientes a tickets
Schedule::command('tickets:process-inbound')->everyMinute()->withoutOverlapping();


// Sincronizar sistema externo cada hora
Schedule::command('nexova:sync-external')->hourly();

// Verificar suscripciones vencidas y hacer downgrade a Free
Schedule::command('nexova:check-subscriptions')->dailyAt('02:00');

// Verificar licencia partner contra nexovadesk.com cada día a las 03:00
Schedule::command('partner:check-license')->dailyAt('03:00');

// Verificar TX hash de pagos crypto en blockchain cada 5 minutos
Schedule::command('nexova:verify-crypto')->everyFiveMinutes();

// Re-indexar URLs (source='url') cada semana
Schedule::call(function () {
    \App\Models\KnowledgeBase::where('source', 'url')
        ->where('is_active', true)
        ->pluck('reference_id')
        ->each(fn ($url) => Artisan::call('nexova:scrape-url', ['url' => $url, '--force' => true]));
})->weekly()->name('nexova:recrawl-urls');
