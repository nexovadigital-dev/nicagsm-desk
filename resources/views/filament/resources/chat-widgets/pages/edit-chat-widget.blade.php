<x-filament-panels::page>
<style>
/* ── Layout ── */
.wc-page { max-width: 1100px; margin: 0 auto; padding: 16px 0 60px; display: grid; grid-template-columns: 1fr; gap: 0; }
@media (min-width: 1024px) {
    .wc-page { grid-template-columns: 1fr 300px; gap: 0; align-items: start; }
}
.wc-left-col  { display: flex; flex-direction: column; }
.wc-right-col { position: sticky; top: 16px; display: flex; flex-direction: column; gap: 14px; padding-left: 18px; }

/* ── Section (accordion) ── */
.nx-section { background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea); border-radius: 14px; margin-bottom: 10px; overflow: hidden; }
.nx-section-hd { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; cursor: pointer; user-select: none; }
.nx-section-hd:hover { background: var(--c-bg,#f8f9fa); }
.nx-section-title { font-size: 14px; font-weight: 700; color: var(--c-text,#111); }
.nx-section-body  { padding: 0 20px 20px; border-top: 1px solid var(--c-border,#e3e6ea); }
.nx-section-row { display: flex; align-items: center; gap: 12px; padding: 13px 0; border-bottom: 1px solid var(--c-border,#e3e6ea); cursor: pointer; }
.nx-section-row:last-child { border-bottom: none; padding-bottom: 0; }
.nx-section-row-icon { width: 32px; height: 32px; border-radius: 8px; background: var(--c-bg,#f5f6f8); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nx-section-row-label { font-size: 13px; font-weight: 500; color: var(--c-text,#1f2937); flex: 1; }
.nx-section-row-sub { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 1px; }
.nx-section-row-badge { font-size: 11px; font-weight: 600; color: var(--c-sub,#6b7280); padding: 2px 8px; border-radius: 99px; background: var(--c-bg,#f5f6f8); }

/* ── Fields ── */
.wc-field { display: flex; flex-direction: column; gap: 5px; }
.wc-label { font-size: 11px; font-weight: 600; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }
.wc-input, .wc-select { background: var(--c-bg,#f5f6f8); border: 1.5px solid var(--c-border,#e3e6ea); border-radius: 8px; color: var(--c-text,#111); font-size: 13px; padding: 9px 12px; outline: none; width: 100%; font-family: inherit; transition: border-color .12s; box-sizing: border-box; }
.wc-input:focus, .wc-select:focus { border-color: var(--c-accent,#22c55e); }
.wc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

/* ── Icon circles (Chatway style) ── */
.nx-icon-row { display: flex; gap: 14px; flex-wrap: wrap; padding-top: 14px; }
.nx-icon-opt { display: flex; flex-direction: column; align-items: center; gap: 7px; cursor: pointer; }
.nx-icon-circle { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all .15s; }
.nx-icon-radio { width: 16px; height: 16px; border-radius: 50%; border: 2px solid var(--c-border,#d1d5db); background: transparent; display: flex; align-items: center; justify-content: center; transition: all .15s; flex-shrink: 0; }
.nx-icon-radio.checked { border-color: var(--c-accent,#22c55e); background: var(--c-accent,#22c55e); }
.nx-icon-radio.checked::after { content:''; width: 6px; height: 6px; border-radius: 50%; background: #fff; }
.nx-icon-label { font-size: 10px; color: var(--c-sub,#6b7280); font-weight: 500; }

/* ── Color swatches ── */
.nx-color-grid { display: flex; gap: 8px; flex-wrap: wrap; padding-top: 12px; }
.nx-swatch { width: 40px; height: 40px; border-radius: 50%; cursor: pointer; border: 3px solid transparent; transition: all .12s; position: relative; }
.nx-swatch.active { box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor; }
.nx-swatch-custom { display: flex; align-items: center; justify-content: center; border: 2px dashed var(--c-border,#d1d5db); background: transparent; position: relative; }
.nx-swatch-custom input[type=color] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; border: none; padding: 0; }

/* ── Toggle ── */
.wc-toggle { position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0; }
.wc-toggle input { opacity: 0; width: 0; height: 0; }
.wc-slider { position: absolute; cursor: pointer; inset: 0; background: var(--c-border,#d1d5db); border-radius: 99px; transition: background .2s; }
.wc-slider:before { content:''; position: absolute; height: 16px; width: 16px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: transform .2s; }
.wc-toggle input:checked + .wc-slider { background: #22c55e; }
.wc-toggle input:checked + .wc-slider:before { transform: translateX(18px); }

/* ── Segmented control ── */
.wc-seg { display: flex; background: var(--c-bg,#f5f6f8); border: 1.5px solid var(--c-border,#e3e6ea); border-radius: 9px; padding: 3px; gap: 2px; }
.wc-seg-btn { flex: 1; padding: 7px 10px; border-radius: 7px; font-size: 12px; font-weight: 500; border: none; background: transparent; color: var(--c-sub,#6b7280); cursor: pointer; font-family: inherit; transition: background .12s, color .12s; }
.wc-seg-btn.active { background: var(--c-surface,#fff); color: var(--c-text,#111); box-shadow: 0 1px 4px rgba(0,0,0,.1); }

/* ── Preview message cards ── */
.nx-pm-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding-top: 12px; }
.nx-pm-card { border-radius: 10px; border: 2px solid var(--c-border,#e3e6ea); cursor: pointer; overflow: hidden; transition: border-color .15s; }
.nx-pm-card.active { border-color: #22c55e; }
.nx-pm-card-body { background: #f5f6f8; padding: 14px 10px 10px; position: relative; min-height: 90px; display: flex; align-items: flex-end; justify-content: flex-end; }
.nx-pm-card-foot { padding: 8px 12px; display: flex; align-items: center; gap: 6px; }
.nx-pm-card-radio { width: 14px; height: 14px; border-radius: 50%; border: 2px solid var(--c-border,#d1d5db); flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.nx-pm-card-radio.checked { border-color: #22c55e; background: #22c55e; }
.nx-pm-card-radio.checked::after { content:''; width: 5px; height: 5px; border-radius: 50%; background: #fff; }
.nx-pm-card-lbl { font-size: 11.5px; font-weight: 600; color: var(--c-text,#111); }
.nx-pm-card-sub { font-size: 10px; color: var(--c-sub,#6b7280); margin-top: 1px; line-height: 1.3; }

/* ── Buttons ── */
.wc-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1.5px solid transparent; font-family: inherit; transition: all .12s; }
.wc-btn-primary { background: #22c55e; color:#fff; border-color: #22c55e; }
.wc-btn-primary:hover { background: #16a34a; border-color: #16a34a; }
.wc-btn-ghost { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.wc-btn-ghost:hover { background: var(--c-bg,#f5f6f8); }
.wc-color-row { display: flex; align-items: center; gap: 10px; }
.wc-color-picker { width: 40px; height: 36px; border: 1.5px solid var(--c-border,#e3e6ea); border-radius: 8px; cursor: pointer; padding: 3px; background: none; }

/* ── Item row (FAQ, Social channels) ── */
.wc-item-row { display: flex; gap: 10px; align-items: flex-start; padding: 12px; background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 9px; }
.wc-item-fields { flex: 1; display: flex; flex-direction: column; gap: 8px; }
.pcb-icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; border: none; cursor: pointer; background: transparent; color: var(--c-sub,#6b7280); transition: background .12s, color .12s; }
.pcb-icon-btn:hover { background: var(--c-surf2,#f0f2f5); color: var(--c-text,#111); }
.pcb-icon-btn.danger:hover { background: rgba(220,38,38,.08); color: #dc2626; }
.pcb-add-btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 11px; border-radius: 9px; border: 1.5px dashed var(--c-border,#d1d5db); background: transparent; color: var(--c-sub,#9ca3af); font-size: 13px; font-weight: 500; cursor: pointer; font-family: inherit; transition: border-color .15s, color .15s, background .15s; margin-top: 8px; }
.pcb-add-btn:hover { border-color: var(--c-text,#374151); color: var(--c-text,#374151); background: var(--c-bg,#f8f9fa); }
.pcb-field { background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 10px; overflow: hidden; transition: border-color .15s; margin-top: 6px; }
.pcb-field:hover { border-color: #22c55e; }
.pcb-field-header { display: flex; align-items: center; gap: 10px; padding: 11px 14px; cursor: pointer; border-bottom: 1px solid transparent; transition: border-color .15s; }
.pcb-field-header.open { border-bottom-color: var(--c-border,#e3e6ea); }
.pcb-field-type-badge { font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px; text-transform: uppercase; letter-spacing: .04em; flex-shrink: 0; }
.pcb-field-name { font-size: 13px; font-weight: 500; color: var(--c-text,#111); flex: 1; min-width: 0; }
.pcb-field-req-badge { font-size: 10px; font-weight: 600; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; padding: 2px 8px; border-radius: 5px; }
.pcb-field-opt-badge { font-size: 10px; font-weight: 500; color: var(--c-sub,#94a3b8); background: var(--c-bg,#f8fafc); border: 1px solid var(--c-border,#e2e8f0); padding: 2px 8px; border-radius: 5px; }
.pcb-field-body { padding: 14px; display: flex; flex-direction: column; gap: 12px; }
.pcb-body-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.pcb-body-actions { display: flex; align-items: center; justify-content: space-between; padding-top: 4px; }
.type-text, .type-email, .type-tel, .type-select { background: var(--c-bg,#f1f5f9); color: var(--c-sub,#64748b); border: 1px solid var(--c-border,#e2e8f0); }
.wc-hours-table { width: 100%; border-collapse: collapse; }
.wc-hours-table th { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .04em; padding: 4px 8px; text-align: left; }
.wc-hours-table td { padding: 6px 8px; vertical-align: middle; }
.wc-hours-table tr { border-bottom: 1px solid var(--c-border,#e3e6ea); }
.wc-hours-table tr:last-child { border-bottom: none; }
.wc-token-box { background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 8px; padding: 10px 14px; font-family: monospace; font-size: 11.5px; color: var(--c-text,#374151); word-break: break-all; line-height: 1.6; }
@keyframes spin { to { transform: rotate(360deg); } }
@keyframes nx-spin { to { transform: rotate(360deg); } }
.wl-upload-overlay { position:absolute;inset:0;background:rgba(0,0,0,.55);border-radius:50%;align-items:center;justify-content:center; }
.fi-page-header, .fi-breadcrumbs { display: none !important; }
</style>

@php
$fabIconDefs = [
    'chat'       => ['label'=>'Chat',    'stroke'=>true,  'path'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
    'chat_solid' => ['label'=>'Sólido',  'stroke'=>false, 'path'=>'M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z'],
    'headset'    => ['label'=>'Soporte', 'stroke'=>true,  'path'=>'M3 18v-6a9 9 0 0118 0v6 M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z'],
    'help'       => ['label'=>'Ayuda',   'stroke'=>true,  'path'=>'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
];
$presetColors = ['#3b82f6','#22c55e','#111827','#06b6d4','#1e40af','#7c3aed','#f97316','#ec4899','#ef4444'];
$fabIconSvgs = [
    'chat'       => '<svg fill="none" stroke="white" viewBox="0 0 24 24" width="22" height="22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>',
    'chat_solid' => '<svg fill="white" viewBox="0 0 24 24" width="22" height="22"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>',
    'headset'    => '<svg fill="none" stroke="white" viewBox="0 0 24 24" width="22" height="22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0118 0v6"/><path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z"/></svg>',
    'help'       => '<svg fill="none" stroke="white" viewBox="0 0 24 24" width="22" height="22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
];
$fabPxMap = ['sm'=>44,'md'=>52,'lg'=>60,'xl'=>68];
$fabPx = $fabPxMap[$widgetSize] ?? 44;
@endphp

<div class="wc-page">
<div class="wc-left-col">

{{-- ══════════════════════════════════════════
     SECTION 1 — Widget Customization
══════════════════════════════════════════ --}}
<div class="nx-section" x-data="{ open: true }">
    <div class="nx-section-hd" @click="open = !open">
        <span class="nx-section-title">Personalización del widget</span>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" :style="open ? 'transform:rotate(0deg)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub)"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <div x-show="open" x-transition style="padding:0 20px 22px">

        {{-- Bot name + avatar + on/off --}}
        <div class="wc-field" style="margin-top:18px">
            <label class="wc-label">Nombre del bot</label>
            <input type="text" class="wc-input" wire:model.live="botName" placeholder="Nexova IA">
        </div>

        {{-- Bot avatar upload --}}
        <div class="wc-field" style="margin-top:12px">
            <label class="wc-label">Foto / avatar del bot</label>
            <div style="display:flex;align-items:center;gap:12px;margin-top:6px">
                @php $avatarSrc = $botAvatarPreview ?: ($botAvatar ?: null); @endphp
                <div style="width:48px;height:48px;border-radius:50%;background:{{ $accentColor }}22;border:2px solid {{ $accentColor }}44;overflow:hidden;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    @if($avatarSrc)
                        <img src="{{ $avatarSrc }}" style="width:100%;height:100%;object-fit:cover">
                    @else
                        <span style="font-size:18px;font-weight:700;color:{{ $accentColor }}">{{ strtoupper(substr($botName ?: 'N',0,1)) }}</span>
                    @endif
                </div>
                <div style="flex:1">
                    <input type="file" wire:model="botAvatarFile" accept="image/*"
                           style="font-size:12px;color:var(--c-sub);width:100%">
                    <p style="font-size:11px;color:var(--c-sub);margin:4px 0 0">JPG, PNG o GIF · máx 1 MB</p>
                </div>
                @if($botAvatar)
                <button type="button" wire:click="$set('botAvatar','')" style="font-size:11px;color:#ef4444;background:none;border:none;cursor:pointer;padding:0">Eliminar</button>
                @endif
            </div>
        </div>

        {{-- Bot IA toggle --}}
        @php
            $org = auth()->user()?->organization;
            $plan = $org?->plan ?? 'free';
            $isAiBlocked = $org?->isAiBlocked() ?? true;
            $botMsgUsed  = $org?->bot_messages_this_month ?? 0;
            $planModel   = \App\Models\Plan::where('slug', $plan)->first();
            $botMsgLimit = $planModel?->max_bot_messages_monthly ?? 0;
            $planLabel   = ['free'=>'Gratuito','trial'=>'Prueba','pro'=>'Pro','enterprise'=>'Enterprise'][$plan] ?? ucfirst($plan);
        @endphp
        <div style="margin-top:16px;padding:14px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <div>
                    <div style="font-size:13px;font-weight:600;color:#111827">Bot de IA</div>
                    <div style="font-size:11.5px;color:var(--c-sub);margin-top:1px">
                        @if($isAiBlocked) Solo responde con FAQ y base de conocimiento @else Responde con IA (Groq / Gemini) @endif
                    </div>
                </div>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                    <div x-data="{ on: @entangle('botEnabled') }"
                         @click="on = !on; $wire.set('botEnabled', on)"
                         :style="on ? 'background:{{ $accentColor }}' : 'background:#d1d5db'"
                         style="width:40px;height:22px;border-radius:11px;transition:background .2s;position:relative;cursor:pointer">
                        <div :style="on ? 'left:20px' : 'left:2px'"
                             style="position:absolute;top:3px;width:16px;height:16px;border-radius:50%;background:#fff;transition:left .2s;box-shadow:0 1px 3px rgba(0,0,0,.2)"></div>
                    </div>
                    <span style="font-size:12px;font-weight:500;color:#374151">{{ $botEnabled ? 'Activado' : 'Desactivado' }}</span>
                </label>
            </div>

            {{-- Plan info --}}
            <div style="border-top:1px solid #e5e7eb;padding-top:10px;display:flex;flex-wrap:wrap;gap:8px">
                <span style="font-size:11px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:6px;padding:3px 8px;color:#374151;font-weight:500">
                    Plan: <strong>{{ $planLabel }}</strong>
                </span>
                <span style="font-size:11px;background:{{ $isAiBlocked ? '#fef3c7' : '#dcfce7' }};border:1px solid {{ $isAiBlocked ? '#fde68a' : '#bbf7d0' }};border-radius:6px;padding:3px 8px;color:{{ $isAiBlocked ? '#92400e' : '#166534' }};font-weight:500">
                    IA: {{ $isAiBlocked ? 'Solo KB/FAQ' : 'Completa' }}
                </span>
                <span style="font-size:11px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:3px 8px;color:#1e40af;font-weight:500">
                    Mensajes bot: {{ $botMsgUsed }} / {{ $botMsgLimit === 0 ? '∞' : number_format($botMsgLimit) }}
                </span>
            </div>
        </div>

        {{-- Widget name (internal) --}}
        <div class="wc-field" style="margin-top:12px">
            <label class="wc-label">Nombre interno del widget</label>
            <input type="text" class="wc-input" wire:model="name" placeholder="Ej: Widget Principal">
        </div>
        @if($widgetId)
        <div style="margin-top:8px">
            <div class="wc-label" style="margin-bottom:4px">Token público</div>
            <div class="wc-token-box">{{ $widgetToken }}</div>
        </div>
        @endif

        {{-- Widget Icon (Chatway style) --}}
        <div style="margin-top:22px">
            <div class="wc-label" style="margin-bottom:2px">Icono del widget</div>
            <p style="font-size:11.5px;color:var(--c-sub);margin:0 0 4px">Elige el ícono que aparecerá en el botón flotante</p>
            <div class="nx-icon-row">
                @foreach($fabIconDefs as $key => $ico)
                @php $isActive = ($buttonIcon === $key && $buttonStyle === 'icon'); @endphp
                <label class="nx-icon-opt" style="cursor:pointer" wire:click="$set('buttonIcon','{{ $key }}'); $set('buttonStyle','icon')">
                    <div class="nx-icon-circle" style="background:{{ $isActive ? $accentColor : $accentColor.'22' }};border:2px solid {{ $isActive ? $accentColor : $accentColor.'44' }};box-shadow:{{ $isActive ? '0 0 0 3px '.$accentColor.'33' : 'none' }};transition:all .15s">
                        @if($ico['stroke'])
                        <svg fill="none" stroke="{{ $isActive ? '#fff' : $accentColor }}" viewBox="0 0 24 24" width="22" height="22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $ico['path'] }}"/></svg>
                        @else
                        <svg fill="{{ $isActive ? '#fff' : $accentColor }}" viewBox="0 0 24 24" width="22" height="22"><path d="{{ $ico['path'] }}"/></svg>
                        @endif
                    </div>
                    <div class="nx-icon-radio {{ $isActive ? 'checked' : '' }}"></div>
                    <span class="nx-icon-label">{{ $ico['label'] }}</span>
                </label>
                @endforeach
                {{-- Upload logo option --}}
                @php
                    $isImg    = ($buttonStyle === 'image');
                    $logoSrc  = $buttonImagePreview ?: $buttonImage; // preview first, then saved
                @endphp
                <div class="nx-icon-opt" style="cursor:pointer" onclick="document.getElementById('wl-file-input').click()">
                    <div class="nx-icon-circle" style="background:transparent;border:2.5px dashed {{ $isImg ? $accentColor : 'var(--c-border,#d1d5db)' }};position:relative;overflow:hidden">
                        @if($isImg && $logoSrc)
                        <img src="{{ $logoSrc }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%" onerror="this.style.display='none'">
                        @else
                        <svg fill="none" stroke="{{ $isImg ? $accentColor : 'var(--c-sub,#9ca3af)' }}" viewBox="0 0 24 24" width="20" height="20" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                        @endif
                        {{-- Loading overlay — shown only while Livewire processes the file --}}
                        <div wire:loading.flex wire:target="buttonImageFile" class="wl-upload-overlay">
                            <div style="width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:nx-spin .7s linear infinite"></div>
                        </div>
                    </div>
                    <div class="nx-icon-radio {{ $isImg ? 'checked' : '' }}"></div>
                    <span class="nx-icon-label">Logo</span>
                </div>
                {{-- Hidden file input --}}
                <input id="wl-file-input" type="file" accept="image/*" wire:model="buttonImageFile" style="display:none">
            </div>

            {{-- Logo hint + remove --}}
            @if($buttonStyle === 'image')
            <div style="display:flex;align-items:center;gap:10px;margin-top:10px">
                @if($buttonImagePreview)
                <span style="font-size:11px;color:#f59e0b;font-weight:600">⚠ Imagen pendiente — guarda los cambios para confirmar</span>
                @else
                <span style="font-size:11px;color:var(--c-sub)">Imagen cuadrada ≥ 64×64 px — circular con borde de color del widget</span>
                @endif
                @if($buttonImage && !$buttonImagePreview)
                <button type="button" wire:click="$set('buttonImage','')" style="font-size:11px;color:#dc2626;background:none;border:none;cursor:pointer;padding:0;font-family:inherit;white-space:nowrap">Eliminar</button>
                @endif
            </div>
            @endif

            {{-- Optional button text + text color --}}
            @if($buttonStyle === 'icon')
            <div class="wc-grid" style="margin-top:14px">
                <div class="wc-field">
                    <label class="wc-label">Texto del botón <span style="font-weight:400;text-transform:none;font-size:10px">(opcional)</span></label>
                    <input type="text" class="wc-input" wire:model.live="buttonText" placeholder="Vacío = solo icono" maxlength="18">
                </div>
                <div class="wc-field">
                    <label class="wc-label">Color del texto</label>
                    <div class="wc-color-row">
                        <input type="color" class="wc-color-picker" wire:model.live="buttonTextColor">
                        <input type="text" class="wc-input" wire:model.live="buttonTextColor" placeholder="#ffffff" style="font-family:monospace">
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Widget Color --}}
        <div style="margin-top:22px">
            <div class="wc-label" style="margin-bottom:2px">Color del widget</div>
            <div class="nx-color-grid">
                @foreach($presetColors as $clr)
                <button type="button" wire:click="$set('accentColor','{{ $clr }}')"
                    class="nx-swatch{{ $accentColor === $clr ? ' active' : '' }}"
                    style="background:{{ $clr }};color:{{ $clr }}"
                    title="{{ $clr }}">
                    @if($accentColor === $clr)
                    <svg fill="white" viewBox="0 0 24 24" width="14" height="14" style="position:absolute"><path d="M20 6L9 17l-5-5"/><path stroke="white" stroke-width="2.5" stroke-linecap="round" fill="none" d="M20 6L9 17l-5-5"/></svg>
                    @endif
                </button>
                @endforeach
                {{-- Custom color swatch --}}
                <div class="nx-swatch nx-swatch-custom" title="Color personalizado"
                    style="color:var(--c-sub,#9ca3af)">
                    <input type="color" wire:model.live="accentColor" title="Color personalizado">
                    @if(!in_array($accentColor, $presetColors))
                    <div style="width:100%;height:100%;border-radius:50%;background:{{ $accentColor }};position:absolute;top:0;left:0"></div>
                    @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" style="position:relative;z-index:1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    @endif
                </div>
            </div>
        </div>

        {{-- Collect visitor's info row --}}
        <div x-data="{ open: false }" style="margin-top:18px">
            <div class="nx-section-row" @click="open = !open" style="border-top:1px solid var(--c-border,#e3e6ea);padding-top:13px">
                <div class="nx-section-row-icon" style="background:rgba(59,130,246,.1)">
                    <svg fill="none" stroke="#3b82f6" viewBox="0 0 24 24" width="16" height="16" stroke-width="2" stroke-linecap="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div style="flex:1">
                    <div class="nx-section-row-label">Recolectar datos del visitante</div>
                    <div class="nx-section-row-sub">
                        @if($preChatEnabled && count($preChatFields))
                            {{ implode(', ', array_map(fn($f) => $f['label'] ?? '', array_slice($preChatFields, 0, 3))) }}
                        @else
                            Solicitar info antes de iniciar el chat
                        @endif
                    </div>
                </div>
                <label class="wc-toggle" @click.stop>
                    <input type="checkbox" wire:model.live="preChatEnabled">
                    <span class="wc-slider"></span>
                </label>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" :style="open ? 'transform:rotate(0)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub);margin-left:4px"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div x-show="open" x-transition style="padding-top:12px">
                @if($preChatEnabled)
                <div style="display:flex;flex-direction:column;gap:6px">
                    @if(empty($preChatFields))
                    <div style="display:flex;gap:8px;align-items:center;padding:10px 14px;background:rgba(34,197,94,.07);border:1px dashed rgba(34,197,94,.3);border-radius:9px">
                        <svg fill="none" stroke="#22c55e" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span style="font-size:12px;color:var(--c-sub);flex:1">Sin campos. Usa los campos base o agrega los tuyos.</span>
                        <button type="button" class="wc-btn wc-btn-primary" style="padding:5px 12px;font-size:11.5px" wire:click="addDefaultFields">Campos base</button>
                    </div>
                    @endif
                    @forelse($preChatFields as $i => $field)
                    @php $type = $field['type'] ?? 'text'; $label = $field['label'] ?? 'Campo'; @endphp
                    <div class="pcb-field" x-data="{ open: false }">
                        <div class="pcb-field-header" :class="open ? 'open' : ''" @click="open = !open">
                            <span class="pcb-field-type-badge type-{{ $type }}">{{ $type }}</span>
                            <span class="pcb-field-name">{{ $label ?: 'Sin nombre' }}</span>
                            @if($field['required'] ?? false)
                                <span class="pcb-field-req-badge">Requerido</span>
                            @else
                                <span class="pcb-field-opt-badge">Opcional</span>
                            @endif
                        </div>
                        <div class="pcb-field-body" x-show="open" x-collapse>
                            <div class="pcb-body-grid">
                                <div class="wc-field">
                                    <label class="wc-label">Etiqueta</label>
                                    <input type="text" class="wc-input" wire:model.live="preChatFields.{{ $i }}.label" placeholder="Ej: Nombre completo">
                                </div>
                                <div class="wc-field">
                                    <label class="wc-label">Tipo</label>
                                    <select class="wc-select" wire:model.live="preChatFields.{{ $i }}.type">
                                        <option value="text">Texto</option>
                                        <option value="email">Email</option>
                                        <option value="tel">Teléfono</option>
                                        <option value="select">Selección</option>
                                    </select>
                                </div>
                                <div class="wc-field" style="grid-column:1/-1">
                                    <label class="wc-label">Placeholder</label>
                                    <input type="text" class="wc-input" wire:model="preChatFields.{{ $i }}.placeholder" placeholder="Texto de ayuda...">
                                </div>
                            </div>
                            <div class="pcb-body-actions">
                                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12.5px;color:var(--c-text);font-weight:500">
                                    <label class="wc-toggle" style="width:34px;height:19px"><input type="checkbox" wire:model.live="preChatFields.{{ $i }}.required"><span class="wc-slider"></span></label>
                                    Requerido
                                </label>
                                <button type="button" class="pcb-icon-btn danger" wire:click="removeField({{ $i }})" wire:confirm="¿Eliminar este campo?">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    @endforelse
                    <button type="button" class="pcb-add-btn" wire:click="addField">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Agregar campo
                    </button>
                </div>
                @else
                <p style="font-size:12px;color:var(--c-sub);padding:6px 0">Activa la opción para configurar los campos del formulario.</p>
                @endif
            </div>
        </div>

        {{-- FAQ row --}}
        <div x-data="{ open: false }">
            <div class="nx-section-row" @click="open = !open">
                <div class="nx-section-row-icon" style="background:#f1f5f9">
                    <svg fill="none" stroke="#64748b" viewBox="0 0 24 24" width="16" height="16" stroke-width="2" stroke-linecap="round"><path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div style="flex:1">
                    <div class="nx-section-row-label">Preguntas frecuentes (FAQ)</div>
                    @if($faqEnabled && count($faqItems))
                    <div class="nx-section-row-sub">{{ count($faqItems) }} pregunta(s) configurada(s)</div>
                    @endif
                </div>
                <label class="wc-toggle" @click.stop>
                    <input type="checkbox" wire:model.live="faqEnabled">
                    <span class="wc-slider"></span>
                </label>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" :style="open ? 'transform:rotate(0)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub);margin-left:4px"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div x-show="open" x-transition style="padding-top:12px">
                @if($faqEnabled)
                <div style="display:flex;flex-direction:column;gap:8px">
                    @forelse($faqItems as $i => $faq)
                    <div class="wc-item-row">
                        <div class="wc-item-fields">
                            <input type="text" class="wc-input" wire:model.live="faqItems.{{ $i }}.question" placeholder="Pregunta frecuente...">
                            <textarea class="wc-input" wire:model="faqItems.{{ $i }}.answer" placeholder="Respuesta automática..." rows="2" style="resize:vertical"></textarea>
                        </div>
                        <button type="button" class="pcb-icon-btn danger" wire:click="removeFaq({{ $i }})" wire:confirm="¿Eliminar esta pregunta?">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @empty
                    <div style="text-align:center;padding:16px;color:var(--c-sub);font-size:12.5px">Sin preguntas. Agrega una abajo.</div>
                    @endforelse
                    <button type="button" class="pcb-add-btn" wire:click="addFaq">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Agregar pregunta
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════
     SECTION 2 — General Preferences
══════════════════════════════════════════ --}}
<div class="nx-section" x-data="{ open: true }">
    <div class="nx-section-hd" @click="open = !open">
        <span class="nx-section-title">Preferencias generales</span>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" :style="open ? 'transform:rotate(0deg)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub)"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <div x-show="open" x-transition style="padding:0 20px 22px">

        {{-- Widget Preview Message (visual cards) --}}
        <div style="margin-top:18px">
            <div class="wc-label">Mensaje de burbuja flotante</div>
            <p style="font-size:11.5px;color:var(--c-sub);margin:2px 0 0">Muestra un mensaje encima del botón para captar atención</p>
            <div class="nx-pm-cards">
                {{-- Preview ON --}}
                <label class="nx-pm-card {{ $previewMessageEnabled ? 'active' : '' }}" wire:click="$set('previewMessageEnabled',true)" style="cursor:pointer">
                    <div class="nx-pm-card-body">
                        {{-- Mini bubble above FAB --}}
                        <div style="position:absolute;bottom:52px;right:10px;background:#fff;border-radius:10px 10px 10px 3px;padding:6px 9px;font-size:9px;color:#374151;box-shadow:0 2px 8px rgba(0,0,0,.12);max-width:80px;line-height:1.3">
                            {{ $previewMessage ?: '¿Necesitas ayuda? 👋' }}
                        </div>
                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $accentColor }};display:flex;align-items:center;justify-content:center;margin-right:8px;margin-bottom:6px;box-shadow:0 2px 8px rgba(0,0,0,.15)">
                            {!! $fabIconSvgs[$buttonIcon] ?? $fabIconSvgs['chat'] !!}
                        </div>
                    </div>
                    <div class="nx-pm-card-foot">
                        <div class="nx-pm-card-radio {{ $previewMessageEnabled ? 'checked' : '' }}"></div>
                        <div>
                            <div class="nx-pm-card-lbl">Preview On</div>
                            <div class="nx-pm-card-sub">Muestra el mensaje flotante</div>
                        </div>
                    </div>
                </label>
                {{-- Preview OFF --}}
                <label class="nx-pm-card {{ !$previewMessageEnabled ? 'active' : '' }}" wire:click="$set('previewMessageEnabled',false)" style="cursor:pointer">
                    <div class="nx-pm-card-body" style="align-items:flex-end;justify-content:flex-end">
                        <div style="width:36px;height:36px;border-radius:50%;background:{{ $accentColor }};display:flex;align-items:center;justify-content:center;margin-right:8px;margin-bottom:6px;box-shadow:0 2px 8px rgba(0,0,0,.15)">
                            {!! $fabIconSvgs[$buttonIcon] ?? $fabIconSvgs['chat'] !!}
                        </div>
                    </div>
                    <div class="nx-pm-card-foot">
                        <div class="nx-pm-card-radio {{ !$previewMessageEnabled ? 'checked' : '' }}"></div>
                        <div>
                            <div class="nx-pm-card-lbl">Preview Off</div>
                            <div class="nx-pm-card-sub">Solo el ícono, sin mensajes</div>
                        </div>
                    </div>
                </label>
            </div>
            @if($previewMessageEnabled)
            <div class="wc-field" style="margin-top:10px">
                <label class="wc-label">Texto del mensaje flotante</label>
                <input type="text" class="wc-input" wire:model="previewMessage" placeholder="¿Necesitas ayuda? ¡Estamos aquí!">
            </div>
            @endif
        </div>

        {{-- Welcome message --}}
        <div class="wc-field" style="margin-top:18px">
            <label class="wc-label">Mensaje de bienvenida</label>
            <input type="text" class="wc-input" wire:model.live="welcomeMessage" placeholder="Hola, ¿en qué te puedo ayudar?">
        </div>

        {{-- Position + Size + Screen --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:16px">
            <div class="wc-field">
                <label class="wc-label">Posición</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $widgetPosition === 'left' ? 'active' : '' }}" wire:click="$set('widgetPosition','left')">← Izquierda</button>
                    <button type="button" class="wc-seg-btn {{ $widgetPosition === 'right' ? 'active' : '' }}" wire:click="$set('widgetPosition','right')">Derecha →</button>
                </div>
            </div>
            <div class="wc-field">
                <label class="wc-label">Tamaño del botón</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'sm' ? 'active' : '' }}" wire:click="$set('widgetSize','sm')">S</button>
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'md' ? 'active' : '' }}" wire:click="$set('widgetSize','md')">M</button>
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'lg' ? 'active' : '' }}" wire:click="$set('widgetSize','lg')">L</button>
                    <button type="button" class="wc-seg-btn {{ $widgetSize === 'xl' ? 'active' : '' }}" wire:click="$set('widgetSize','xl')">XL</button>
                </div>
            </div>
            <div class="wc-field">
                <label class="wc-label">Pantalla al abrir</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $defaultScreen === 'home' ? 'active' : '' }}" wire:click="$set('defaultScreen','home')">Inicio</button>
                    <button type="button" class="wc-seg-btn {{ $defaultScreen === 'chat' ? 'active' : '' }}" wire:click="$set('defaultScreen','chat')">Chat</button>
                </div>
            </div>
            <div class="wc-field">
                <label class="wc-label">Mostrar en</label>
                <div class="wc-seg">
                    <button type="button" class="wc-seg-btn {{ $showOn === 'both' ? 'active' : '' }}" wire:click="$set('showOn','both')">Todos</button>
                    <button type="button" class="wc-seg-btn {{ $showOn === 'desktop' ? 'active' : '' }}" wire:click="$set('showOn','desktop')">Desktop</button>
                    <button type="button" class="wc-seg-btn {{ $showOn === 'mobile' ? 'active' : '' }}" wire:click="$set('showOn','mobile')">Móvil</button>
                </div>
            </div>
        </div>

        {{-- Attention effect --}}
        <div class="wc-field" style="margin-top:14px">
            <label class="wc-label">Efecto de atención del botón</label>
            <select class="wc-select" wire:model="attentionEffect">
                <option value="none">Sin efecto</option>
                <option value="bounce">Rebotar</option>
                <option value="pulse">Pulsar</option>
                <option value="spin">Girar</option>
                <option value="shake">Sacudir</option>
            </select>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════
     SECTION 3 — Canales de contacto
══════════════════════════════════════════ --}}
<div class="nx-section" x-data="{ open: {{ count($socialChannels) > 0 ? 'true' : 'false' }} }">
    <div class="nx-section-hd" @click="open = !open">
        <div style="display:flex;align-items:center;gap:10px">
            <span class="nx-section-title">Canales de contacto rápido</span>
            @if(count($socialChannels))
            <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:99px;background:rgba(34,197,94,.12);color:#16a34a">{{ count($socialChannels) }}</span>
            @endif
        </div>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" :style="open ? 'transform:rotate(0deg)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub)"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <div x-show="open" x-transition style="padding:0 20px 20px">
        <p style="font-size:11.5px;color:var(--c-sub);margin:12px 0 12px">Aparecen en la pestaña "Canales" del widget</p>
        <div style="display:flex;flex-direction:column;gap:8px">
            @forelse($socialChannels as $i => $ch)
            <div class="wc-item-row">
                <div class="wc-item-fields">
                    <div style="display:grid;grid-template-columns:140px 1fr 1fr;gap:8px">
                        <select class="wc-select" wire:model.live="socialChannels.{{ $i }}.type">
                            <option value="whatsapp">WhatsApp</option>
                            <option value="telegram">Telegram</option>
                            <option value="instagram">Instagram</option>
                            <option value="facebook">Facebook</option>
                            <option value="email">Email</option>
                            <option value="phone">Teléfono</option>
                            <option value="other">Otro</option>
                        </select>
                        <input type="text" class="wc-input" wire:model="socialChannels.{{ $i }}.label" placeholder="Etiqueta">
                        <input type="text" class="wc-input" wire:model="socialChannels.{{ $i }}.url" placeholder="URL o número">
                    </div>
                </div>
                <button type="button" class="pcb-icon-btn danger" wire:click="removeSocialChannel({{ $i }})" wire:confirm="¿Eliminar este canal?">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            @empty
            <p style="font-size:12.5px;color:var(--c-sub);text-align:center;padding:10px 0">Sin canales de contacto.</p>
            @endforelse
            <button type="button" class="pcb-add-btn" wire:click="addSocialChannel">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Agregar canal
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SECTION 4 — Horario de atención
══════════════════════════════════════════ --}}
<div class="nx-section" x-data="{ open: {{ $workingHoursEnabled ? 'true' : 'false' }} }">
    <div class="nx-section-hd" @click="open = !open">
        <span class="nx-section-title">Horario de atención</span>
        <div style="display:flex;align-items:center;gap:8px">
            <label class="wc-toggle" @click.stop>
                <input type="checkbox" wire:model.live="workingHoursEnabled">
                <span class="wc-slider"></span>
            </label>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" :style="open ? 'transform:rotate(0deg)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub)"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
        </div>
    </div>
    <div x-show="open" x-transition style="padding:0 20px 20px">
        @if($workingHoursEnabled)
        <table class="wc-hours-table" style="margin-top:14px">
            <thead><tr><th>Día</th><th>Activo</th><th>Desde</th><th>Hasta</th></tr></thead>
            <tbody>
                @foreach(['mon','tue','wed','thu','fri','sat','sun'] as $day)
                <tr>
                    <td style="font-size:13px;font-weight:500;color:var(--c-text)">{{ $workingHours[$day]['label'] ?? $day }}</td>
                    <td><label class="wc-toggle" style="width:34px;height:19px"><input type="checkbox" wire:model.live="workingHours.{{ $day }}.enabled"><span class="wc-slider"></span></label></td>
                    <td><input type="time" class="wc-input" style="width:110px" wire:model="workingHours.{{ $day }}.from" {{ !($workingHours[$day]['enabled'] ?? false) ? 'disabled' : '' }}></td>
                    <td><input type="time" class="wc-input" style="width:110px" wire:model="workingHours.{{ $day }}.to" {{ !($workingHours[$day]['enabled'] ?? false) ? 'disabled' : '' }}></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="wc-field" style="margin-top:12px">
            <label class="wc-label">Mensaje fuera de horario</label>
            <input type="text" class="wc-input" wire:model="offlineMessage" placeholder="Estamos fuera de horario. Te responderemos pronto.">
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════
     SECTION 5 — Comportamiento
══════════════════════════════════════════ --}}
<div class="nx-section" x-data="{ open: false }">
    <div class="nx-section-hd" @click="open = !open">
        <span class="nx-section-title">Comportamiento</span>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" :style="open ? 'transform:rotate(0deg)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub)"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <div x-show="open" x-transition style="padding:0 20px 20px">
        <div style="margin-top:6px">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid var(--c-border,#e3e6ea)">
                <div><div style="font-size:13px;font-weight:500;color:var(--c-text)">Mostrar watermark</div><div style="font-size:11.5px;color:var(--c-sub)">Muestra "Powered by Nexova Digital Solutions"</div></div>
                <label class="wc-toggle"><input type="checkbox" wire:model.live="showBranding"><span class="wc-slider"></span></label>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid var(--c-border,#e3e6ea)">
                <div><div style="font-size:13px;font-weight:500;color:var(--c-text)">Sonidos de notificación</div><div style="font-size:11.5px;color:var(--c-sub)">Reproduce sonido al recibir mensajes</div></div>
                <label class="wc-toggle"><input type="checkbox" wire:model.live="soundEnabled"><span class="wc-slider"></span></label>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 0">
                <div><div style="font-size:13px;font-weight:500;color:var(--c-text)">Pedir calificación al cerrar</div><div style="font-size:11.5px;color:var(--c-sub)">El usuario puede valorar de 1 a 5 estrellas</div></div>
                <label class="wc-toggle"><input type="checkbox" wire:model.live="requireRating"><span class="wc-slider"></span></label>
            </div>
            @if($requireRating)
            <div class="wc-field" style="margin-top:12px">
                <label class="wc-label">Mensaje de calificación</label>
                <input type="text" class="wc-input" wire:model="ratingMessage" placeholder="¿Cómo fue tu experiencia?">
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ══ Llamada a agente ══ --}}
<div class="nx-section" x-data="{ open: false }">
    <div class="nx-section-hd" @click="open = !open">
        <span class="nx-section-title">Llamada a agente humano</span>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16" :style="open ? 'transform:rotate(0deg)' : 'transform:rotate(180deg)'" style="transition:transform .2s;color:var(--c-sub)"><polyline points="18 15 12 9 6 15" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <div x-show="open" x-transition style="padding:0 20px 20px">
        <p style="font-size:12px;color:var(--c-sub);margin:12px 0">
            Cuando un usuario pide hablar con un agente, el panel emite una alerta sonora. Configura el tiempo de espera y qué ocurre si nadie atiende.
        </p>
        <div class="wc-field" style="margin-bottom:14px">
            <label class="wc-label">Tiempo límite de llamada</label>
            <select class="wc-input" wire:model.live="agentCallTimeout">
                <option value="5">5 minutos</option>
                <option value="10">10 minutos</option>
                <option value="15">15 minutos</option>
            </select>
        </div>
        <div class="wc-field">
            <label class="wc-label">Si no hay respuesta del agente…</label>
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:6px">
                <label style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1.5px solid {{ $agentNoResponse === 'bot' ? 'var(--c-accent,#7c3aed)' : 'var(--c-border,#e3e6ea)' }};border-radius:8px;cursor:pointer">
                    <input type="radio" wire:model.live="agentNoResponse" value="bot" style="margin-top:2px;accent-color:var(--c-accent,#7c3aed)">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text)">Volver al bot</div>
                        <div style="font-size:11.5px;color:var(--c-sub)">El chat regresa al asistente IA automáticamente</div>
                    </div>
                </label>
                <label style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border:1.5px solid {{ $agentNoResponse === 'ticket' ? 'var(--c-accent,#7c3aed)' : 'var(--c-border,#e3e6ea)' }};border-radius:8px;cursor:pointer">
                    <input type="radio" wire:model.live="agentNoResponse" value="ticket" style="margin-top:2px;accent-color:var(--c-accent,#7c3aed)">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text)">Solicitar datos y crear ticket</div>
                        <div style="font-size:11.5px;color:var(--c-sub)">El usuario deja nombre, email y mensaje. Se crea un ticket de soporte.</div>
                    </div>
                </label>
            </div>
        </div>
        @php $widgetDepts = $this->availableDepartments; @endphp
        @if($widgetDepts->isNotEmpty())
        <div class="wc-field" style="margin-top:16px">
            <label class="wc-label">Departamento por defecto</label>
            <select class="wc-input" wire:model.live="defaultDepartmentId">
                <option value="">Sin departamento (asignar manualmente)</option>
                @foreach($widgetDepts as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
            <span style="font-size:11px;color:var(--c-sub);margin-top:4px;display:block">
                Las conversaciones de este widget se asignan automáticamente a este departamento.
            </span>
        </div>
        @endif
    </div>
</div>

</div>{{-- /.wc-left-col --}}

{{-- ══════════════════════════════════════════
     RIGHT COLUMN — Preview + Code + Save
══════════════════════════════════════════ --}}
<div class="wc-right-col">

    {{-- ── Live Preview ── --}}
    <div style="background:var(--c-surface,#fff);border:1px solid var(--c-border,#e3e6ea);border-radius:14px;padding:16px">
        <div style="font-size:12px;font-weight:700;color:var(--c-text);margin-bottom:12px;display:flex;align-items:center;gap:6px">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Vista previa
        </div>

        {{-- Browser mock --}}
        <div style="background:#dde3ec;border-radius:10px;padding:10px;position:relative;min-height:200px;overflow:hidden">
            <div style="background:#fff;border-radius:6px;padding:9px 11px;margin-bottom:6px">
                <div style="height:6px;background:#e5e7eb;border-radius:99px;width:52%;margin-bottom:5px"></div>
                <div style="height:4px;background:#f3f4f6;border-radius:99px;width:36%"></div>
            </div>
            <div style="background:#fff;border-radius:6px;padding:9px 11px">
                <div style="height:4px;background:#f3f4f6;border-radius:99px;margin-bottom:4px"></div>
                <div style="height:4px;background:#f3f4f6;border-radius:99px;width:78%;margin-bottom:4px"></div>
                <div style="height:4px;background:#f3f4f6;border-radius:99px;width:52%"></div>
            </div>

            {{-- Mini widget window --}}
            <div style="position:absolute;{{ $widgetPosition === 'left' ? 'left:9px' : 'right:9px' }};bottom:{{ $fabPx + 14 }}px;width:190px;background:#fff;border-radius:10px;box-shadow:0 5px 20px rgba(0,0,0,.14);overflow:hidden">
                <div style="background:{{ $accentColor }};padding:9px 12px;display:flex;align-items:center;gap:8px">
                    <div style="width:24px;height:24px;border-radius:50%;background:rgba(255,255,255,.22);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0">{{ strtoupper(substr($botName ?: 'N',0,1)) }}</div>
                    <div>
                        <div style="font-size:11px;font-weight:700;color:#fff;line-height:1.2">{{ $botName ?: 'Nexova IA' }}</div>
                        <div style="display:flex;align-items:center;gap:3px;margin-top:1px">
                            <span style="width:5px;height:5px;border-radius:50%;background:#34d399;display:block"></span>
                            <span style="font-size:9px;color:rgba(255,255,255,.8)">En línea</span>
                        </div>
                    </div>
                </div>
                <div style="padding:9px 11px;background:#f8fafc;min-height:44px">
                    @if($previewMessageEnabled && $previewMessage)
                    <div style="background:rgba(0,0,0,.06);border-radius:6px;padding:4px 7px;font-size:9px;color:#374151;margin-bottom:5px;font-style:italic">{{ $previewMessage }}</div>
                    @endif
                    <div style="background:#fff;border-radius:9px 9px 9px 3px;padding:6px 9px;font-size:9.5px;color:#374151;display:inline-block;max-width:90%;box-shadow:0 1px 3px rgba(0,0,0,.07)">{{ $welcomeMessage ?: 'Hola, ¿en qué te puedo ayudar?' }}</div>
                </div>
                @if(!empty($socialChannels))
                <div style="border-top:1px solid #f0f2f5;display:flex">
                    <div style="flex:1;padding:5px 0;display:flex;align-items:center;justify-content:center;gap:2px;font-size:8px;font-weight:700;color:{{ $accentColor }};border-right:1px solid #f0f2f5">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="9" height="9" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg> Chat
                    </div>
                    <div style="flex:1;padding:5px 0;display:flex;align-items:center;justify-content:center;gap:2px;font-size:8px;font-weight:700;color:var(--c-sub,#9ca3af)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="9" height="9" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg> Canales
                    </div>
                </div>
                @endif
            </div>

            {{-- FAB button --}}
            @php $previewLogoSrc = $buttonImagePreview ?: $buttonImage; @endphp
            <div style="position:absolute;{{ $widgetPosition === 'left' ? 'left:9px' : 'right:9px' }};bottom:9px">
                @if($buttonStyle === 'image' && $previewLogoSrc)
                <div style="width:{{ $fabPx }}px;height:{{ $fabPx }}px;border-radius:50%;border:3px solid {{ $accentColor }};overflow:hidden;background:#fff;box-shadow:0 3px 12px rgba(0,0,0,.2)">
                    <img src="{{ $previewLogoSrc }}" style="width:100%;height:100%;object-fit:cover" onerror="this.style.opacity=.3">
                </div>
                @elseif($buttonText)
                <div style="height:{{ $fabPx }}px;border-radius:99px;background:{{ $accentColor }};display:inline-flex;align-items:center;gap:5px;padding:0 {{ round($fabPx*.35) }}px;box-shadow:0 3px 12px rgba(0,0,0,.2);white-space:nowrap">
                    {!! str_replace('22', (string)round($fabPx*.38), $fabIconSvgs[$buttonIcon] ?? $fabIconSvgs['chat']) !!}
                    <span style="color:{{ $buttonTextColor }};font-size:{{ round($fabPx*.27) }}px;font-weight:700">{{ $buttonText }}</span>
                </div>
                @else
                <div style="width:{{ $fabPx }}px;height:{{ $fabPx }}px;border-radius:50%;background:{{ $accentColor }};display:flex;align-items:center;justify-content:center;box-shadow:0 3px 12px rgba(0,0,0,.2)">
                    {!! $fabIconSvgs[$buttonIcon] ?? $fabIconSvgs['chat'] !!}
                </div>
                @endif
            </div>
        </div>

        {{-- Config badges --}}
        <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:5px">
            @php
            $badges = [];
            $badges[] = $widgetPosition === 'left' ? '← Izquierda' : 'Derecha →';
            $badges[] = strtoupper($widgetSize);
            if($attentionEffect !== 'none') $badges[] = ucfirst($attentionEffect);
            if(!empty($socialChannels)) $badges[] = count($socialChannels).' canal'.(count($socialChannels)>1?'es':'');
            if($faqEnabled && count($faqItems)) $badges[] = count($faqItems).' FAQ';
            if($requireRating) $badges[] = 'Calificación';
            if($preChatEnabled) $badges[] = 'Pre-chat';
            if($workingHoursEnabled) $badges[] = 'Horario';
            @endphp
            @foreach($badges as $b)
            <span style="font-size:9.5px;font-weight:600;padding:2px 8px;border-radius:5px;background:var(--c-bg,#f1f5f9);color:var(--c-sub,#64748b);border:1px solid var(--c-border,#e2e8f0);letter-spacing:.01em">{{ $b }}</span>
            @endforeach
        </div>
    </div>

    {{-- ── Install code ── --}}
    @if($widgetId)
    <div style="background:var(--c-surface,#fff);border:1px solid var(--c-border,#e3e6ea);border-radius:14px;padding:16px" x-data>
        <div style="font-size:12px;font-weight:700;color:var(--c-text);margin-bottom:10px;display:flex;align-items:center;gap:6px">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            Código de instalación
        </div>
        <div class="wc-token-box">
            &lt;script&gt;<br>
            &nbsp;&nbsp;window.NexovaChatConfig = {<br>
            &nbsp;&nbsp;&nbsp;&nbsp;apiUrl: "{{ rtrim(config('app.url'),'/') }}",<br>
            &nbsp;&nbsp;&nbsp;&nbsp;widgetToken: "{{ $widgetToken }}"<br>
            &nbsp;&nbsp;};<br>
            &lt;/script&gt;<br>
            &lt;script src="{{ rtrim(config('app.url'),'/') }}/widget.js" defer&gt;&lt;/script&gt;
        </div>
        <button type="button" style="margin-top:8px;width:100%" class="wc-btn wc-btn-ghost"
            @click="navigator.clipboard.writeText(`<script>\n  window.NexovaChatConfig = { apiUrl: '{{ rtrim(config('app.url'),'/') }}', widgetToken: '{{ $widgetToken }}' };\n<\/script>\n<script src='{{ rtrim(config('app.url'),'/') }}/widget.js' defer><\/script>`); $dispatch('nexova-toast', {type:'success',message:'Código copiado'})">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Copiar código
        </button>
    </div>
    @endif

    {{-- ── Save button ── --}}
    <div style="background:var(--c-surface,#fff);border:1px solid var(--c-border,#e3e6ea);border-radius:14px;padding:14px">
        <button class="wc-btn wc-btn-primary" wire:click="save" wire:loading.attr="disabled" style="width:100%;justify-content:center;font-size:14px;padding:11px 0">
            <span wire:loading.remove wire:target="save" style="display:flex;align-items:center;gap:6px">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $widgetId ? 'Guardar cambios' : 'Crear widget' }}
            </span>
            <span wire:loading wire:target="save" style="display:none;align-items:center;gap:6px">
                <svg style="animation:spin 1s linear infinite" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Guardando...
            </span>
        </button>
        @if($widgetId)
        <a href="{{ \App\Filament\Resources\ChatWidgetResource::getUrl('index') }}"
           style="display:block;text-align:center;margin-top:8px;font-size:12px;color:var(--c-sub);text-decoration:none">
            ← Volver a todos los widgets
        </a>
        @endif
    </div>

</div>{{-- /.wc-right-col --}}
</div>
</x-filament-panels::page>
