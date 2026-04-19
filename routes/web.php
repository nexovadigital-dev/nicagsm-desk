<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\AcceptInvitation;
use App\Livewire\Auth\ForgotPassword;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\WpConnectController;
use App\Http\Controllers\WpAuthorizeController;

// ── Auth público ──────────────────────────────────────────────────────────────
// /register y /login redirigen a Filament — sin middleware guest para evitar loop
Route::get('/register', fn () => redirect('/app/login'))->name('auth.register');
Route::get('/login',    fn () => redirect('/app/login'))->name('auth.login');

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password',    ForgotPassword::class)->name('auth.forgot');
    Route::get('/invitation/{token}', AcceptInvitation::class)->name('auth.invitation');
});

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('auth.logout');

// ── Landing / demo ────────────────────────────────────────────────────────────
// Partner Edition — redirect to Filament login directly (avoids guest-middleware loop)
Route::get('/', fn () => redirect('/app/login'));

// ── Blog / Novedades ──────────────────────────────────────────────────────────
Route::get('/novedades', function () {
    $cat   = request('cat');
    $query = \App\Models\Post::published()->orderByDesc('published_at');
    if ($cat) {
        $query->where('category', $cat);
    }
    $posts = $query->paginate(9)->withQueryString();
    return view('novedades.index', compact('posts', 'cat'));
});

Route::get('/novedades/{slug}', function (string $slug) {
    $post = \App\Models\Post::published()->where('slug', $slug)->firstOrFail();
    $related = \App\Models\Post::published()
        ->where('id', '!=', $post->id)
        ->where('category', $post->category)
        ->orderByDesc('published_at')
        ->limit(3)
        ->get();
    return view('novedades.show', compact('post', 'related'));
});

// ── Páginas estáticas CMS ─────────────────────────────────────────────────────
Route::get('/p/{slug}', function (string $slug) {
    $page = \App\Models\Page::published()->where('slug', $slug)->firstOrFail();
    return view('pages.show', compact('page'));
});

// Página de demostración del widget (solo desarrollo)
Route::get('/chat-demo', function () {
    return view('chat-demo');
});

// HQ super-admin not available in Partner Edition

// ── Survey público (sin auth) ─────────────────────────────────────────────────
Route::get('/survey/{token}',  [SurveyController::class, 'show'])->name('survey.show');
Route::post('/survey/{token}', [SurveyController::class, 'submit'])->name('survey.submit');

// ── Widget loader — sirve public/widget.js (IIFE build) con CORS ─────────────
Route::get('/widget.js', function () {
    $path = public_path('widget.js');
    if (! file_exists($path)) {
        return response('/* widget not built — run: npm run build:widget */', 200, [
            'Content-Type' => 'application/javascript',
        ]);
    }
    return response(file_get_contents($path), 200, [
        'Content-Type'                => 'application/javascript',
        'Cache-Control'               => 'public, max-age=3600',
        'Access-Control-Allow-Origin' => '*',
    ]);
});

// ── Panel API — endpoints internos para el agente (requieren auth web) ───────
Route::middleware('auth')->prefix('api/panel')->group(function () {

    // Llamadas entrantes (tickets human sin agente asignado, últimos 15 min)
    Route::get('/incoming-agent-calls', function () {
        $user = auth()->user();
        $query = \App\Models\Ticket::where('status', 'human')
            ->whereNull('assigned_agent')
            ->whereNotNull('agent_called_at')
            ->where('agent_called_at', '>=', now()->subMinutes(15))
            ->where('organization_id', $user->organization_id)
            ->orderBy('agent_called_at')
            ->get(['id', 'client_name', 'agent_called_at']);
        return response()->json(['calls' => $query]);
    });

    // Aceptar ticket — assignToMe vía API
    Route::post('/assign-ticket/{id}', function (int $id) {
        $user   = auth()->user();
        $ticket = \App\Models\Ticket::where('id', $id)
            ->where('organization_id', $user->organization_id)
            ->first();
        if (! $ticket || $ticket->status === 'closed') {
            return response()->json(['error' => 'No encontrado'], 404);
        }
        $agentName = $user->name ?? 'Agente';
        $ticket->update(['status' => 'human', 'assigned_agent' => $agentName, 'agent_called_at' => null]);
        \App\Models\Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'system',
            'content'     => 'Agente se unió a la conversación.',
        ]);
        $inboxUrl = route('filament.admin.pages.live-inbox') . '?ticket=' . $id;
        return response()->json(['ok' => true, 'inbox_url' => $inboxUrl]);
    });

    // Rechazar ticket — respeta flujo agent_no_response configurado en el widget
    Route::post('/reject-ticket/{id}', function (int $id) {
        $user   = auth()->user();
        $ticket = \App\Models\Ticket::where('id', $id)
            ->where('organization_id', $user->organization_id)
            ->first();
        if (! $ticket) {
            return response()->json(['error' => 'No encontrado'], 404);
        }
        $noResponse = $ticket->widget?->agent_no_response ?? 'bot';
        $ticket->update(['status' => 'bot', 'assigned_agent' => null, 'agent_called_at' => null]);
        \App\Models\Message::create(['ticket_id' => $ticket->id, 'sender_type' => 'system', 'content' => 'Agente no disponible.']);
        // Solo mensaje del bot si el flujo es "volver al bot" — si es "ticket", el widget muestra el formulario
        if ($noResponse !== 'ticket') {
            \App\Models\Message::create(['ticket_id' => $ticket->id, 'sender_type' => 'bot', 'content' => 'En este momento no hay agentes disponibles. Puedo seguir ayudándote. ¿En qué más puedo asistirte?']);
        }
        return response()->json(['ok' => true]);
    });
});

// ── WP Plugin connect (OAuth-like popup) ─────────────────────────────────────
Route::get('/connect',                [WpConnectController::class, 'show'])->name('wp-connect.show');
Route::post('/connect/authorize',     [WpConnectController::class, 'authorize'])->name('wp-connect.authorize');
Route::get('/wp-authorize/{token}',   [WpAuthorizeController::class, 'show'])->name('wp-authorize.show');
Route::post('/wp-authorize/confirm',  [WpAuthorizeController::class, 'confirm'])->name('wp-authorize.confirm');

// MercadoPago return pages
Route::get('/payment/mp/success', [PaymentController::class, 'mpSuccess']);
Route::get('/payment/mp/failure', [PaymentController::class, 'mpFailure']);
Route::get('/payment/mp/pending', [PaymentController::class, 'mpPending']);
