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
.ch-channel-id {
    display: flex; align-items: center; gap: 10px;
}
.ch-channel-icon {
    width: 36px; height: 36px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: #fff; flex-shrink: 0;
}
.ch-channel-name { font-size: 14px; font-weight: 600; color: var(--c-text,#111); }
.ch-channel-desc { font-size: 12.5px; color: var(--c-sub,#6b7280); line-height: 1.6; margin-top: 6px; }

.ch-panel {
    background: var(--c-surface,#fff);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 10px;
    padding: 18px 20px;
}

.ch-field { display: flex; flex-direction: column; gap: 4px; margin-bottom: 14px; }
.ch-field:last-child { margin-bottom: 0; }
.ch-label { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform:uppercase; letter-spacing:.05em; }
.ch-input {
    background: var(--c-bg,#f5f6f8);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 7px; color: var(--c-text,#111);
    font-size: 13px; font-family: monospace;
    padding: 8px 11px; outline: none; width: 100%;
    transition: border-color .12s; box-sizing: border-box;
}
.ch-input:focus { border-color: #16a34a; }

.ch-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 14px; }
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

.ch-status {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 4px 10px;
    border-radius: 99px;
}
.ch-status-ok    { background: rgba(5,150,105,.08); color:#059669; border:1px solid rgba(5,150,105,.15); }
.ch-status-error { background: rgba(220,38,38,.08); color:#dc2626; border:1px solid rgba(220,38,38,.15); }

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
.ch-step a { color: #2563eb; text-decoration: none; }
.ch-step code { background: var(--c-bg,#f5f6f8); padding: 1px 5px; border-radius: 4px; font-size: 11px; }

.ch-coming-soon {
    display: inline-flex; align-items: center;
    font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280);
    background: var(--c-surf2,#f0f2f5); border: 1px solid var(--c-border,#e3e6ea);
    padding: 3px 9px; border-radius: 99px; letter-spacing: .03em;
}
.ch-dimmed { opacity: .65; }

.ch-code-block {
    background: var(--c-bg,#f5f6f8);
    border: 1px solid var(--c-border,#e3e6ea);
    border-radius: 8px; padding: 11px 13px;
    font-size: 11.5px; overflow-x: auto;
    color: var(--c-text,#111); white-space: pre-wrap; word-break: break-all;
    font-family: ui-monospace, monospace;
}
</style>

<div class="ch-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:28px;letter-spacing:-.02em">Canales</h1>

    {{-- ── Telegram ── --}}
    <div class="ch-section">
        <div class="ch-channel-meta">
            <div class="ch-channel-id">
                <div class="ch-channel-icon" style="background:#2CA5E0">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                    </svg>
                </div>
                <span class="ch-channel-name">Telegram</span>
            </div>
            <p class="ch-channel-desc">Conecta un bot de Telegram para recibir mensajes directamente en el panel.</p>
            @if($telegramStatus === 'ok')
                <span class="ch-status ch-status-ok">
                    <svg fill="currentColor" viewBox="0 0 20 20" width="9" height="9"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    {{ $telegramBotInfo }}
                </span>
            @elseif($telegramStatus === 'error')
                <span class="ch-status ch-status-error">Sin conexión</span>
            @endif
        </div>

        <div class="ch-panel">
            <div class="ch-field">
                <label class="ch-label">Bot Token</label>
                <input type="text" class="ch-input" wire:model="telegramToken" placeholder="123456:ABCdefGhIJKlmno…" autocomplete="off">
            </div>
            <div class="ch-actions">
                <button class="ch-btn ch-btn-outline" wire:click="testTelegram" wire:loading.attr="disabled">
                    Probar conexión
                </button>
                <button class="ch-btn ch-btn-primary" wire:click="registerWebhook" wire:loading.attr="disabled">
                    Registrar webhook
                </button>
                <button class="ch-btn ch-btn-outline" wire:click="saveTelegramToken" wire:loading.attr="disabled">
                    Guardar token
                </button>
            </div>
            <div class="ch-steps">
                <div class="ch-step">
                    <div class="ch-step-num">1</div>
                    <div>En Telegram busca <strong>@BotFather</strong>, escribe <code>/newbot</code> y copia el token.</div>
                </div>
                <div class="ch-step">
                    <div class="ch-step-num">2</div>
                    <div>Pega el token y haz clic en <strong>Probar conexión</strong>.</div>
                </div>
                <div class="ch-step">
                    <div class="ch-step-num">3</div>
                    <div>Haz clic en <strong>Registrar webhook</strong> — apuntará el bot a <code>{{ url('/api/webhook/telegram') }}</code>.</div>
                </div>
            </div>
            @if($msg)
                <div class="ch-alert ch-alert-{{ $msgType }}" style="margin-top:14px">{{ $msg }}</div>
            @endif
        </div>
    </div>

    {{-- ── WhatsApp ── --}}
    <div class="ch-section ch-dimmed">
        <div class="ch-channel-meta">
            <div class="ch-channel-id">
                <div class="ch-channel-icon" style="background:#25D366">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="17" height="17">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
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
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                    </svg>
                </div>
                <span class="ch-channel-name">Web Widget</span>
            </div>
            <p class="ch-channel-desc">Añade el widget de chat a cualquier sitio web con un snippet de código.</p>
        </div>
        <div class="ch-panel">
            <div style="font-size:12px;color:var(--c-sub);margin-bottom:10px">
                Pega este código en el <code style="background:var(--c-bg);padding:1px 5px;border-radius:4px;font-size:11px">&lt;head&gt;</code> de tu sitio:
            </div>
            <pre class="ch-code-block"><code>&lt;div id="nexova-chat-root"&gt;&lt;/div&gt;
@vite(['resources/css/widget.css','resources/js/widget/widget.jsx'])
&lt;script&gt;window.NexovaChatConfig={apiUrl:"{{ url('/') }}"}&lt;/script&gt;</code></pre>
        </div>
    </div>

</div>
</x-filament-panels::page>
