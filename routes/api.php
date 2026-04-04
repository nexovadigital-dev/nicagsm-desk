<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\WpApiController;
use App\Http\Controllers\PaymentController;

// ── Chat widget (web) ──
Route::prefix('chat')->group(function () {
    Route::get('/config',                [ChatController::class, 'widgetConfig']);
    Route::post('/start',                [ChatController::class, 'startSession']);
    Route::post('/send',                 [ChatController::class, 'sendMessage']);
    Route::get('/messages/{session_id}', [ChatController::class, 'getMessages']);
    Route::post('/rate',                 [ChatController::class, 'rateChat']);
    Route::post('/visitor',              [ChatController::class, 'updateVisitor']);
    Route::post('/request-agent',        [ChatController::class, 'requestAgent']);
    Route::post('/revert-to-bot',        [ChatController::class, 'revertToBot']);
    // Contact recognition (returning visitor / WooCommerce lookup)
    Route::post('/contact-lookup',       [ChatController::class, 'contactLookup']);
    // Historial: resumen bulk de conversaciones por session_ids
    Route::post('/conversations/bulk',   [ChatController::class, 'conversationsBulk']);
    // Renombrar conversación
    Route::post('/rename',               [ChatController::class, 'renameConversation']);
    // Sneak-peek: visitante transmite texto mientras escribe
    Route::post('/typing-preview',       [ChatController::class, 'typingPreview']);
    // Visitor management
    Route::post('/visitor-ping',         [ChatController::class, 'visitorPing']);
    Route::post('/visitor-offline',      [ChatController::class, 'visitorOffline']);
});

// ── Admin notifications (sin auth, solo verifica Referer interno) ──
Route::get('/admin/new-events',   [ChatController::class, 'adminNewEvents']);
Route::get('/admin/unread-count', [ChatController::class, 'adminUnreadCount']);

// ── Telegram webhook ──
Route::prefix('webhook')->group(function () {
    // Per-org Telegram webhook: POST /api/webhook/telegram/{orgId}
    Route::post('/telegram/{orgId}', [TelegramWebhookController::class, 'handle'])
        ->where('orgId', '[0-9]+');
});

// ── WP Plugin API (Bearer token auth) ──
Route::prefix('wp')->group(function () {
    Route::get('/verify',          [WpApiController::class, 'verify']);
    Route::get('/widgets',         [WpApiController::class, 'widgets']);
    Route::get('/widgets/{id}',    [WpApiController::class, 'widget']);
});

// ── Payment webhooks (no CSRF) ──
Route::post('/webhooks/mercadopago', [PaymentController::class, 'mpWebhook']);

// ── Payment API (auth required) ──
Route::middleware('auth:sanctum')->prefix('payment')->group(function () {
    Route::post('/mp/initiate',      [PaymentController::class, 'mpInitiate']);
    Route::post('/crypto/initiate',  [PaymentController::class, 'cryptoInitiate']);
    Route::post('/crypto/hash',      [PaymentController::class, 'cryptoSubmitHash']);
});