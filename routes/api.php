<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\TelegramWebhookController;
use App\Http\Controllers\Api\WpApiController;

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
    Route::post('/contact-lookup',       [ChatController::class, 'contactLookup']);
    Route::post('/conversations/bulk',   [ChatController::class, 'conversationsBulk']);
    Route::post('/rename',               [ChatController::class, 'renameConversation']);
    Route::post('/typing-preview',       [ChatController::class, 'typingPreview']);
    Route::post('/visitor-ping',         [ChatController::class, 'visitorPing']);
    Route::post('/visitor-offline',      [ChatController::class, 'visitorOffline']);
});

// ── Admin notifications ──
Route::get('/admin/new-events',   [ChatController::class, 'adminNewEvents']);
Route::get('/admin/unread-count', [ChatController::class, 'adminUnreadCount']);

// ── Telegram webhook ──
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
