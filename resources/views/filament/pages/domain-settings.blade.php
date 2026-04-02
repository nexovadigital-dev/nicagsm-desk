<x-filament-panels::page>
<style>
.fi-page-header, .fi-breadcrumbs { display: none !important; }

.dm-wrap { padding: 32px 36px 64px; max-width: 1040px; }

.dm-section {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 32px;
    padding: 28px 0;
    border-top: 1px solid var(--c-border,#e3e6ea);
}
.dm-section:first-of-type { border-top: none; padding-top: 0; }
@media (max-width: 720px) { .dm-section { grid-template-columns: 1fr; gap: 12px; } }

.dm-section-label { padding-top: 2px; }
.dm-section-label h3 { font-size: 14px; font-weight: 600; color: var(--c-text,#111); margin: 0 0 6px; }
.dm-section-label p  { font-size: 12.5px; color: var(--c-sub,#6b7280); margin: 0; line-height: 1.6; }

.dm-field { display: flex; flex-direction: column; gap: 4px; margin-bottom: 12px; }
.dm-label { font-size: 10px; font-weight: 700; color: var(--c-sub,#6b7280); text-transform: uppercase; letter-spacing: .05em; }
.dm-input { background: var(--c-bg,#f5f6f8); border: 1px solid var(--c-border,#e3e6ea); border-radius: 7px; color: var(--c-text,#111); font-size: 13px; padding: 8px 11px; outline: none; width: 100%; font-family: inherit; transition: border-color .12s; box-sizing: border-box; }
.dm-input:focus { border-color: #16a34a; }

.dm-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 16px; border-radius: 7px; font-size: 12.5px; font-weight: 500; cursor: pointer; border: 1px solid transparent; font-family: inherit; transition: background .1s; }
.dm-btn-primary { background: #1e293b; color:#f8fafc; }
.dm-btn-primary:hover { background: #0f172a; }
.dm-btn-green { background: #16a34a; color: white; }
.dm-btn-green:hover { background: #15803d; }
.dm-btn-ghost { background: transparent; color: var(--c-sub,#6b7280); border-color: var(--c-border,#e3e6ea); }
.dm-btn-ghost:hover { background: var(--c-surf2,#f0f2f5); }
.dm-btn-danger { background: transparent; color: #dc2626; border-color: #fca5a5; }
.dm-btn-danger:hover { background: rgba(220,38,38,.06); }
.dm-actions { margin-top: 14px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.dm-notice { padding: 11px 14px; border-radius: 8px; font-size: 12px; line-height: 1.7; margin-bottom: 14px; }
.dm-notice-info    { background: rgba(59,130,246,.07); border: 1px solid rgba(59,130,246,.2); color: #1d4ed8; }
.dm-notice-success { background: rgba(5,150,105,.07); border: 1px solid rgba(5,150,105,.2); color: #059669; }
.dm-notice-error   { background: rgba(220,38,38,.07); border: 1px solid rgba(220,38,38,.2); color: #dc2626; }
.dm-notice-warn    { background: rgba(234,179,8,.07); border: 1px solid rgba(234,179,8,.25); color: #a16207; }

.dm-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 99px; font-size: 11px; font-weight: 600; }
.dm-badge-green  { background: rgba(5,150,105,.1); color: #059669; }
.dm-badge-yellow { background: rgba(234,179,8,.12); color: #a16207; }

.dm-txt-record {
    background: #1e293b;
    color: #f1f5f9;
    border-radius: 8px;
    padding: 14px 16px;
    font-family: monospace;
    font-size: 12px;
    line-height: 1.8;
    overflow-x: auto;
    margin: 12px 0;
}
.dm-txt-record .k { color: #94a3b8; }
.dm-txt-record .v { color: #86efac; }

.dm-steps { list-style: none; padding: 0; margin: 14px 0 0; counter-reset: steps; }
.dm-steps li { counter-increment: steps; padding: 6px 0 6px 28px; position: relative; font-size: 12.5px; color: var(--c-text,#111); border-bottom: 1px solid var(--c-border,#e3e6ea); }
.dm-steps li:last-child { border-bottom: none; }
.dm-steps li::before { content: counter(steps); position: absolute; left: 0; top: 7px; width: 18px; height: 18px; background: var(--c-border,#e3e6ea); border-radius: 50%; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
</style>

<div class="dm-wrap">
    <h1 style="font-size:22px;font-weight:700;color:var(--c-text,#111827);margin-bottom:4px;letter-spacing:-.02em">Dominio Propio</h1>
    <p style="font-size:13px;color:var(--c-sub,#6b7280);margin-bottom:28px">
        Verifica la titularidad de tu dominio para usar emails como <strong>soporte@tuempresa.com</strong> en tus tickets.
    </p>

    {{-- ── ¿Por qué verificar? ── --}}
    <div class="dm-section">
        <div class="dm-section-label">
            <h3>¿Para qué sirve?</h3>
            <p>La verificación demuestra que eres el dueño del dominio.</p>
        </div>
        <div>
            <div class="dm-notice dm-notice-info">
                <strong>Sin dominio verificado</strong>, los emails de tickets se envían desde
                <code style="background:rgba(0,0,0,.07);padding:1px 5px;border-radius:4px;font-size:11.5px">{{ \App\Services\OrgMailer::genericEmail(auth()->user()->organization) }}</code>
                usando el servidor de la plataforma. No puedes personalizar esta dirección.<br><br>
                <strong>Con dominio verificado</strong> + tu SMTP configurado, los emails salen desde
                <code style="background:rgba(0,0,0,.07);padding:1px 5px;border-radius:4px;font-size:11.5px">soporte@tudominio.com</code>
                con tu identidad de marca.
            </div>
        </div>
    </div>

    {{-- ── Dominio ── --}}
    <div class="dm-section">
        <div class="dm-section-label">
            <h3>Tu dominio</h3>
            <p>Solo el dominio raíz, sin www ni protocolo. Ej: <strong>tuempresa.com</strong></p>
        </div>
        <div>
            @if($domainVerified)
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                    <span style="font-size:15px;font-weight:600;color:var(--c-text,#111)">{{ $domain }}</span>
                    <span class="dm-badge dm-badge-green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="11" height="11">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                        Verificado
                    </span>
                </div>
                <div class="dm-notice dm-notice-success">
                    Dominio verificado. Ahora ve a <a href="{{ url('/app/smtp-settings') }}" style="text-decoration:underline;font-weight:600">Email &amp; SMTP</a> y configura tu servidor para enviar desde tu propio dominio.
                </div>
                <button class="dm-btn dm-btn-danger" wire:click="removeDomain" wire:confirm="¿Eliminar el dominio verificado? Tendrás que reverificar si lo agregas de nuevo.">
                    Eliminar dominio
                </button>
            @else
                <div class="dm-field">
                    <label class="dm-label">Dominio</label>
                    <input type="text" class="dm-input" wire:model="domain" placeholder="tuempresa.com" style="max-width:340px">
                </div>

                @if($verifyError)
                    <div class="dm-notice dm-notice-error">{{ $verifyError }}</div>
                @endif

                <div class="dm-actions">
                    <button class="dm-btn dm-btn-primary" wire:click="saveDomain" wire:loading.attr="disabled">
                        Guardar dominio
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Instrucciones DNS ── --}}
    @if($verifyToken && !$domainVerified)
    <div class="dm-section">
        <div class="dm-section-label">
            <h3>Registro DNS TXT</h3>
            <p>Agrega este registro en tu proveedor DNS (GoDaddy, Namecheap, Cloudflare, etc.) para probar titularidad.</p>
        </div>
        <div>
            <ol class="dm-steps">
                <li>Inicia sesión en el panel de tu proveedor DNS donde está registrado <strong>{{ $domain }}</strong>.</li>
                <li>Ve a la sección de gestión de DNS / Zona DNS.</li>
                <li>Crea un nuevo registro TXT con los siguientes datos:</li>
            </ol>

            <div class="dm-txt-record">
                <span class="k">Tipo:  </span><span class="v">TXT</span><br>
                <span class="k">Host:  </span><span class="v">@&nbsp;&nbsp;&nbsp;(o el dominio raíz)</span><br>
                <span class="k">Valor: </span><span class="v">{{ $verifyToken }}</span><br>
                <span class="k">TTL:   </span><span class="v">3600&nbsp;&nbsp;(o el mínimo disponible)</span>
            </div>

            <div class="dm-notice dm-notice-warn">
                Los cambios DNS pueden tardar entre <strong>15 minutos y 48 horas</strong> en propagarse. Una vez propagados, haz clic en "Verificar ahora".
            </div>

            @if($verifyError)
                <div class="dm-notice dm-notice-error">{{ $verifyError }}</div>
            @endif

            <div class="dm-actions">
                <button class="dm-btn dm-btn-green" wire:click="verifyDomain" wire:loading.attr="disabled">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verificar ahora
                </button>
                <span style="font-size:11.5px;color:var(--c-sub,#6b7280)" wire:loading wire:target="verifyDomain">Comprobando registro DNS...</span>
            </div>
        </div>
    </div>
    @endif

</div>
</x-filament-panels::page>
