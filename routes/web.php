<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\AcceptInvitation;
use App\Livewire\Auth\ForgotPassword;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\WpConnectController;

// ── Auth público ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register',           Register::class)->name('auth.register');
    Route::get('/login',              Login::class)->name('auth.login');
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
Route::get('/', function () {
    $plans        = \App\Models\Plan::where('is_active', true)->where('is_public', true)->orderBy('sort')->get();
    $latestPosts  = \App\Models\Post::published()->orderByDesc('published_at')->limit(3)->get();
    return view('landing', compact('plans', 'latestPosts'));
});

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

// ── Super-admin impersonation return ─────────────────────────────────────────
Route::get('/nx-hq/stop-impersonate', function () {
    $adminId = session('superadmin_impersonating');
    if (! $adminId) {
        return redirect('/app');
    }
    auth()->logout();
    session()->forget('superadmin_impersonating');
    auth()->loginUsingId($adminId);
    return redirect('/nx-hq');
})->middleware('web');

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

// ── WP Plugin connect (OAuth-like popup) ─────────────────────────────────────
Route::get('/connect',           [WpConnectController::class, 'show'])->name('wp-connect.show');
Route::post('/connect/authorize',[WpConnectController::class, 'authorize'])->name('wp-connect.authorize');

// MercadoPago return pages
Route::get('/payment/mp/success', [PaymentController::class, 'mpSuccess']);
Route::get('/payment/mp/failure', [PaymentController::class, 'mpFailure']);
Route::get('/payment/mp/pending', [PaymentController::class, 'mpPending']);
