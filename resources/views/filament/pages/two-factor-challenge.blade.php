<x-filament-panels::page>
<style>
.tfa-wrap {
    max-width: 380px;
    margin: 60px auto 0;
    background: #161b27;
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 16px;
    padding: 36px 32px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
    text-align: center;
}
.tfa-icon {
    width: 52px; height: 52px;
    background: rgba(124,58,237,.12);
    border: 1px solid rgba(124,58,237,.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a78bfa;
}
.tfa-title { font-size: 17px; font-weight: 700; color: #e2e8f0; }
.tfa-sub   { font-size: 12px; color: #64748b; line-height: 1.6; max-width: 280px; }
.tfa-input {
    background: #0f1117;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px;
    color: #e2e8f0;
    font-size: 20px;
    font-family: monospace;
    letter-spacing: .25em;
    padding: 12px 16px;
    text-align: center;
    width: 100%;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.tfa-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
.tfa-btn {
    width: 100%;
    padding: 11px;
    background: #1e293b;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
}
.tfa-btn:hover { background: #6d28d9; }
.tfa-error {
    width: 100%;
    padding: 9px 14px;
    background: rgba(239,68,68,.08);
    border: 1px solid rgba(239,68,68,.2);
    border-radius: 8px;
    font-size: 12px;
    color: #f87171;
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
        <div class="tfa-error">{{ $errorMsg }}</div>
    @endif

    <button class="tfa-btn" wire:click="verify" wire:loading.attr="disabled">
        Verificar acceso
    </button>

    <a href="{{ filament()->getLoginUrl() }}" style="font-size:11px;color:#475569;text-decoration:none;">
        Volver al inicio de sesión
    </a>
</div>
</x-filament-panels::page>
