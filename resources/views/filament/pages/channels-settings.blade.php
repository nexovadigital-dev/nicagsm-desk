<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.ch-wrap { padding: 32px 36px 64px; max-width: 1040px; }

.ch-section {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 32px;
    padding: 28px 0;
    border-top: 1px solid var(--c-border,#e3e6ea);
    align-items: start;
}
.ch-section:first-of-type { border-top: none; padding-top: 0; }
@media (max-width: 700px) { .ch-section { grid-template-columns: 1fr; gap: 16px; } }

.ch-channel-meta { display: flex; flex-direction: column; gap: 8px; padding-top: 2px; }
.ch-channel-id { display: flex; align-items: center; gap: 10px; }
.ch-channel-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
.ch-channel-name { font-size: 14px; font-weight: 600; color: var(--c-text,#111); }
.ch-channel-desc { font-size: 12.5px; color: var(--c-sub,#6b7280); line-height: 1.6; margin-top: 6px; }

.ch-panel { background: var(--c-surface,#fff); border: 1px solid var(--c-border,#e3e6ea); border-radius: 10px; padding: 18px 20px; }

.ch-label { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform:uppercase; letter-spacing:.05em; }
.ch-input {
    background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111); font-size: 13px;
    padding: 8px 11px; outline: none; width: 100%;
    transition: border-color .12s; box-sizing: border-box;
}
.ch-input:focus { border-color: #16a34a; }

.ch-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 13px; border-radius: 7px; font-size: 12px;
    font-weight: 500; cursor: pointer; border: 1px solid transparent;
    font-family: inherit; transition: background .1s;
}
.ch-btn-primary { background: #1e293b; color:#f8fafc; border-color:#1e293b; }
.ch-btn-primary:hover { background: #0f172a; }
.ch-btn-outline { background: transparent; color: var(--c-text,#111); border-color: var(--c-border,#e3e6ea); }
.ch-btn-outline:hover { background: var(--c-surf2,#f0f2f5); }

.ch-alert { padding: 9px 13px; border-radius: 7px; font-size: 12px; font-weight: 500; margin-top: 10px; }
.ch-alert-success { background: rgba(5,150,105,.07); border:1px solid rgba(5,150,105,.2); color:#059669; }
.ch-alert-error   { background: rgba(220,38,38,.07); border:1px solid rgba(220,38,38,.2); color:#dc2626; }

.ch-steps { display: flex; flex-direction: column; gap: 7px; }
.ch-step { display: flex; gap: 10px; align-items: flex-start; font-size: 12px; color: var(--c-sub,#6b7280); line-height: 1.6; }
.ch-step-num {
    width: 18px; height: 18px; min-width: 18px; border-radius: 50%;
    background: var(--c-surf2,#f0f2f5); border: 1px solid var(--c-border,#e3e6ea);
    display: flex; align-items:center; justify-content:center;
    font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280);
}
.ch-step code { background: var(--c-bg,#f5f6f8); padding: 1px 5px; border-radius: 4px; font-size: 11px; }
.ch-coming-soon {
    display: inline-flex; font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280);
    background: var(--c-surf2,#f0f2f5); border: 1px solid var(--c-border,#e3e6ea);
    padding: 3px 9px; border-radius: 99px;
}
.ch-dimmed { opacity: .65; }

/* ══ Tremor-style Status Card ══ */
.tg-status-card {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 12px; overflow: hidden;
}
.tg-bot-avatar {
    width: 46px; height: 46px; border-radius: 12px;
    background: linear-gradient(135deg,#2CA5E0 0%,#1a7fc4 100%);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    box-shadow: 0 2px 10px rgba(44,165,224,.25);
}
.tg-status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 11.5px; font-weight: 600; padding: 5px 12px;
    border-radius: 99px; flex-shrink: 0;
}
.tg-status-badge--ok    { background: rgba(5,150,105,.08); color:#059669; border:1px solid rgba(5,150,105,.2); }
.tg-status-badge--error { background: rgba(220,38,38,.08); color:#dc2626; border:1px solid rgba(220,38,38,.2); }
.tg-status-badge--none  { background: var(--c-bg,#f5f6f8); color: var(--c-sub,#6b7280); border: 1px solid var(--c-border,#e3e6ea); }

/* ══ Config Card ══ */
.tg-config-card {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 12px; margin-top: 12px; overflow: hidden;
}
.tg-config-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: rgba(99,102,241,.1);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}

/* Toggle moderno */
.nx-toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 13px 0; }
.nx-toggle-row + .nx-toggle-row { border-top: 1px solid var(--c-border,#e3e6ea); }
.nx-toggle__label { font-size: 13px; font-weight: 600; color: var(--c-text,#111); }
.nx-toggle__desc  { font-size: 11.5px; color: var(--c-sub,#6b7280); margin-top: 2px; line-height: 1.5; }
.nx-toggle__track {
    width: 40px; height: 22px; border-radius: 99px;
    background: var(--c-border,#d1d5db);
    position: relative; cursor: pointer; transition: background .2s; flex-shrink: 0;
}
.nx-toggle__track.on { background: #22c55e; }
.nx-toggle__thumb {
    position: absolute; left: 3px; top: 3px;
    width: 16px; height: 16px; border-radius: 50%;
    background: #fff; transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.nx-toggle__track.on .nx-toggle__thumb { transform: translateX(18px); }

.faq-item {
    display: flex; gap: 10px; align-items: flex-start;
    background: var(--c-bg,#f8fafc); padding: 12px;
    border-radius: 8px; border: 1px solid var(--c-border,#e3e6ea);
}
.faq-item + .faq-item { margin-top: 10px; }

@keyframes tg-pulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
</style>

<div class="ch-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:28px;letter-spacing:-.02em">Canales</h1>

    {{-- ── Telegram ── --}}
    <div class="ch-section">
        <div class="ch-channel-meta">
            <div class="ch-channel-id">
                <div class="ch-channel-icon" style="background:#2CA5E0">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                </div>
                <span class="ch-channel-name">Telegram</span>
            </div>
            <p class="ch-channel-desc">Conecta un bot de Telegram para recibir mensajes directamente en el panel con soporte de IA.</p>
        </div>

        <div>
            {{-- ══ STATUS CARD (Tremor-style) ══ --}}
            <div class="tg-status-card">

                {{-- Header: avatar + info + badge --}}
                <div style="display:flex;align-items:center;gap:14px;padding:18px 20px">
                    {{-- Avatar --}}
                    <div class="tg-bot-avatar">
                        <svg viewBox="0 0 24 24" fill="white" width="24" height="24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    </div>

                    {{-- Bot info --}}
                    <div style="flex:1;min-width:0">
                        @if($telegramStatus === 'ok')
                            <div style="font-size:15px;font-weight:700;color:var(--c-text,#111)">Bot de Telegram</div>
                            <div style="font-size:12.5px;color:#2CA5E0;font-weight:600;margin-top:3px;display:flex;align-items:center;gap:4px">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="11" height="11"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                {{ $telegramBotInfo }}
                            </div>
                        @else
                            <div style="font-size:15px;font-weight:700;color:var(--c-sub,#6b7280)">Sin bot conectado</div>
                            <div style="font-size:12px;color:var(--c-sub,#9ca3af);margin-top:2px">Pega tu token para empezar</div>
                        @endif
                    </div>

                    {{-- Status badge --}}
                    @if($telegramStatus === 'ok')
                        <span class="tg-status-badge tg-status-badge--ok">
                            <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block;animation:tg-pulse 2s ease-in-out infinite"></span>
                            Conectado
                        </span>
                    @elseif($telegramStatus === 'error')
                        <span class="tg-status-badge tg-status-badge--error">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                            Error
                        </span>
                    @else
                        <span class="tg-status-badge tg-status-badge--none">Sin conectar</span>
                    @endif
                </div>

                {{-- Divider + Token + Actions --}}
                <div style="border-top:1px solid var(--c-border,#e3e6ea);padding:16px 20px">
                    <div style="margin-bottom:10px">
                        <label class="ch-label" style="display:block;margin-bottom:5px">Bot Token</label>
                        <input type="password" class="ch-input" wire:model="telegramToken"
                            placeholder="{{ $telegramStatus === 'ok' ? '••••••••••• (configurado — pega nuevo para reemplazar)' : '123456:ABCdefGhIJKlmno…' }}"
                            autocomplete="off"
                            style="font-family:monospace">
                    </div>

                    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                        <button class="ch-btn ch-btn-outline" wire:click="testTelegram" wire:loading.attr="disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Probar
                        </button>
                        <button class="ch-btn ch-btn-primary" wire:click="saveTelegramToken" wire:loading.attr="disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Guardar token
                        </button>
                        <button class="ch-btn ch-btn-outline" wire:click="registerWebhook" wire:loading.attr="disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.1-1.1m2.657-9.435a4 4 0 015.656 5.656l-4 4a4 4 0 01-5.656 0l-1.1-1.1"/></svg>
                            Registrar webhook
                        </button>
                        @if($telegramStatus === 'ok')
                        <button class="ch-btn" style="margin-left:auto;background:transparent;color:#dc2626;border-color:rgba(220,38,38,.25)"
                            wire:click="disconnectTelegram"
                            wire:confirm="¿Desconectar el bot? Los mensajes de Telegram dejarán de recibirse.">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Desconectar
                        </button>
                        @endif
                    </div>

                    @if($msg)
                        <div class="ch-alert ch-alert-{{ $msgType }}" style="margin-top:10px">{{ $msg }}</div>
                    @endif
                </div>

                {{-- Steps footer --}}
                <div style="border-top:1px solid var(--c-border,#e3e6ea);background:var(--c-bg,#f8fafc);padding:13px 20px">
                    <div style="font-size:10px;font-weight:700;color:var(--c-sub,#9ca3af);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Cómo conectar</div>
                    <div class="ch-steps">
                        <div class="ch-step"><div class="ch-step-num">1</div><div>En Telegram busca <strong>@BotFather</strong>, escribe <code>/newbot</code> y copia el token.</div></div>
                        <div class="ch-step"><div class="ch-step-num">2</div><div>Pega el token, haz clic en <strong>Probar</strong> y luego <strong>Guardar token</strong>.</div></div>
                        <div class="ch-step"><div class="ch-step-num">3</div><div>Haz clic en <strong>Registrar webhook</strong> — apuntará el bot a <code>{{ $this->getWebhookUrl() }}</code>.</div></div>
                    </div>
                </div>
            </div>

            {{-- ══ CONFIG CARD (solo si conectado) ══ --}}
            @if($telegramStatus === 'ok')
            <div class="tg-config-card">

                {{-- Config Header --}}
                <div style="display:flex;align-items:center;gap:12px;padding:15px 20px;border-bottom:1px solid var(--c-border,#e3e6ea)">
                    <div class="tg-config-icon">
                        <svg fill="none" stroke="#6366f1" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <div style="font-size:13.5px;font-weight:700;color:var(--c-text,#111)">Configuración del Bot</div>
                        <div style="font-size:11.5px;color:var(--c-sub,#6b7280);margin-top:1px">Personalidad, respuestas rápidas y contexto para el asistente de Telegram</div>
                    </div>
                </div>

                <div style="padding:20px">

                    {{-- Toggle IA + WooCommerce --}}
                    <div class="nx-toggle-row">
                        <div style="flex:1;padding-right:24px">
                            <div class="nx-toggle__label">Asistente IA activo</div>
                            <div class="nx-toggle__desc">Si se desactiva, los mensajes solo llegarán a los agentes sin respuesta automática.</div>
                        </div>
                        <div class="nx-toggle__track {{ $telegramAiEnabled ? 'on' : '' }}" wire:click="$toggle('telegramAiEnabled')">
                            <div class="nx-toggle__thumb"></div>
                        </div>
                    </div>

                    <div class="nx-toggle-row">
                        <div style="flex:1;padding-right:24px">
                            <div class="nx-toggle__label">Contexto de tienda (WooCommerce)</div>
                            <div class="nx-toggle__desc">El bot podrá responder sobre catálogo, precios y métodos de pago del plugin.</div>
                        </div>
                        <div class="nx-toggle__track {{ $telegramUseStoreContext ? 'on' : '' }}" wire:click="$toggle('telegramUseStoreContext')">
                            <div class="nx-toggle__thumb"></div>
                        </div>
                    </div>

                    {{-- Prompt --}}
                    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--c-border,#e3e6ea)">
                        <label class="ch-label" style="display:block;margin-bottom:6px">Prompt del asistente</label>
                        <textarea class="ch-input" wire:model="telegramBotPrompt" rows="3"
                            placeholder="Ej: Eres un asistente amigable de la empresa. Nunca inventes precios..."
                            style="font-family:inherit;font-size:13px;resize:vertical"></textarea>
                        <div style="font-size:11px;color:var(--c-sub,#9ca3af);margin-top:4px">Instrucciones exclusivas para el canal Telegram. Se combinan con la base de conocimiento global.</div>
                    </div>

                    {{-- FAQ --}}
                    <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--c-border,#e3e6ea)">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px">
                            <div>
                                <label class="ch-label">Botones rápidos (FAQ)</label>
                                <div style="font-size:11.5px;color:var(--c-sub,#6b7280);margin-top:3px;line-height:1.5">Aparecen como teclado en Telegram. La respuesta se envía sin consumir tokens de IA.</div>
                            </div>
                            @if(count($telegramFaqItems) < 6)
                            <button class="ch-btn ch-btn-outline" wire:click="addTelegramFaq" style="padding:5px 10px;font-size:11px;flex-shrink:0;margin-left:12px">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                Añadir
                            </button>
                            @endif
                        </div>

                        <div style="margin-top:12px">
                            @forelse($telegramFaqItems as $idx => $faq)
                                <div class="faq-item">
                                    <div style="width:22px;height:22px;border-radius:6px;background:var(--c-border,#e3e6ea);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--c-sub,#6b7280);flex-shrink:0;margin-top:3px">{{ $idx+1 }}</div>
                                    <div style="flex:1;display:flex;flex-direction:column;gap:7px">
                                        <input type="text" class="ch-input" wire:model="telegramFaqItems.{{ $idx }}.question"
                                            placeholder="Texto del botón (Ej: Métodos de Pago)" style="font-family:inherit;font-size:12.5px">
                                        <textarea class="ch-input" wire:model="telegramFaqItems.{{ $idx }}.answer"
                                            rows="2" placeholder="Respuesta exacta que dará el bot..."
                                            style="font-family:inherit;font-size:12.5px;resize:vertical"></textarea>
                                    </div>
                                    <button type="button" wire:click="removeTelegramFaq({{ $idx }})"
                                        style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;flex-shrink:0;transition:color .1s"
                                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#9ca3af'" title="Eliminar">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @empty
                                <div style="text-align:center;padding:24px;color:var(--c-sub,#9ca3af);font-size:12.5px;background:var(--c-bg,#f8fafc);border-radius:8px;border:1.5px dashed var(--c-border,#e3e6ea)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="display:block;margin:0 auto 6px;opacity:.4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Sin botones rápidos — haz clic en "Añadir" para crear el primero
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Save button --}}
                    <div style="display:flex;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid var(--c-border,#e3e6ea)">
                        <button class="ch-btn ch-btn-primary" wire:click="saveTelegramConfig" wire:loading.attr="disabled">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Guardar configuración
                        </button>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── WhatsApp ── --}}
    <div class="ch-section ch-dimmed">
        <div class="ch-channel-meta">
            <div class="ch-channel-id">
                <div class="ch-channel-icon" style="background:#25D366">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <span class="ch-channel-name">WhatsApp</span>
                <span class="ch-coming-soon">Próximamente</span>
            </div>
            <p class="ch-channel-desc">Integración con WhatsApp Business API (Meta) con soporte para plantillas y sesiones de 24 h.</p>
        </div>
        <div class="ch-panel">
            <p style="font-size:13px;color:var(--c-sub,#6b7280);margin:0;line-height:1.6">
                Disponible en una próxima versión. Requiere acceso a la <strong>WhatsApp Business API</strong> con número verificado.
            </p>
        </div>
    </div>

    {{-- ── Web Widget ── --}}
    <div class="ch-section">
        <div class="ch-channel-meta">
            <div class="ch-channel-id">
                <div class="ch-channel-icon" style="background:#475569">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                </div>
                <span class="ch-channel-name">Web Widget</span>
            </div>
            <p class="ch-channel-desc">Añade el widget de chat a cualquier sitio web. Cada widget tiene su propio snippet de instalación con token único.</p>
        </div>
        <div class="ch-panel">
            <p style="font-size:13px;color:var(--c-text,#374151);line-height:1.6;margin:0 0 14px">
                Puedes crear varios widgets — uno por sitio web o marca. Cada uno tiene su propio snippet de código con token único que encuentras dentro de la configuración del widget.
            </p>
            <a href="{{ \App\Filament\Resources\ChatWidgetResource::getUrl('index') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#1e293b;color:#f8fafc;border-radius:7px;font-size:12.5px;font-weight:500;text-decoration:none"
               onmouseover="this.style.background='#0f172a'" onmouseout="this.style.background='#1e293b'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Ir a Widgets
            </a>
        </div>
    </div>

</div>
</x-filament-panels::page>
