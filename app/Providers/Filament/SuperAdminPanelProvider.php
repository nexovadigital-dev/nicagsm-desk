<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\SuperAdmin\Pages\SuperDashboard;
use App\Filament\SuperAdmin\Pages\Organizations;
use App\Filament\SuperAdmin\Pages\PlansManager;
use App\Filament\SuperAdmin\Pages\PaymentConfigPage;
use App\Filament\SuperAdmin\Pages\TransactionsPage;
use App\Filament\SuperAdmin\Pages\WidgetsOverview;
use App\Filament\SuperAdmin\Pages\SystemSettingsPage;
use App\Filament\SuperAdmin\Pages\SuperAdminProfilePage;
use App\Filament\SuperAdmin\Pages\MailConfigPage;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Enums\ThemeMode;
use Filament\Navigation\NavigationGroup;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;
use Filament\View\PanelsRenderHook;

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('superadmin')
            ->path('nx-hq')
            ->login()
            ->brandName('')
            ->brandLogo(null)
            ->brandLogoHeight('0')
            ->favicon(asset('images/nexovadesklogo.svg'))
            ->colors([
                'primary' => Color::Emerald,
                'gray'    => Color::Zinc,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::Light)
            ->sidebarCollapsibleOnDesktop()
            ->topNavigation(false)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->navigationGroups([
                NavigationGroup::make('Visión General')->collapsible(false),
                NavigationGroup::make('Clientes')->collapsible(false),
                NavigationGroup::make('Planes & Pagos')->collapsible(false),
                NavigationGroup::make('Sistema')->collapsible(false),
            ])
            ->pages([
                SuperDashboard::class,
                Organizations::class,
                PlansManager::class,
                PaymentConfigPage::class,
                TransactionsPage::class,
                SystemSettingsPage::class,
                SuperAdminProfilePage::class,
                MailConfigPage::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => <<<'HTML'
<script>
(function(){
    localStorage.removeItem('theme');
    document.documentElement.classList.remove('dark');
    document.documentElement.classList.add('light');
    document.documentElement.style.colorScheme = 'light';
})();
</script>
<style>
@keyframes nxToastIn  { from { opacity:0; transform:translateY(10px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }
@keyframes nxToastOut { from { opacity:1; transform:translateY(0) scale(1); }        to { opacity:0; transform:translateY(6px) scale(.97); } }
.nx-toast         { animation: nxToastIn .22s cubic-bezier(.16,1,.3,1) forwards; }
.nx-toast.exiting { animation: nxToastOut .18s ease forwards !important; pointer-events:none; }
</style>
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
                 background: #fff;
                 border: 1px solid #e2e8f0;
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
            <span x-text="t.message" style="font-size:12.5px;font-weight:500;color:#111827;flex:1;line-height:1.35"></span>
            <button @click="dismiss(t.id)"
                    style="display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;padding:4px;border-radius:5px;flex-shrink:0;opacity:.4;transition:opacity .1s,background .1s;color:#6b7280"
                    @mouseover="$el.style.opacity=1;$el.style.background='#f1f5f9'" @mouseout="$el.style.opacity=.4;$el.style.background='transparent'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
HTML
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn (): string => <<<'HTML'
<style>
.nx-hq-brand { padding:12px 10px 10px; border-bottom:1px solid #e5e7eb; margin-bottom:2px; }
.nx-hq-brand a { display:flex;align-items:center;gap:9px;padding:6px 8px;border-radius:6px;text-decoration:none;transition:background .15s; }
.nx-hq-brand a:hover { background:#e2e8f0; }
.nx-hq-brand img { height:26px;width:auto;flex-shrink:0; }
.nx-hq-brand-name { font-size:13.5px;font-weight:700;letter-spacing:-.02em;flex:1;min-width:0;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.nx-hq-badge { display:inline-flex;align-items:center;padding:2px 7px;background:#f1f5f9;color:#374151;font-size:10px;font-weight:700;border-radius:99px;flex-shrink:0; }
</style>
<div class="nx-hq-brand">
    <a href="/nx-hq">
        <img src="/images/nexovadesklogo.svg" alt="Nexova Desk">
        <span class="nx-hq-brand-name">Nexova Desk</span>
        <span class="nx-hq-badge">HQ</span>
    </a>
</div>
HTML
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                function (): string {
                    $user      = auth()->user();
                    $name      = e($user?->name ?? 'SuperAdmin');
                    $email     = e($user?->email ?? '');
                    $initial   = strtoupper(mb_substr($user?->name ?? 'S', 0, 1));
                    $logoutUrl = route('filament.superadmin.auth.logout');
                    $profileUrl = '/nx-hq/super-admin-profile-page';
                    $csrfToken = csrf_token();
                    return <<<HTML
<style>
.nx-hq-sf { padding:4px 8px 6px; }
.nx-hq-panel {
    position:fixed;z-index:9999;background:#fff;
    border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;
    box-shadow:0 8px 30px rgba(0,0,0,.12),0 2px 8px rgba(0,0,0,.06);
}
.nx-hq-panel-email { padding:10px 12px 9px;font-size:11px;font-weight:500;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;border-bottom:1px solid #f3f4f6; }
.nx-hq-sep { height:1px;background:#f3f4f6;margin:2px 0; }
.nx-hq-item { display:flex;align-items:center;gap:8px;width:100%;padding:8px 12px;font-size:12.5px;font-weight:500;color:#374151;background:none;border:none;cursor:pointer;font-family:inherit;text-decoration:none;transition:background .1s,color .1s;text-align:left;line-height:1;box-sizing:border-box; }
.nx-hq-item:hover { background:#f9fafb;color:#111827; }
.nx-hq-item svg { flex-shrink:0;color:#9ca3af; }
.nx-hq-item--danger { color:#dc2626; }
.nx-hq-item--danger:hover { background:rgba(220,38,38,.05);color:#b91c1c; }
.nx-hq-item--danger svg { color:#dc2626; }
.nx-hq-trigger { display:flex;align-items:center;gap:9px;width:100%;padding:6px 8px;background:none;border:none;cursor:pointer;font-family:inherit;text-align:left;border-radius:7px;transition:background .12s; }
.nx-hq-trigger:hover { background:#e2e8f0; }
.nx-hq-avatar { width:28px;height:28px;border-radius:7px;background:#0f172a;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.nx-hq-info { flex:1;min-width:0; }
.nx-hq-uname { font-size:12px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.3; }
.nx-hq-role { font-size:10.5px;color:#9ca3af;line-height:1.2;margin-top:1px; }
.nx-hq-dots { display:flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:4px;flex-shrink:0;color:#9ca3af;transition:color .1s; }
.nx-hq-trigger:hover .nx-hq-dots { color:#374151; }
</style>
<div class="nx-hq-sf"
     x-data="{ open:false, ps:'', toggle() { if(this.open){this.open=false;return;} const t=this.\$refs.trigger,s=document.querySelector('.fi-sidebar'),tr=t.getBoundingClientRect(),sl=s?s.getBoundingClientRect().left:0,sw=s?s.getBoundingClientRect().width:256,g=6; this.ps='bottom:'+(window.innerHeight-tr.top+g)+'px;left:'+(sl+g)+'px;width:'+(sw-g*2)+'px'; this.open=true; } }"
     @click.outside="open=false" @keydown.escape.window="open=false">
    <div class="nx-hq-panel" x-show="open" x-cloak :style="ps"
         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="nx-hq-panel-email">{$email}</div>
        <a href="{$profileUrl}" class="nx-hq-item" @click="open=false">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Mi perfil &amp; 2FA
        </a>
        <a href="/" class="nx-hq-item" target="_blank" @click="open=false">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Ver sitio web
        </a>
        <div class="nx-hq-sep"></div>
        <form method="POST" action="{$logoutUrl}" style="margin:0">
            <input type="hidden" name="_token" value="{$csrfToken}">
            <button type="submit" class="nx-hq-item nx-hq-item--danger">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar sesión
            </button>
        </form>
    </div>
    <button class="nx-hq-trigger" type="button" x-ref="trigger" @click="toggle()">
        <div class="nx-hq-avatar">{$initial}</div>
        <div class="nx-hq-info">
            <div class="nx-hq-uname">{$name}</div>
            <div class="nx-hq-role">Super Admin</div>
        </div>
        <div class="nx-hq-dots">
            <svg fill="currentColor" viewBox="0 0 20 20" width="14" height="14"><circle cx="10" cy="4" r="1.5"/><circle cx="10" cy="10" r="1.5"/><circle cx="10" cy="16" r="1.5"/></svg>
        </div>
    </button>
</div>
HTML;
                });
    }
}
