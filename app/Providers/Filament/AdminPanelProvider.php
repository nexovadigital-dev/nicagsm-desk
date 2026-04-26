<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\TwoFactorAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\Facades\Storage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('app')
            ->login(\App\Filament\Pages\CustomLogin::class)
            ->brandName('Nexova Desk Edge')
            ->brandLogo(null)
            ->brandLogoHeight('0')
            ->favicon(asset('images/nexovadesklogo.svg'))
            ->colors([
                'primary' => Color::Emerald,
                'gray'    => Color::Zinc,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::System)
            ->when(config('app.spa_mode', true), fn (Panel $p) => $p->spa())
            ->sidebarCollapsibleOnDesktop()
            ->topNavigation(false)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->navigationGroups([
                NavigationGroup::make('Conversaciones')->icon('heroicon-o-inbox-stack')->collapsible(false),
                NavigationGroup::make('Widget')->icon('heroicon-o-chat-bubble-oval-left-ellipsis')->collapsible(false),
                NavigationGroup::make('Inteligencia')->icon('heroicon-o-cpu-chip')->collapsible(false),
                NavigationGroup::make('Agentes')->icon('heroicon-o-users')->collapsible(false),
                NavigationGroup::make('Canales')->icon('heroicon-o-signal')->collapsible(false),
                NavigationGroup::make('Correo y Notificaciones')->icon('heroicon-o-envelope')->collapsible(false),
                NavigationGroup::make('Cuenta')->icon('heroicon-o-user-circle')->collapsible(false),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\DebugPanelEntry::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                TwoFactorAuthentication::class,
                \App\Http\Middleware\SetOrganizationTimezone::class,
            ])
            ->renderHook(
                PanelsRenderHook::PAGE_START,
                function (): string {
                    $user = auth()->user();

                    // ── Impersonation banner ──────────────────────────────────
                    if (session()->has('superadmin_impersonating')) {
                        return <<<HTML
<div style="position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;
            display:flex;align-items:center;gap:10px;
            background:#0f172a;border:1px solid rgba(34,197,94,.4);
            border-radius:99px;padding:8px 8px 8px 16px;
            box-shadow:0 8px 32px rgba(0,0,0,.25),0 0 0 1px rgba(34,197,94,.15);
            font-family:Inter,system-ui,sans-serif;
            white-space:nowrap;pointer-events:all">
    <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;flex-shrink:0;box-shadow:0 0 6px #22c55e"></span>
    <span style="font-size:12.5px;font-weight:500;color:#cbd5e1">
        Impersonando como <strong style="color:#f1f5f9">{$user?->name}</strong>
    </span>
    <a href="/nx-hq/stop-impersonate"
       style="display:inline-flex;align-items:center;gap:5px;
              background:#22c55e;color:#0d1117;
              padding:5px 14px;border-radius:99px;
              font-size:12px;font-weight:700;text-decoration:none;
              transition:background .15s;margin-left:4px"
       onmouseover="this.style.background='#16a34a'" onmouseout="this.style.background='#22c55e'">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Salir
    </a>
</div>
HTML;
                    }

                    if (! $user || ! $user->organization_id) return '';

                    $org = $user->organization;
                    if (! $org || $org->plan !== 'trial' || ! $org->trial_ends_at) return '';

                    $daysLeft = (int) now()->diffInDays($org->trial_ends_at, false);
                    if ($daysLeft < 0) {
                        return <<<'HTML'
<div style="background:#fef2f2;border-bottom:1px solid #fecaca;padding:9px 20px;font-size:13px;color:#991b1b;display:flex;align-items:center;gap:8px">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    Tu periodo de prueba ha expirado. <a href="/admin/subscription" style="font-weight:700;color:#991b1b;text-decoration:underline;margin-left:4px">Actualiza al plan Pro</a> para continuar usando Nexova Desk.
</div>
HTML;
                    }

                    if ($daysLeft > 7) return '';

                    return <<<HTML
<div style="background:#fff7ed;border-bottom:1px solid #fed7aa;padding:9px 20px;font-size:13px;color:#92400e;display:flex;align-items:center;gap:8px">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Tu prueba gratuita termina en <strong style="margin:0 2px">{$daysLeft} día(s)</strong>. <a href="/admin/subscription" style="font-weight:700;color:#92400e;text-decoration:underline;margin-left:4px">Ver planes</a>
</div>
HTML;
                }
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
<!-- ── Nexova Custom Toast ── -->
<style>
@keyframes nxToastIn  { from { opacity:0; transform:translateY(10px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }
@keyframes nxToastOut { from { opacity:1; transform:translateY(0) scale(1); }        to { opacity:0; transform:translateY(6px) scale(.97); } }
.nx-toast        { animation: nxToastIn .22s cubic-bezier(.16,1,.3,1) forwards; }
.nx-toast.exiting { animation: nxToastOut .18s ease forwards !important; pointer-events:none; }
.fi-no { display: none !important; }

/* ── Skeleton loader ── */
@keyframes nx-shimmer {
    0%   { background-position: -600px 0; }
    100% { background-position: 600px 0; }
}
#nx-skeleton {
    position: fixed;
    left: var(--nx-sk-left, 256px); right: 0; top: 0; bottom: 0;
    background: var(--fi-color-white, #fff);
    z-index: 8888;
    padding: 32px 36px;
    display: none;
    flex-direction: column;
    gap: 20px;
    pointer-events: none;
}
/* skeleton always light */
#nx-skeleton.visible { display: flex; }
.nx-sk-line {
    height: 14px; border-radius: 7px;
    background: linear-gradient(90deg, rgba(128,128,128,.1) 25%, rgba(128,128,128,.2) 50%, rgba(128,128,128,.1) 75%);
    background-size: 600px 100%;
    animation: nx-shimmer 1.4s ease-in-out infinite;
}
.nx-sk-block {
    height: 80px; border-radius: 10px;
    background: linear-gradient(90deg, rgba(128,128,128,.08) 25%, rgba(128,128,128,.14) 50%, rgba(128,128,128,.08) 75%);
    background-size: 600px 100%;
    animation: nx-shimmer 1.4s ease-in-out infinite;
}
</style>

<!-- Skeleton overlay -->
<div id="nx-skeleton">
    <div class="nx-sk-line" style="width:38%;height:20px;margin-bottom:8px"></div>
    <div class="nx-sk-line" style="width:22%;height:12px;opacity:.6"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-top:8px">
        <div class="nx-sk-block"></div>
        <div class="nx-sk-block"></div>
        <div class="nx-sk-block"></div>
        <div class="nx-sk-block"></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px">
        <div class="nx-sk-line" style="width:100%"></div>
        <div class="nx-sk-line" style="width:88%"></div>
        <div class="nx-sk-line" style="width:74%"></div>
        <div class="nx-sk-line" style="width:91%"></div>
        <div class="nx-sk-line" style="width:65%"></div>
    </div>
    <div class="nx-sk-block" style="height:120px;margin-top:4px"></div>
    <div style="display:flex;flex-direction:column;gap:10px">
        <div class="nx-sk-line" style="width:100%"></div>
        <div class="nx-sk-line" style="width:80%"></div>
        <div class="nx-sk-line" style="width:60%"></div>
    </div>
</div>
<script>
(function() {
    const sk = document.getElementById('nx-skeleton');
    if (!sk) return;
    let timer = null;

    function show() {
        // Small delay so instant navigations don't flash the skeleton
        timer = setTimeout(() => sk.classList.add('visible'), 80);
    }
    function hide() {
        clearTimeout(timer);
        sk.classList.remove('visible');
    }

    // Adjust left offset based on sidebar state
    function syncLeft() {
        const sidebar = document.querySelector('.fi-sidebar');
        if (!sidebar) return;
        const collapsed = sidebar.offsetWidth < 100; // collapsed = icon-only ~64px
        document.documentElement.style.setProperty('--nx-sk-left', collapsed ? '64px' : '256px');
    }

    function getMain() {
        return document.querySelector('.fi-main') || document.querySelector('.fi-page-wrap');
    }

    let isNavigating = false;

    // SALIDA — blur + fade antes de navegar, nx-preload para evitar flash de tema
    document.addEventListener('livewire:navigate', () => {
        if (isNavigating) return;
        isNavigating = true;
        document.documentElement.classList.add('nx-preload');
        const main = getMain();
        if (main) {
            main.style.transition = 'opacity .18s ease, transform .18s ease, filter .18s ease';
            main.style.opacity = '0';
            main.style.transform = 'translateY(5px)';
            main.style.filter = 'blur(3px)';
        }
    });

    document.addEventListener('livewire:navigating', () => { syncLeft(); show(); });

    // ENTRADA — limpia blur y fade-in
    document.addEventListener('livewire:navigated', () => {
        hide();
        const main = getMain();
        if (!main) { isNavigating = false; return; }

        if (isNavigating) {
            // Llegamos vía navegación SPA — animar entrada
            main.style.transition = 'none';
            main.style.opacity = '0';
            main.style.transform = 'translateY(7px)';
            main.style.filter = 'blur(3px)';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    main.style.transition = 'opacity .24s ease, transform .24s ease, filter .24s ease';
                    main.style.opacity = '1';
                    main.style.transform = 'translateY(0)';
                    main.style.filter = 'blur(0)';
                    isNavigating = false;
                });
            });
        } else {
            // Carga inicial — solo asegurar que el contenido sea visible
            main.style.opacity = '1';
            main.style.transform = '';
            main.style.filter = '';
            isNavigating = false;
        }
    });

    document.addEventListener('livewire:request-succeeded', hide);
    // Keep left in sync when sidebar toggles
    const obs = new MutationObserver(syncLeft);
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.fi-sidebar');
        if (sidebar) obs.observe(sidebar, { attributes: true, attributeFilter: ['class', 'style'] });
    });
})();
</script>
<div id="nx-toast-ctn"
     x-data="{
         toasts: [],
         add(detail) {
             const id = Date.now() + Math.random();
             this.toasts.push({ id, type: detail.type||'info', message: detail.message||'', exiting: false });
             setTimeout(() => this.dismiss(id), detail.duration||3500);
         },
         dismiss(id) {
             const t = this.toasts.find(x => x.id===id);
             if (!t || t.exiting) return;
             t.exiting = true;
             setTimeout(() => this.toasts = this.toasts.filter(x => x.id!==id), 200);
         }
     }"
     @nexova-toast.window="add($event.detail)"
     style="position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column-reverse;gap:8px;align-items:flex-end;pointer-events:none">
    <template x-for="t in toasts" :key="t.id">
        <div class="nx-toast"
             :class="t.exiting ? 'exiting' : ''"
             :style="`
                 background: var(--c-surface,#fff);
                 border: 1px solid var(--c-border,#e3e6ea);
                 border-left: 3px solid ${t.type==='success'?'#22c55e':t.type==='error'?'#ef4444':t.type==='warning'?'#f59e0b':'#64748b'};
                 border-radius: 10px;
                 padding: 10px 10px 10px 12px;
                 display: flex; align-items: center; gap: 9px;
                 min-width: 210px; max-width: 310px;
                 box-shadow: 0 4px 20px rgba(0,0,0,.09);
                 pointer-events: all;
                 font-family: Inter, system-ui, sans-serif;
             `">
            <span :style="`color:${t.type==='success'?'#22c55e':t.type==='error'?'#ef4444':t.type==='warning'?'#f59e0b':'#64748b'};flex-shrink:0;display:flex`">
                <template x-if="t.type==='success'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></template>
                <template x-if="t.type==='error'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></template>
                <template x-if="t.type==='warning'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></template>
                <template x-if="t.type==='info'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></template>
            </span>
            <span x-text="t.message" style="font-size:12.5px;font-weight:500;color:var(--c-text,#111827);flex:1;line-height:1.35"></span>
            <button @click="dismiss(t.id)"
                    style="display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;padding:4px;border-radius:5px;flex-shrink:0;opacity:.4;transition:opacity .1s,background .1s;color:var(--c-sub,#6b7280)"
                    @mouseover="$el.style.opacity=1;$el.style.background='var(--c-surf2,#f0f2f5)'"
                    @mouseout="$el.style.opacity=.4;$el.style.background='transparent'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>


<!-- ── Admin: sonidos + push notifications globales ── -->
<script>
(function () {
    // ── Web Audio para admin ──
    function playAdminSound() {
        try {
            const Ctx  = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            const ctx  = new Ctx();
            const gain = ctx.createGain();
            gain.connect(ctx.destination);

            // Tres tonos ascendentes (notificación profesional)
            [[0, 880], [0.12, 1046], [0.24, 1318]].forEach(([delay, freq]) => {
                const osc = ctx.createOscillator();
                osc.connect(gain);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
                gain.gain.setValueAtTime(0.12, ctx.currentTime + delay);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
                osc.start(ctx.currentTime + delay);
                osc.stop(ctx.currentTime + delay + 0.3);
            });

            setTimeout(() => ctx.close(), 1500);
        } catch (e) {}
    }

    // ── Browser Push Notification ──
    function requestNotifPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    function showNotification(title, body) {
        if (!('Notification' in window) || Notification.permission !== 'granted') return;
        try {
            const n = new Notification(title, {
                body,
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: 'nexova-chat-msg',
                requireInteraction: false,
            });
            n.onclick = () => { window.focus(); n.close(); };
            setTimeout(() => n.close(), 6000);
        } catch (e) {}
    }

    // ── Polling de nuevos mensajes ──
    let lastSince = new Date().toISOString();
    let pollActive = false;

    async function pollNewMessages() {
        if (pollActive) return;
        pollActive = true;
        try {
            const res  = await fetch('/api/admin/new-events?since=' + encodeURIComponent(lastSince));
            const data = await res.json();
            lastSince  = data.server_time || new Date().toISOString();

            if (data.count > 0) {
                playAdminSound();
                const evt  = data.messages[0];
                const name = evt.conversation_name || 'Visitante';
                // Diferenciar: escalación vs. mensaje normal del cliente
                const isEscalation = evt.event_type === 'escalation';
                const title = isEscalation
                    ? '🔔 Asistencia solicitada · ' + name
                    : 'Mensaje de cliente · ' + name;
                const body = evt.content.length > 60 ? evt.content.slice(0, 60) + '…' : evt.content;
                showNotification(title, body);

                // Emitir evento para que LiveInbox pueda reaccionar
                document.dispatchEvent(new CustomEvent('nexova:new-message', { detail: data }));
            }
        } catch (e) {}
        pollActive = false;
    }

    // Iniciar cuando la página cargue
    document.addEventListener('DOMContentLoaded', function () {
        requestNotifPermission();
        setInterval(pollNewMessages, 5000);

        // Re-pedir permiso en primer click si fue rechazado por política del navegador
        document.addEventListener('click', function onFirstClick() {
            requestNotifPermission();
            document.removeEventListener('click', onFirstClick);
        }, { once: true });
    });

    // SPA: si DOMContentLoaded ya pasó, sólo pedir permiso — el polling ya corre
    if (document.readyState !== 'loading') {
        requestNotifPermission();
    }
})();
</script>

<!-- ── Premium Theme Switcher — Alpine component ── -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('nxTheme', () => ({
        cur:          localStorage.getItem('nx-theme') || 'auto',
        popover:      false,
        popoverStyle: '',

        get modeLabel() {
            return { light: 'Claro', dark: 'Oscuro', auto: 'Automático' }[this.cur] ?? 'Automático';
        },

        init() {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (this.cur === 'auto') this._apply('auto');
            });
        },

        togglePopover(el) {
            if (this.popover) { this.popover = false; return; }
            const rect = el.getBoundingClientRect();
            const left = Math.max(8, Math.min(rect.left - 4, window.innerWidth - 252));
            this.popoverStyle = `bottom:${window.innerHeight - rect.top + 10}px;left:${left}px;`;
            this.popover = true;
        },

        _apply(v) {
            // Silent apply — no animation (used for init / SPA re-assertion)
            const dark = v === 'dark' || (v === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            try { localStorage.setItem('_x_colorMode', dark ? 'dark' : 'light'); } catch(e) {}
        },

        set(v) {
            this.cur = v;
            localStorage.setItem('nx-theme', v);
            // Animated apply — only on explicit user action
            const dark = v === 'dark' || (v === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.add('nx-switching');
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
            try { localStorage.setItem('_x_colorMode', dark ? 'dark' : 'light'); } catch(e) {}
            setTimeout(() => document.documentElement.classList.remove('nx-switching'), 260);
            this.popover = false;
        }
    }));
});
</script>

<!-- ── Theme persistence — survives SPA nav & Filament Alpine re-init ── -->
<script>
(function(){
    function nxAssertTheme() {
        var s = localStorage.getItem('nx-theme') || 'auto';
        var dark = s === 'dark' || (s === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
        try { localStorage.setItem('_x_colorMode', dark ? 'dark' : 'light'); } catch(e) {}
    }
    // After ALL Alpine components (including Filament's fi-color-mode-switcher) have init'd
    document.addEventListener('alpine:initialized', nxAssertTheme);
    // After each Livewire SPA navigation: assert theme + remove nx-preload after double-RAF
    document.addEventListener('livewire:navigated', function() {
        requestAnimationFrame(function() {
            requestAnimationFrame(function() {
                nxAssertTheme();
                document.documentElement.classList.remove('nx-preload');
            });
        });
    });
})();
</script>

<!-- ── Alerta global de solicitud de agente (aparece en cualquier página del panel) ── -->
<div id="nx-agent-alert"
     style="display:none;position:fixed;bottom:24px;right:24px;z-index:9999;
            background:#fff;border:1px solid #e2e8f0;border-radius:14px;
            box-shadow:0 8px 32px rgba(0,0,0,.14);padding:16px 18px;
            min-width:300px;max-width:340px;
            animation:nxToastIn .25s cubic-bezier(.16,1,.3,1) forwards">
    <div style="display:flex;align-items:flex-start;gap:12px">
        <div style="width:36px;height:36px;border-radius:10px;background:#fef3c7;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 00-5-5.917V4a1 1 0 10-2 0v1.083A6 6 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:700;color:#111827" id="nx-agent-alert-name">Visitante solicita agente</div>
            <div style="font-size:11.5px;color:#6b7280;margin-top:2px">Haz clic en Aceptar para tomar el chat</div>
        </div>
        <button onclick="nxAgentAlertDismiss()" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;line-height:1;font-size:16px">✕</button>
    </div>
    <div style="display:flex;gap:8px;margin-top:12px">
        <button id="nx-agent-alert-accept"
                style="flex:1;background:#22c55e;color:#fff;border:none;border-radius:8px;
                       padding:8px 0;font-size:12.5px;font-weight:600;cursor:pointer">
            ✓ Aceptar chat
        </button>
        <button id="nx-agent-alert-reject"
                style="flex:1;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;
                       padding:8px 0;font-size:12.5px;font-weight:600;cursor:pointer">
            ✕ Rechazar
        </button>
    </div>
</div>

<script>
(function() {
    var alertEl     = document.getElementById('nx-agent-alert');
    var alertName   = document.getElementById('nx-agent-alert-name');
    var btnAccept   = document.getElementById('nx-agent-alert-accept');
    var btnReject   = document.getElementById('nx-agent-alert-reject');
    var currentTicketId = null;
    var pollTimer   = null;
    var dismissed   = {};

    function show(ticketId, clientName) {
        if (dismissed[ticketId]) return;
        currentTicketId = ticketId;
        alertName.textContent = (clientName || 'Visitante') + ' solicita un agente';
        alertEl.style.display = 'block';
        playAlertSound();
    }

    function hide() {
        alertEl.style.display = 'none';
        currentTicketId = null;
    }

    window.nxAgentAlertDismiss = function() {
        if (currentTicketId) dismissed[currentTicketId] = true;
        hide();
    };

    var audioCtx = null;
    function playAlertSound() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            [880, 1100].forEach(function(freq, i) {
                var o = audioCtx.createOscillator();
                var g = audioCtx.createGain();
                o.connect(g); g.connect(audioCtx.destination);
                o.type = 'sine'; o.frequency.value = freq;
                var t = audioCtx.currentTime + i * 0.15;
                g.gain.setValueAtTime(0, t);
                g.gain.linearRampToValueAtTime(0.15, t + 0.05);
                g.gain.linearRampToValueAtTime(0, t + 0.3);
                o.start(t); o.stop(t + 0.35);
            });
        } catch(e) {}
    }

    function poll() {
        fetch('/api/panel/incoming-agent-calls', { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' } })
            .then(function(r) { return r.ok ? r.json() : null; })
            .then(function(data) {
                if (!data || !data.calls || data.calls.length === 0) { hide(); return; }
                var first = data.calls[0];
                if (currentTicketId !== first.id || alertEl.style.display === 'none') {
                    show(first.id, first.client_name);
                }
            })
            .catch(function(){});
    }

    // Botón Aceptar — llama a assignToMe vía API interna
    btnAccept.addEventListener('click', function() {
        if (!currentTicketId) return;
        var id = currentTicketId;
        fetch('/api/panel/assign-ticket/' + id, {
            method: 'POST',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
        }).then(function(r) {
            return r.json();
        }).then(function(d) {
            hide();
            dismissed[id] = true;
            // Navegar al live inbox con ese ticket
            if (d.inbox_url) window.location.href = d.inbox_url;
        }).catch(function(){});
    });

    // Botón Rechazar — devuelve al bot
    btnReject.addEventListener('click', function() {
        if (!currentTicketId) return;
        var id = currentTicketId;
        fetch('/api/panel/reject-ticket/' + id, {
            method: 'POST',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
        }).then(function() {
            dismissed[id] = true;
            hide();
        }).catch(function(){});
    });

    // Iniciar polling cada 8 segundos
    function start() {
        if (pollTimer) return;
        poll();
        pollTimer = setInterval(poll, 8000);
    }

    // Arrancar cuando el usuario esté autenticado en el panel
    if (document.cookie.indexOf('laravel_session') !== -1 || document.cookie.indexOf('XSRF-TOKEN') !== -1) {
        start();
    }
    // También arrancar tras navegaciones SPA
    document.addEventListener('livewire:navigated', function() {
        if (!pollTimer) start();
    });
})();
</script>
HTML
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                function (): string {
                    $org     = auth()->user()?->organization;
                    $orgName = addslashes($org?->name ?? '');
                    return <<<HTML
<!-- ── Theme init — NO flash, runs before render ── -->
<script>
(function(){
    var s = localStorage.getItem('nx-theme') || 'auto';
    var dark = s === 'dark' || (s === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    // nx-preload disables all transitions during initial paint
    document.documentElement.classList.add('nx-preload');
    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.style.colorScheme = dark ? 'dark' : 'light';
    // Sync Filament's own key so it doesn't override us on Alpine init
    try { localStorage.setItem('_x_colorMode', dark ? 'dark' : 'light'); } catch(e) {}
    // Remove preload after first paint so transitions work normally
    requestAnimationFrame(function(){ requestAnimationFrame(function(){ document.documentElement.classList.remove('nx-preload'); }); });
})();
</script>
<script>
(function(){
    var ORG = '{$orgName}';
    var APP = 'Nexova Desk Edge';
    var SEP = ' \u2014 ';
    var busy = false;

    function fmt() {
        if (busy) return;
        var t = document.title;
        if (!t || t.indexOf(APP) === -1) return; // Filament aún no lo añadió
        if (ORG && t.indexOf(ORG) !== -1) return; // Ya tiene el org name
        busy = true;
        // Filament pone: "Página - Nexova Desk Edge"
        // Queremos:      "Página — Nexova Desk Edge — OrgName"
        document.title = t
            .replace(' - ' + APP, SEP + APP)  // normaliza separador
            .replace(SEP + APP, SEP + APP + (ORG ? SEP + ORG : ''));
        busy = false;
    }

    document.addEventListener('DOMContentLoaded', function(){ setTimeout(fmt, 50); });
    document.addEventListener('livewire:navigated', function(){ setTimeout(fmt, 50); });
})();
</script>
<style>
/* ── Global mobile overrides ── */
@media (max-width: 768px) {
    /* Filament native tables (Contacts, Agents, etc.) */
    .fi-ta-table-ctn { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
    .fi-ta-table { min-width: 480px; font-size: 12.5px !important; }
    .fi-ta-cell { padding: 8px 10px !important; }
    /* Filament header padding */
    .fi-main-ctn { padding: 8px !important; }
    .fi-page { padding: 0 !important; }
    /* Prevent iOS zoom on input focus (font-size >= 16px) */
    input[type="text"], input[type="email"], input[type="tel"],
    input[type="search"], textarea, select { font-size: 16px !important; }
    /* Filament filters / toolbar */
    .fi-ta-header-toolbar { flex-wrap: wrap !important; gap: 6px !important; }
    /* Toast position on mobile */
    #nx-toast-ctn { right: 8px !important; bottom: 12px !important; }
}
</style>
HTML;
                })
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn (): string => <<<'HTML'
<style>
/* Ocultar la cabecera de marca por defecto de Filament */
.fi-sidebar-header { display: none !important; }

.nx-brand {
    padding: 14px 10px 12px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 2px;
}
.nx-brand a {
    display: flex; align-items: center; gap: 10px;
    padding: 6px 8px; border-radius: 8px;
    text-decoration: none; transition: background .15s;
}
.nx-brand a:hover { background: #f1f5f9; }
.nx-brand img {
    height: 28px; width: 28px;
    border-radius: 7px; object-fit: contain; flex-shrink: 0;
}
.nx-brand-name {
    font-size: 14px; letter-spacing: -.02em;
    flex: 1; min-width: 0;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    line-height: 1;
}
.nx-brand-name strong { font-weight: 800; color: #111827; }
.nx-brand-name span   { font-weight: 800; color: #22c55e; }
.nx-brand-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #22c55e; flex-shrink: 0;
    box-shadow: 0 0 0 2px rgba(34,197,94,.2);
}
.nx-brand-badge {
    min-width: 18px; height: 18px; border-radius: 99px;
    background: #1e293b; color: #f8fafc;
    font-size: 9.5px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    padding: 0 5px; flex-shrink: 0;
}
</style>
<div class="nx-brand"
     x-data="{ count: 0 }"
     x-init="
         const load = async () => {
             try { const r = await fetch('/api/admin/unread-count'); const d = await r.json(); count = d.count || 0; } catch(e) {}
         };
         load(); setInterval(load, 8000);
     ">
    <a href="/app">
        <img src="/images/nexovadeskicon.png" alt="Nexova Desk">
        <span class="nx-brand-name"><strong>Nexova</strong> <span>Desk</span></span>
        <span class="nx-brand-badge" x-show="count > 0" x-text="count > 99 ? '99+' : count" style="display:none"></span>
        <span class="nx-brand-dot" x-show="count === 0"></span>
    </a>
</div>
HTML
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                function (): string {
                    $user       = auth()->user();
                    $name       = e($user?->name ?? 'Usuario');
                    $email      = e($user?->email ?? '');
                    $initial    = strtoupper(mb_substr($user?->name ?? 'U', 0, 1));
                    $roleMap    = ['owner'=>'Propietario','admin'=>'Admin','agent'=>'Agente'];
                    $roleLabel  = $roleMap[$user?->role ?? 'agent'] ?? 'Agente';
                    $logoutUrl  = route('filament.admin.auth.logout');
                    $profileUrl = '/app/agent-profile';
                    $csrfToken  = csrf_token();
                    $avatarUrl  = $user?->avatar_path ? e(Storage::url($user->avatar_path)) : null;
                    $avatarHtml = $avatarUrl
                        ? "<img src=\"{$avatarUrl}\" alt=\"\" style=\"width:100%;height:100%;object-fit:cover;border-radius:7px\">"
                        : $initial;
                    return <<<HTML
<style>
/* ── Sidebar user panel — light sidebar ── */
.nx-sf { padding: 4px 8px 6px; }

.nx-sf-panel {
    position: fixed; z-index: 9999;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px; overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.06);
}
.nx-sf-panel-email {
    padding: 10px 12px 9px;
    font-size: 11px; font-weight: 500; color: #9ca3af;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    border-bottom: 1px solid #f3f4f6;
}
.nx-sf-sep { height: 1px; background: #f3f4f6; margin: 2px 0; }
.nx-sf-item {
    display: flex; align-items: center; gap: 8px;
    width: 100%; padding: 8px 12px;
    font-size: 12.5px; font-weight: 500; color: #374151;
    background: none; border: none; cursor: pointer;
    font-family: inherit; text-decoration: none;
    transition: background .1s, color .1s;
    text-align: left; line-height: 1; box-sizing: border-box;
}
.nx-sf-item:hover { background: #f9fafb; color: #111827; }
.nx-sf-item svg { flex-shrink: 0; color: #9ca3af; }
.nx-sf-item-arrow { margin-left: auto; opacity: .4; display: flex; align-items: center; }
.nx-sf-item--danger { color: #dc2626; }
.nx-sf-item--danger:hover { background: rgba(220,38,38,.05); color: #b91c1c; }
.nx-sf-item--danger svg { color: #dc2626; }

.nx-sf-trigger {
    display: flex; align-items: center; gap: 9px;
    width: 100%; padding: 6px 8px;
    background: none; border: none; cursor: pointer;
    font-family: inherit; text-align: left;
    border-radius: 7px; transition: background .12s;
}
.nx-sf-trigger:hover { background: #e2e8f0; }

.nx-sf-panel-user {
    display: flex; align-items: center; gap: 9px;
    width: 100%; padding: 8px 10px;
    background: none; border: none; cursor: pointer;
    font-family: inherit; text-align: left;
    transition: background .1s;
    border-top: 1px solid #f3f4f6;
}
.nx-sf-panel-user:hover { background: #f9fafb; }

.nx-sf-avatar {
    width: 28px; height: 28px; border-radius: 7px;
    background: #1e293b; color: #ffffff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; overflow: hidden;
}
.nx-sf-info { flex: 1; min-width: 0; }
.nx-sf-name {
    font-size: 12px; font-weight: 600; color: #111827;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    line-height: 1.3;
}
.nx-sf-role {
    font-size: 10.5px; color: #9ca3af;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    line-height: 1.2; margin-top: 1px;
}
.nx-sf-dots {
    display: flex; align-items: center; justify-content: center;
    width: 20px; height: 20px; border-radius: 4px; flex-shrink: 0;
    color: #9ca3af; transition: color .1s;
}
.nx-sf-trigger:hover .nx-sf-dots { color: #374151; }

/* ── Inline appearance row inside panel ── */
.nx-sf-appearance {
    display: flex; align-items: center; justify-content: space-between;
    padding: 7px 12px 8px;
}
.nx-sf-app-label {
    font-size: 11px; font-weight: 600; color: #9ca3af;
}
.nx-sf-app-opts {
    display: flex; gap: 3px;
}
.nx-sf-app-opt {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 26px; border-radius: 6px;
    border: 1px solid transparent;
    background: transparent; color: #9ca3af;
    cursor: pointer; transition: background .1s, color .1s, border-color .1s;
}
.nx-sf-app-opt:hover { background: #f1f5f9; color: #374151; }
.nx-sf-app-opt.nx-sf-app-on {
    background: rgba(34,197,94,.1);
    border-color: rgba(34,197,94,.3);
    color: #16a34a;
}
</style>

<div class="nx-sf"
     x-data="{
         open: false,
         ps: '',
         toggle() {
             if (this.open) { this.open = false; return; }
             const t = this.\$refs.trigger;
             const s = document.querySelector('.fi-sidebar');
             const tr = t.getBoundingClientRect();
             const sl = s ? s.getBoundingClientRect().left : 0;
             const sw = s ? s.getBoundingClientRect().width : 256;
             const gap = 6;
             this.ps = 'bottom:' + (window.innerHeight - tr.top + gap) + 'px;left:' + (sl + gap) + 'px;width:' + (sw - gap*2) + 'px';
             this.open = true;
         }
     }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

    <div class="nx-sf-panel"
         x-show="open"
         x-cloak
         :style="ps"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="nx-sf-panel-email">{$email}</div>

        <a href="{$profileUrl}" class="nx-sf-item" @click="open=false">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Mi perfil
        </a>

        <a href="/" class="nx-sf-item" target="_blank" @click="open=false">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Ir al sitio web
            <span class="nx-sf-item-arrow">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
        </a>

        <!-- ── Appearance / Theme inline ── -->
        <div class="nx-sf-sep"></div>
        <div class="nx-sf-appearance" x-data="nxTheme()">
            <span class="nx-sf-app-label">Apariencia</span>
            <div class="nx-sf-app-opts">
                <button type="button" class="nx-sf-app-opt" :class="cur==='light' ? 'nx-sf-app-on' : ''" @click="set('light')" title="Claro">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="13" height="13"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                </button>
                <button type="button" class="nx-sf-app-opt" :class="cur==='dark' ? 'nx-sf-app-on' : ''" @click="set('dark')" title="Oscuro">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="13" height="13"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <button type="button" class="nx-sf-app-opt" :class="cur==='auto' ? 'nx-sf-app-on' : ''" @click="set('auto')" title="Automático">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="13" height="13"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                </button>
            </div>
        </div>

        <div class="nx-sf-sep"></div>

        <form method="POST" action="{$logoutUrl}" style="margin:0">
            <input type="hidden" name="_token" value="{$csrfToken}">
            <button type="submit" class="nx-sf-item nx-sf-item--danger">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar sesión
            </button>
        </form>

        <div class="nx-sf-panel-user" @click="open = false" style="cursor:pointer">
            <div class="nx-sf-avatar">{$avatarHtml}</div>
            <div class="nx-sf-info">
                <div class="nx-sf-name">{$name}</div>
                <div class="nx-sf-role">{$roleLabel}</div>
            </div>
        </div>
    </div>

    <button class="nx-sf-trigger" type="button" x-ref="trigger" @click="toggle()">
        <div class="nx-sf-avatar">{$avatarHtml}</div>
        <div class="nx-sf-info">
            <div class="nx-sf-name">{$name}</div>
            <div class="nx-sf-role">{$roleLabel}</div>
        </div>
        <div class="nx-sf-dots">
            <svg fill="currentColor" viewBox="0 0 20 20" width="14" height="14"><circle cx="10" cy="4" r="1.5"/><circle cx="10" cy="10" r="1.5"/><circle cx="10" cy="16" r="1.5"/></svg>
        </div>
    </button>
</div>
HTML;
                });
    }
}