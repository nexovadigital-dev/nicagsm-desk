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
            ->brandName('Nexova Desk')
            ->brandLogo(asset('images/nexovadesklogo.svg'))
            ->darkModeBrandLogo(asset('images/nexovadesklogo.svg'))
            ->brandLogoHeight('5rem')
            ->favicon(asset('images/nexovadesklogo.svg'))
            ->colors([
                'primary' => Color::Emerald,
                'gray'    => Color::Zinc,
            ])
            ->font('Inter')
            ->defaultThemeMode(ThemeMode::Dark)
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
<!-- ── Nexova Super-Admin Toast + Progress (same as user panel) ── -->
<style>
@keyframes nxToastIn  { from { opacity:0; transform:translateY(10px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }
@keyframes nxToastOut { from { opacity:1; transform:translateY(0) scale(1); }        to { opacity:0; transform:translateY(6px) scale(.97); } }
.nx-toast         { animation: nxToastIn .22s cubic-bezier(.16,1,.3,1) forwards; }
.nx-toast.exiting { animation: nxToastOut .18s ease forwards !important; pointer-events:none; }
#nx-progress-bar {
    position:fixed; top:0; left:0; height:2px; width:0%;
    background:linear-gradient(90deg,#22c55e,#4ade80);
    z-index:99999; transition:width .3s ease,opacity .4s ease;
    pointer-events:none;
}
.nx-progress-done { opacity:0 !important; transition:width .1s,opacity .5s .1s !important; }
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
                 background: var(--c-surface,#1e2330);
                 border: 1px solid var(--c-border,#2d3348);
                 border-left: 3px solid ${t.type==='success'?'#22c55e':t.type==='error'?'#ef4444':t.type==='warning'?'#f59e0b':'#22c55e'};
                 border-radius: 10px;
                 padding: 10px 10px 10px 12px;
                 display: flex; align-items: center; gap: 9px;
                 min-width: 210px; max-width: 310px;
                 box-shadow: 0 4px 24px rgba(0,0,0,.3);
                 pointer-events: all;
                 font-family: Inter, system-ui, sans-serif;
             `">
            <span :style="`color:${t.type==='success'?'#22c55e':t.type==='error'?'#ef4444':t.type==='warning'?'#f59e0b':'#22c55e'};flex-shrink:0;display:flex`">
                <template x-if="t.type==='success'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></template>
                <template x-if="t.type==='error'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></template>
                <template x-if="t.type==='warning'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></template>
                <template x-if="t.type==='info'"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></template>
            </span>
            <span x-text="t.message" style="font-size:12.5px;font-weight:500;color:var(--c-text,#f1f5f9);flex:1;line-height:1.35"></span>
            <button @click="dismiss(t.id)"
                    style="display:flex;align-items:center;justify-content:center;background:none;border:none;cursor:pointer;padding:4px;border-radius:5px;flex-shrink:0;opacity:.4;transition:opacity .1s;color:var(--c-sub,#94a3b8)"
                    @mouseover="$el.style.opacity=1" @mouseout="$el.style.opacity=.4">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<div id="nx-progress-bar"></div>
<script>
(function () {
    const bar = document.getElementById('nx-progress-bar');
    if (!bar) return;
    let timer;
    function start() { bar.style.opacity='1'; bar.style.width='15%'; clearTimeout(timer); timer=setTimeout(()=>{bar.style.width='70%';},400); }
    function done()  { clearTimeout(timer); bar.style.width='100%'; setTimeout(()=>bar.classList.add('nx-progress-done'),20); setTimeout(()=>{bar.classList.remove('nx-progress-done');bar.style.width='0%';},600); }
    document.addEventListener('livewire:navigate-start', start);
    document.addEventListener('livewire:navigated',      done);
    document.addEventListener('livewire:request',  start);
    document.addEventListener('livewire:response', done);
})();
</script>
HTML
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_FOOTER,
                fn (): string => <<<'HTML'
<div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,.07)">
    <a href="/"
       style="display:flex;align-items:center;gap:9px;padding:8px 12px;border-radius:8px;font-size:13px;font-weight:600;color:rgba(255,255,255,.45);text-decoration:none;transition:background .15s,color .15s"
       onmouseover="this.style.background='rgba(255,255,255,.05)';this.style.color='rgba(255,255,255,.8)'"
       onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,.45)'">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Ir al sitio web
    </a>
</div>
HTML
            );
    }
}
