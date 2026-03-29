<div>
@if($error)
    <div class="auth-title">Invitación inválida</div>
    <div class="auth-subtitle">Este enlace expiró o ya fue utilizado.</div>
    <div style="margin-top:24px;text-align:center">
        <a href="{{ route('auth.login') }}" class="auth-link">Ir al inicio de sesión</a>
    </div>

@elseif($accepted)
    <div class="auth-title">¡Bienvenido al equipo!</div>
    <div class="auth-subtitle">Tu cuenta ha sido creada. Redirigiendo...</div>

@else
    <div class="auth-title">Acepta la invitación</div>
    <div class="auth-subtitle">
        Únete a <strong style="color:rgba(255,255,255,.85)">{{ $invitation->organization->name }}</strong>
        como {{ $invitation->role === 'admin' ? 'Administrador' : 'Agente' }}.
    </div>

    <div style="margin:16px 0;padding:10px 14px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);border-radius:8px;font-size:13px;color:rgba(255,255,255,.7)">
        Ingresarás con el email: <strong style="color:#22c55e">{{ $invitation->email }}</strong>
    </div>

    @if($errors->any())
        <div class="auth-error">{{ $errors->first() }}</div>
    @endif

    <div class="auth-field">
        <label class="auth-label">Tu nombre</label>
        <input type="text" wire:model="name" class="auth-input" placeholder="Juan García" autocomplete="name">
    </div>

    <div class="auth-field">
        <label class="auth-label">Contraseña</label>
        <input type="password" wire:model="password" class="auth-input" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
    </div>

    <div class="auth-field" style="margin-bottom:22px">
        <label class="auth-label">Confirmar contraseña</label>
        <input type="password" wire:model="password_confirmation" class="auth-input" placeholder="Repite tu contraseña">
    </div>

    <button class="auth-btn" wire:click="submit" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="submit">Crear mi cuenta</span>
        <span wire:loading wire:target="submit">Creando cuenta...</span>
    </button>
@endif
</div>
