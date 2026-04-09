<x-filament-panels::page>
<style>
.tfa-wrap {
    max-width: 380px;
    margin: 60px auto 0;
    background: #111827;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 16px;
    padding: 36px 32px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
    text-align: center;
    animation: tfa-fadein .4s cubic-bezier(.16,1,.3,1) both;
}
@keyframes tfa-fadein {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.tfa-icon {
    width: 52px; height: 52px;
    background: rgba(34,197,94,.1);
    border: 1px solid rgba(34,197,94,.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #22c55e;
}
.tfa-title { font-size: 17px; font-weight: 700; color: #f1f5f9; letter-spacing: -.02em; }
.tfa-sub   { font-size: 12.5px; color: rgba(255,255,255,.4); line-height: 1.6; max-width: 280px; }
.tfa-input {
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px;
    color: #fff;
    font-size: 22px;
    font-family: monospace;
    font-weight: 700;
    letter-spacing: .35em;
    padding: 13px 16px;
    text-align: center;
    width: 100%;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.tfa-input:focus {
    border-color: rgba(34,197,94,.5);
    box-shadow: 0 0 0 3px rgba(34,197,94,.08);
}
.tfa-input::placeholder { color: rgba(255,255,255,.2); letter-spacing: .25em; }
.tfa-btn {
    width: 100%;
    padding: 13px;
    background: #22c55e;
    color: #0d1117;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .12s, transform .1s, box-shadow .12s;
    box-shadow: 0 1px 2px rgba(0,0,0,.3);
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.tfa-btn:hover:not(:disabled) {
    background: #16a34a;
    box-shadow: 0 4px 14px rgba(34,197,94,.25);
    transform: translateY(-1px);
}
.tfa-btn:active:not(:disabled) { transform: translateY(0); }
.tfa-btn:disabled { opacity: .55; cursor: not-allowed; }
.tfa-error {
    width: 100%;
    padding: 10px 13px;
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.2);
    border-radius: 8px;
    font-size: 12.5px;
    color: #f87171;
    display: flex; align-items: center; gap: 8px;
}
.fi-page-header { display: none !important; }
</style>

<div class="tfa-wrap">
    <div class="tfa-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:24px;height:24px">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
    </div>

    <div>
        <div class="tfa-title">Verificación en dos pasos</div>
        <div class="tfa-sub">Introduce el código de 6 dígitos de tu app de autenticación.</div>
    </div>

    <input type="text" class="tfa-input" wire:model="code"
        placeholder="000000" maxlength="6" inputmode="numeric"
        wire:keydown.enter="verify" autofocus>

    @if($errorMsg)
        <div class="tfa-error">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ $errorMsg }}
        </div>
    @endif

    <button class="tfa-btn" wire:click="verify" wire:loading.attr="disabled">
        <svg wire:loading wire:target="verify" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15" style="display:none;animation:tfa-spin .8s linear infinite">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span wire:loading.remove wire:target="verify">Verificar acceso</span>
        <span wire:loading wire:target="verify" style="display:none">Verificando...</span>
    </button>

    <a href="{{ filament()->getLoginUrl() }}" style="font-size:12px;color:rgba(255,255,255,.35);text-decoration:none;transition:color .12s"
       onmouseover="this.style.color='rgba(255,255,255,.65)'" onmouseout="this.style.color='rgba(255,255,255,.35)'">
        ← Volver al inicio de sesión
    </a>
</div>
<style>
@keyframes tfa-spin { to { transform: rotate(360deg); } }
</style>
</x-filament-panels::page>
