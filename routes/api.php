<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\WpApiController;

// ── Chat widget (web) — rate limited: 60 req/min por IP ──
Route::prefix('chat')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/config',                [ChatController::class, 'widgetConfig']);
    Route::post('/start',                [ChatController::class, 'startSession']);
    Route::post('/send',                 [ChatController::class, 'sendMessage']);
    Route::get('/messages/{session_id}', [ChatController::class, 'getMessages']);
    Route::post('/rate',                 [ChatController::class, 'rateChat']);
    Route::post('/visitor',              [ChatController::class, 'updateVisitor']);
    Route::post('/request-agent',        [ChatController::class, 'requestAgent']);
    Route::post('/revert-to-bot',        [ChatController::class, 'revertToBot']);
    Route::post('/contact-lookup',       [ChatController::class, 'contactLookup']);
    Route::post('/conversations/bulk',   [ChatController::class, 'conversationsBulk']);
    Route::post('/rename',               [ChatController::class, 'renameConversation']);
    Route::post('/typing-preview',       [ChatController::class, 'typingPreview']);
    Route::post('/visitor-ping',         [ChatController::class, 'visitorPing']);
    Route::post('/visitor-offline',      [ChatController::class, 'visitorOffline']);
});

// ── Admin notifications — llamadas same-origin desde el panel (JS polling) ──
// No usan Bearer token; son fetch() anónimos desde el mismo dominio.
// La seguridad viene del scope por org implícito en cada controller.
Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/admin/new-events',   [ChatController::class, 'adminNewEvents']);
    Route::get('/admin/unread-count', [ChatController::class, 'adminUnreadCount']);
});


// ── Telegram webhook — Telegram server IP, sin auth (webhook secret valida) ──
Route::prefix('webhook')->group(function () {
    Route::post('/telegram/{orgId}', [TelegramWebhookController::class, 'handle'])
        ->where('orgId', '[0-9]+');
});

// ── WP Plugin API (Bearer token auth) ──
Route::prefix('wp')->group(function () {
    Route::get('/verify',          [WpApiController::class, 'verify']);
    Route::get('/widgets',         [WpApiController::class, 'widgets']);
    Route::get('/widgets/{id}',    [WpApiController::class, 'widget']);
});

// ── Cron HTTP — para Hostinger hPanel, cron-job.org, EasyCron, etc. ─────────
// Endpoints públicos (sin token). Rate-limit a 120 req/min.
// Usar via: curl https://tu-dominio.com/api/cron/worker
Route::prefix('cron')->middleware(['throttle:120,1'])->group(function () {
    Route::get('/worker',       [\App\Http\Controllers\Api\CronController::class, 'worker']);
    Route::get('/imap',         [\App\Http\Controllers\Api\CronController::class, 'imap']);
    Route::get('/license',      [\App\Http\Controllers\Api\CronController::class, 'license']);
    Route::get('/imap-status',  [\App\Http\Controllers\Api\CronController::class, 'imapStatus']);
});
