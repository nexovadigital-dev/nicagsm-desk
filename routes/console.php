<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Procesar respuestas de email entrantes (IMAP) — inyecta mensajes de clientes a tickets
Schedule::command('tickets:process-inbound')->everyMinute()->withoutOverlapping();

// Verificar licencia partner contra nexovadesk.com cada día a las 03:00
Schedule::command('partner:check-license')->dailyAt('03:00');

// Revertir llamados a agente que expiraron sin respuesta
Schedule::command('chat:expire-agent-calls')->everyTwoMinutes()->withoutOverlapping();

// Re-indexar URLs (source='url') en la base de conocimiento cada semana
Schedule::call(function () {
    \App\Models\KnowledgeBase::where('source', 'url')
        ->where('is_active', true)
        ->pluck('reference_id')
        ->each(fn ($url) => Artisan::call('nexova:scrape-url', ['url' => $url, '--force' => true]));
})->weekly()->name('nexova:recrawl-urls');
