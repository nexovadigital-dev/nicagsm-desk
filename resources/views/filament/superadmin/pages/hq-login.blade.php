<div>
    <div class="auth-title">Panel de administración</div>
    <div class="auth-subtitle">Acceso restringido · Solo administradores del sistema.</div>

    @if($error)
        <div class="auth-error">{{ $error }}</div>
    @endif

    <div class="auth-field">
        <label class="auth-label">Email</label>
        <input type="email" wire:model.blur="email" class="auth-input"
               placeholder="admin@nexova.com" autocomplete="email"
               wire:keydown.enter="authenticate">
    </div>

    <div class="auth-field" style="margin-bottom:24px">
        <label class="auth-label">Contraseña</label>
        <input type="password" wire:model.blur="password" class="auth-input"
               placeholder="Contraseña de administrador" autocomplete="current-password"
               wire:keydown.enter="authenticate">
    </div>

    <button class="auth-btn" wire:click="authenticate" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="authenticate">Ingresar al panel</span>
        <span wire:loading wire:target="authenticate">Verificando...</span>
    </button>

    <div style="margin-top:28px;padding-top:20px;border-top:1px solid rgba(255,255,255,.07);text-align:center;font-size:12px;color:rgba(255,255,255,.2)">
        Nexova Desk HQ · Uso interno únicamente
    </div>
</div>
