<x-filament-panels::page>
<style>
/* ── Layout ── */
.inst-page { width: 100%; max-width: 1060px; padding: 24px 20px 64px; display: flex; flex-direction: column; gap: 16px; box-sizing: border-box; }
.inst-layout { display: grid; grid-template-columns: 196px 1fr; gap: 16px; align-items: start; }

/* ── Sidebar — estilo Tremor neutral ── */
.inst-sidebar { background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e2e8f0); border-radius: 12px; padding: 6px; overflow: hidden; }
.inst-sidebar-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .07em; padding: 8px 12px 4px; display: block; }
.inst-platform-btn {
    display: flex; align-items: center; gap: 9px; width: 100%; padding: 9px 12px;
    border-radius: 7px; border: none; border-left: 2px solid transparent;
    background: transparent; cursor: pointer; font-size: 13px; font-weight: 500;
    color: #64748b; font-family: inherit; transition: background .12s, color .12s; text-align: left;
}
.inst-platform-btn:hover { background: rgba(15,23,42,.05); color: #1e293b; }
.inst-platform-btn.active { background: rgba(15,23,42,.07); border-left-color: #475569; color: #1e293b; font-weight: 600; }
.inst-platform-icon { width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.inst-platform-icon img { width: 18px; height: 18px; object-fit: contain; display: block; }
.inst-divider { height: 1px; background: #f1f5f9; margin: 5px 8px; }

/* ── Widget selector ── */
.inst-widget-selector {
    background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e2e8f0);
    border-radius: 12px; padding: 14px 18px; display: flex; align-items: center;
    gap: 12px; flex-wrap: wrap;
}
.inst-widget-selector label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
.inst-widget-select {
    flex: 1; min-width: 180px; max-width: 360px;
    border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 11px;
    font-size: 13px; font-family: inherit; color: #1e293b;
    background: #f8fafc; outline: none; transition: border-color .15s;
}
.inst-widget-select:focus { border-color: #475569; }
.inst-widget-token {
    font-family: ui-monospace,monospace; font-size: 10.5px; color: #64748b;
    background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px;
    padding: 4px 10px; word-break: break-all; max-width: 260px;
}
.inst-no-widgets { font-size: 13px; color: #94a3b8; display: flex; align-items: center; gap: 8px; }

/* ── Card principal ── */
.inst-card {
    background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e2e8f0);
    border-radius: 12px; padding: 24px 26px;
    display: flex; flex-direction: column; gap: 0; min-width: 0;
}
.inst-card-title { font-size: 17px; font-weight: 700; color: #0f172a; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.inst-card-sub { font-size: 13px; color: #64748b; line-height: 1.65; margin-bottom: 22px; }

/* ── Steps ── */
.inst-step { display: flex; gap: 13px; margin-bottom: 20px; min-width: 0; }
.inst-step-num { width: 24px; height: 24px; border-radius: 50%; background: #1e293b; color: #f8fafc; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }
.inst-step-num.star { background: #fef3c7; color: #92400e; font-size: 13px; border: 1px solid #fde68a; }
.inst-step-body { flex: 1; min-width: 0; }
.inst-step-title { font-size: 13.5px; font-weight: 600; color: #0f172a; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.inst-step-text { font-size: 13px; color: #64748b; line-height: 1.65; }
.inst-step-text code, .inst-step-title code { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 4px; padding: 1px 5px; font-size: 11.5px; color: #334155; }

/* ── Bloques de código ── */
.inst-code-block {
    position: relative; background: #0f172a; border: 1px solid #1e293b;
    border-radius: 9px; padding: 14px 52px 14px 16px; margin: 10px 0;
    font-family: ui-monospace,'Cascadia Code','Fira Code',monospace; font-size: 11.5px;
    color: #e2e8f0; white-space: pre; overflow-x: auto; line-height: 1.7;
    max-width: 100%; box-sizing: border-box;
}
.inst-copy-btn {
    position: absolute; top: 9px; right: 9px;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 9px; background: rgba(255,255,255,.09); color: #94a3b8;
    border: 1px solid rgba(255,255,255,.12); border-radius: 6px;
    font-size: 10.5px; font-weight: 600; cursor: pointer; font-family: inherit;
    transition: background .12s, color .12s; white-space: nowrap;
}
.inst-copy-btn:hover { background: rgba(255,255,255,.17); color: #e2e8f0; }

/* ── Badges ── */
.inst-badge-ok   { display: inline-flex; align-items: center; gap: 4px; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 3px 10px; border-radius: 99px; font-size: 10.5px; font-weight: 700; }
.inst-badge-soon { display: inline-flex; align-items: center; gap: 4px; background: #fffbeb; color: #92400e; border: 1px solid #fde68a; padding: 3px 10px; border-radius: 99px; font-size: 10.5px; font-weight: 700; }
.inst-badge-woo  { display: inline-flex; align-items: center; gap: 4px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 3px 10px; border-radius: 99px; font-size: 10.5px; font-weight: 700; }

/* ── Tip & info boxes ── */
.inst-tip { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; font-size: 12.5px; color: #475569; line-height: 1.65; margin-top: 6px; }
.inst-tip strong { color: #1e293b; }
.inst-tip code { background: #e2e8f0; border-radius: 4px; padding: 1px 5px; font-size: 11px; color: #334155; }
.inst-woo-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 14px 16px; font-size: 12.5px; color: #0c4a6e; line-height: 1.7; margin-top: 6px; }
.inst-woo-box strong { color: #075985; }

/* ── Responsive ── */
.fi-page-header, .fi-breadcrumbs { display: none !important; }

@media (max-width: 860px) {
    .inst-layout { grid-template-columns: 1fr; }
    .inst-sidebar { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 4px; padding: 10px; }
    .inst-sidebar-label { grid-column: 1 / -1; }
    .inst-platform-btn { border-left: none; border-bottom: 2px solid transparent; }
    .inst-platform-btn.active { border-left-color: transparent; border-bottom-color: #475569; }
}
@media (max-width: 560px) {
    .inst-page { padding: 16px 12px 48px; }
    .inst-card { padding: 18px 16px; }
    .inst-widget-token { display: none; }
    .inst-code-block { font-size: 10.5px; padding: 12px 48px 12px 12px; }
}
</style>

@php
$widgets    = $this->orgWidgets();
$selWidget  = $this->getSelectedWidgetModel();
$embedCode  = $this->getEmbedCode();
$wooCode    = $this->getWooCommerceSnippet();
$reactCode  = $this->getReactCode();
$appUrl     = rtrim(config('app.url'), '/');

$platforms = [
    ['id' => 'any',        'label' => 'Cualquier web',   'img' => null],
    ['id' => 'wordpress',  'label' => 'WordPress',       'img' => 'https://cdn.simpleicons.org/wordpress/21759B'],
    ['id' => 'shopify',    'label' => 'Shopify',         'img' => 'https://cdn.simpleicons.org/shopify/96BF48'],
    ['id' => 'wix',        'label' => 'Wix',             'img' => 'https://cdn.simpleicons.org/wix/0C6EFC'],
    ['id' => 'laravel',    'label' => 'Laravel / PHP',   'img' => 'https://cdn.simpleicons.org/laravel/FF2D20'],
    ['id' => 'react',      'label' => 'React / Next.js', 'img' => 'https://cdn.simpleicons.org/react/61DAFB'],
    ['id' => 'squarespace','label' => 'Squarespace',     'img' => 'https://cdn.simpleicons.org/squarespace/222222'],
];
@endphp

<div class="inst-page">

    <h1 style="font-size:22px;font-weight:700;letter-spacing:-.02em;margin-bottom:0">Instalación del Widget</h1>

    {{-- ── Selector de widget ── --}}
    <div class="inst-widget-selector">
        <label>Widget a instalar</label>
        @if($widgets->isEmpty())
            <div class="inst-no-widgets">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                No tienes widgets creados. <a href="{{ \App\Filament\Resources\ChatWidgetResource::getUrl('create') }}" style="color:#3b82f6;text-decoration:underline">Crea tu primer widget</a>
            </div>
        @else
            <select class="inst-widget-select" wire:model.live="selectedWidget">
                @foreach($widgets as $w)
                    <option value="{{ $w->id }}">{{ $w->name }}{{ $w->is_active ? '' : ' (inactivo)' }}</option>
                @endforeach
            </select>
            @if($selWidget)
                <span class="inst-widget-token" title="Token del widget">{{ $selWidget->token }}</span>
            @endif
        @endif
    </div>

    <div class="inst-layout">

        {{-- ── Sidebar ── --}}
        <div class="inst-sidebar">
            <div class="inst-sidebar-label">Plataforma</div>
            @foreach($platforms as $p)
            <button type="button"
                class="inst-platform-btn {{ $activePlatform === $p['id'] ? 'active' : '' }}"
                wire:click="setPlatform('{{ $p['id'] }}')">
                <span class="inst-platform-icon">
                    @if($p['img'])
                        <img src="{{ $p['img'] }}" alt="" width="20" height="20" loading="lazy" onerror="this.style.opacity=.3">
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.8" stroke-linecap="round" width="20" height="20">
                            <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    @endif
                </span>
                {{ $p['label'] }}
            </button>
            @endforeach
        </div>

        {{-- ── Panel de instrucciones ── --}}
        <div class="inst-card">

            @if(!$selWidget && !$widgets->isEmpty())
                <p style="font-size:13px;color:var(--c-sub,#9ca3af)">Selecciona un widget arriba para ver el código de instalación.</p>

            @elseif($activePlatform === 'any')
            {{-- ════ CUALQUIER WEB ════ --}}
            <div class="inst-card-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" width="22" height="22"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Cualquier sitio web
            </div>
            <p class="inst-card-sub">Compatible con cualquier plataforma que soporte HTML. Pega el siguiente código antes del cierre de <code>&lt;/body&gt;</code>.</p>

            <div class="inst-step">
                <div class="inst-step-num">1</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Copia el código de instalación</div>
                    <div class="inst-step-text">Agrega esto antes de <code>&lt;/body&gt;</code> en cada página donde quieras el chat:</div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $embedCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                </div>
            </div>

            <div class="inst-step">
                <div class="inst-step-num">2</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Verifica la instalación</div>
                    <div class="inst-step-text">Abre tu sitio. El botón flotante del chat debería aparecer en la esquina configurada.</div>
                    <div style="margin-top:8px"><span class="inst-badge-ok">✓ Sin dependencias — funciona en cualquier HTML</span></div>
                </div>
            </div>

            <div class="inst-tip">
                <strong>¿Qué hace <code>widgetToken</code>?</strong> — Identifica cuál de tus widgets cargar, con su configuración, bot, colores y preguntas frecuentes. Cada widget tiene su propio token único.
            </div>

            @elseif($activePlatform === 'wordpress')
            {{-- ════ WORDPRESS / WOOCOMMERCE ════ --}}
            <div class="inst-card-title">
                <img src="https://cdn.simpleicons.org/wordpress/21759B" width="22" height="22" style="border-radius:4px">
                WordPress / WooCommerce
            </div>
            <p class="inst-card-sub">Elige el método de instalación. Si usas WooCommerce, el Método B reconoce automáticamente al cliente logueado y lo vincula con sus conversaciones.</p>

            {{-- Método A: script simple --}}
            <div class="inst-step">
                <div class="inst-step-num">A</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Método A — Script HTML (rápido, cualquier WordPress)</div>
                    <div class="inst-step-text">Instala el plugin gratuito <strong>"Insert Headers and Footers"</strong> desde WordPress.org → <em>Ajustes → Insert H&amp;F → Scripts in Footer</em>. Pega:</div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $embedCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                    <div style="margin-top:6px"><span class="inst-badge-ok">✓ Compatible con todos los temas</span></div>
                </div>
            </div>

            <div class="inst-divider" style="margin:4px 0 18px"></div>

            {{-- Método B: WooCommerce --}}
            <div class="inst-step">
                <div class="inst-step-num">B</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">
                        Método B — WooCommerce (reconocimiento de cliente)
                        <span class="inst-badge-woo">WooCommerce</span>
                    </div>
                    <div class="inst-step-text">Agrega este código en <code>functions.php</code> de tu tema hijo (child theme). Cuando el cliente esté logueado en WooCommerce, el chat lo reconocerá automáticamente y vinculará su historial de conversaciones.</div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $wooCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                    <div class="inst-woo-box">
                        <strong>¿Cómo funciona?</strong><br>
                        El código genera un <strong>HMAC-SHA256</strong> firmado con el token de tu widget. El sistema verifica la firma y asocia al visitante con su cuenta de WooCommerce de forma segura, sin exponer datos sensibles.<br><br>
                        <strong>Clientes no logueados</strong> — el chat funciona igualmente, sin identidad pre-cargada. El agente puede identificarlos manualmente durante la conversación.
                    </div>
                </div>
            </div>

            <div class="inst-divider" style="margin:4px 0 18px"></div>

            {{-- Plugin próximamente --}}
            <div class="inst-step">
                <div class="inst-step-num star">★</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Plugin oficial Nexova Chat <span class="inst-badge-soon">Próximamente</span></div>
                    <div class="inst-step-text">Estamos desarrollando un plugin nativo de WordPress/WooCommerce que instalará y configurará el widget directamente desde el panel de WordPress, con soporte completo para datos de clientes, pedidos y más.</div>
                </div>
            </div>

            @elseif($activePlatform === 'shopify')
            {{-- ════ SHOPIFY ════ --}}
            <div class="inst-card-title">
                <img src="https://cdn.simpleicons.org/shopify/96BF48" width="22" height="22">
                Shopify
            </div>
            <p class="inst-card-sub">Agrega el widget al tema de Shopify editando el archivo <code>theme.liquid</code>.</p>

            <div class="inst-step">
                <div class="inst-step-num">1</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Accede al editor de código del tema</div>
                    <div class="inst-step-text">En tu panel de Shopify: <em>Tienda en línea → Temas → ⋯ → Editar código</em>.</div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">2</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Abre <code>layout/theme.liquid</code></div>
                    <div class="inst-step-text">Busca la etiqueta <code>&lt;/body&gt;</code> y pega el código justo antes:</div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $embedCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">3</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Guarda y verifica</div>
                    <div class="inst-step-text">Haz clic en <em>Guardar</em> y abre tu tienda en una pestaña nueva. El widget debe aparecer en todas las páginas.</div>
                    <div style="margin-top:8px"><span class="inst-badge-ok">✓ Compatible con todos los temas Shopify</span></div>
                </div>
            </div>
            <div class="inst-tip">El widget se carga de forma <strong>asíncrona</strong> — no afecta el tiempo de carga de tu tienda.</div>

            @elseif($activePlatform === 'wix')
            {{-- ════ WIX ════ --}}
            <div class="inst-card-title">
                <img src="https://cdn.simpleicons.org/wix/0C6EFC" width="22" height="22">
                Wix
            </div>
            <p class="inst-card-sub">Wix permite inyectar código personalizado mediante su panel de configuración.</p>

            <div class="inst-step">
                <div class="inst-step-num">1</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Ve a Configuración → Avanzado → Inyección de código</div>
                    <div class="inst-step-text">En el panel de Wix: <em>Configuración → Avanzado → Inyección de código</em>. También puedes encontrarlo en <em>Marketing → Herramientas de Marketing → Píxeles de seguimiento → +Añadir</em>.</div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">2</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Pega en la sección "Pie de página" (Body End)</div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $embedCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">3</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Publica los cambios</div>
                    <div class="inst-step-text">Haz clic en <em>Aplicar → Publicar</em>. El widget aparecerá en todas las páginas de tu sitio Wix.</div>
                </div>
            </div>
            <div class="inst-tip"><strong>Nota Wix:</strong> Si el widget no carga, verifica que tu plan Wix permite inyección de código personalizado (Wix Business o superior).</div>

            @elseif($activePlatform === 'laravel')
            {{-- ════ LARAVEL / PHP ════ --}}
            <div class="inst-card-title">
                <img src="https://cdn.simpleicons.org/laravel/FF2D20" width="22" height="22">
                Laravel / PHP
            </div>
            <p class="inst-card-sub">Agrega el widget en tu layout principal de Laravel o cualquier plantilla PHP.</p>

            <div class="inst-step">
                <div class="inst-step-num">1</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Laravel Blade — layout principal</div>
                    <div class="inst-step-text">Abre <code>resources/views/layouts/app.blade.php</code> (o tu layout principal) y pega antes de <code>&lt;/body&gt;</code>:</div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $embedCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">2</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">PHP puro (sin framework)</div>
                    <div class="inst-step-text">Si usas PHP sin framework, pega el mismo código en tu archivo de plantilla principal antes del cierre <code>&lt;/body&gt;</code>.</div>
                    <div style="margin-top:8px"><span class="inst-badge-ok">✓ Compatible con Laravel, Symfony, CodeIgniter, PHP puro</span></div>
                </div>
            </div>
            <div class="inst-tip"><strong>Tip:</strong> Para pasar datos del usuario autenticado, puedes extender el config antes del script:<br><code>window.NexovaChatConfig.customer = { name: "@{{ auth()->user()->name }}", email: "@{{ auth()->user()->email }}" };</code></div>

            @elseif($activePlatform === 'react')
            {{-- ════ REACT / NEXT.JS ════ --}}
            <div class="inst-card-title">
                <img src="https://cdn.simpleicons.org/react/61DAFB" width="22" height="22">
                React / Next.js
            </div>
            <p class="inst-card-sub">Carga el widget como un componente reutilizable en tu app React o Next.js.</p>

            <div class="inst-step">
                <div class="inst-step-num">1</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Crea el componente <code>NexovaChat.jsx</code></div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $reactCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">2</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Úsalo en tu layout raíz</div>
                    <div class="inst-step-text">En <code>app/layout.tsx</code> (Next.js 13+) o <code>_app.jsx</code>:</div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">import NexovaChat from '@/components/NexovaChat';

export default function RootLayout({ children }) {
  return (
    &lt;html&gt;
      &lt;body&gt;
        {children}
        &lt;NexovaChat /&gt;
      &lt;/body&gt;
    &lt;/html&gt;
  );
}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">3</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Next.js: usa <code>'use client'</code></div>
                    <div class="inst-step-text">Si usas App Router (Next.js 13+), agrega <code>'use client';</code> al principio del componente ya que usa <code>useEffect</code>.</div>
                    <div style="margin-top:8px"><span class="inst-badge-ok">✓ Compatible con React 16+, Next.js 12/13/14, Remix, Vite</span></div>
                </div>
            </div>

            @elseif($activePlatform === 'squarespace')
            {{-- ════ SQUARESPACE ════ --}}
            <div class="inst-card-title">
                <img src="https://cdn.simpleicons.org/squarespace/222222" width="22" height="22" style="border-radius:4px;background:#eee">
                Squarespace
            </div>
            <p class="inst-card-sub">Squarespace permite inyectar código personalizado desde su panel de configuración.</p>

            <div class="inst-step">
                <div class="inst-step-num">1</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Ve a Configuración → Avanzado → Inyección de código</div>
                    <div class="inst-step-text">En el panel de Squarespace: <em>Configuración → Avanzado → Inyección de código</em>.</div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">2</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Pega en la sección <em>Pie de página</em></div>
                    <div class="inst-code-block" x-data="{{ '{}' }}">{{ $embedCode }}<button class="inst-copy-btn" @click="navigator.clipboard.writeText($el.previousSibling?.textContent ?? ''); $el.textContent='✓ Copiado'">📋 Copiar</button></div>
                </div>
            </div>
            <div class="inst-step">
                <div class="inst-step-num">3</div>
                <div class="inst-step-body">
                    <div class="inst-step-title">Guarda y publica</div>
                    <div class="inst-step-text">Haz clic en <em>Guardar</em> y abre tu sitio. El widget aparecerá automáticamente en todas las páginas.</div>
                    <div style="margin-top:8px"><span class="inst-badge-ok">✓ Compatible — Squarespace Business y superior</span></div>
                </div>
            </div>
            <div class="inst-tip"><strong>Requisito:</strong> La función de inyección de código solo está disponible en los planes Business, Commerce Basic y Commerce Advanced de Squarespace.</div>

            @endif

        </div>{{-- /inst-card --}}
    </div>{{-- /inst-layout --}}
</div>
</x-filament-panels::page>
