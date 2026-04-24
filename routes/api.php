<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\WpApiController;

// â”€â”€ Chat widget (web) â€” rate limited: 60 req/min por IP â”€â”€
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
    // Transcripción por email
    Route::post('/send-transcript',      [ChatController::class, 'sendTranscript']);
    Route::post('/visitor-ping',         [ChatController::class, 'visitorPing']);
    Route::post('/visitor-offline',      [ChatController::class, 'visitorOffline']);
});

// â”€â”€ Admin notifications â€” llamadas same-origin desde el panel (JS polling) â”€â”€
// No usan Bearer token; son fetch() anÃ³nimos desde el mismo dominio.
// La seguridad viene del scope por org implÃ­cito en cada controller.
Route::middleware(['throttle:120,1'])->group(function () {
    Route::get('/admin/new-events',   [ChatController::class, 'adminNewEvents']);
    Route::get('/admin/unread-count', [ChatController::class, 'adminUnreadCount']);
});


// â”€â”€ Telegram webhook â€” Telegram server IP, sin auth (webhook secret valida) â”€â”€
Route::prefix('webhook')->group(function () {
    Route::post('/telegram/{orgId}', [TelegramWebhookController::class, 'handle'])
        ->where('orgId', '[0-9]+');
});

// â”€â”€ WP Plugin API (Bearer token auth) â”€â”€
Route::prefix('wp')->group(function () {
    Route::get('/verify',          [WpApiController::class, 'verify']);
    Route::get('/widgets',         [WpApiController::class, 'widgets']);
    Route::get('/widgets/{id}',    [WpApiController::class, 'widget']);
    Route::patch('/widgets/{id}',  [WpApiController::class, 'updateWidget']);
});

// â”€â”€ Cron HTTP â€” para Hostinger hPanel, cron-job.org, EasyCron, etc. â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Endpoints pÃºblicos (sin token). Rate-limit a 120 req/min.
// Usar via: curl https://tu-dominio.com/api/cron/worker
Route::prefix('cron')->middleware(['throttle:120,1'])->group(function () {
    Route::get('/worker',       [\App\Http\Controllers\Api\CronController::class, 'worker']);
    Route::get('/imap',         [\App\Http\Controllers\Api\CronController::class, 'imap']);
    Route::get('/license',      [\App\Http\Controllers\Api\CronController::class, 'license']);
    Route::get('/imap-status',  [\App\Http\Controllers\Api\CronController::class, 'imapStatus']);
});
