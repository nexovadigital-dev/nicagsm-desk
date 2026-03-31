<div>
    @if($step === 'email')
        <div class="auth-title">Recuperar contraseña</div>
        <div class="auth-subtitle">Ingresa tu email y te enviaremos un código de verificación.</div>

        @if($error)
            <div class="auth-error">{{ $error }}</div>
        @endif

        <div class="auth-field">
            <label class="auth-label">Email</label>
            <input type="email" wire:model="email" class="auth-input"
                   placeholder="tu@empresa.com" autocomplete="email"
                   wire:keydown.enter="sendCode">
        </div>

        <button class="auth-btn" wire:click="sendCode" wire:loading.attr="disabled" style="margin-top:8px">
            <span wire:loading.remove wire:target="sendCode">Enviar código</span>
            <span wire:loading wire:target="sendCode">Enviando...</span>
        </button>

        <div class="auth-link-row">
            <a href="{{ route('auth.login') }}" class="auth-link">← Volver al login</a>
        </div>

    @elseif($step === 'code')
        <div class="auth-title">Código de verificación</div>
        <div class="auth-subtitle">Revisa tu bandeja de entrada. El código expira en 15 minutos.</div>

        @if($success)
            <div class="auth-success">{{ $success }}</div>
        @endif
        @if($error)
            <div class="auth-error">{{ $error }}</div>
        @endif

        <div class="auth-field">
            <label class="auth-label">Código de 6 dígitos</label>
            <input type="text" wire:model="code" class="auth-input otp-input"
                   placeholder="XXXXXX" maxlength="6" autocomplete="one-time-code"
                   wire:keydown.enter="verifyCode">
        </div>
        <div class="otp-hint">Enviamos el código a <strong style="color:rgba(255,255,255,.7)">{{ $email }}</strong></div>

        <button class="auth-btn" wire:click="verifyCode" wire:loading.attr="disabled" style="margin-top:20px">
            <span wire:loading.remove wire:target="verifyCode">Verificar código</span>
            <span wire:loading wire:target="verifyCode">Verificando...</span>
        </button>

        <div class="auth-link-row">
            <a href="{{ route('auth.forgot') }}" class="auth-link">Reenviar código</a>
        </div>

    @elseif($step === 'reset')
        <div class="auth-title">Nueva contraseña</div>
        <div class="auth-subtitle">Elige una contraseña segura de al menos 8 caracteres.</div>

        @if($error)
            <div class="auth-error">{{ $error }}</div>
        @endif

        <div class="auth-field">
            <label class="auth-label">Nueva contraseña</label>
            <input type="password" wire:model="newPass" class="auth-input"
                   placeholder="Mínimo 8 caracteres" autocomplete="new-password"
                   wire:keydown.enter="resetPassword">
        </div>

        <div class="auth-field">
            <label class="auth-label">Confirmar contraseña</label>
            <input type="password" wire:model="confirm" class="auth-input"
                   placeholder="Repite la contraseña" autocomplete="new-password"
                   wire:keydown.enter="resetPassword">
        </div>

        <button class="auth-btn" wire:click="resetPassword" wire:loading.attr="disabled" style="margin-top:8px">
            <span wire:loading.remove wire:target="resetPassword">Guardar contraseña</span>
            <span wire:loading wire:target="resetPassword">Guardando...</span>
        </button>

    @elseif($step === 'done')
        <div class="auth-title">¡Contraseña actualizada!</div>
        <div class="auth-subtitle">Tu contraseña fue cambiada correctamente. Ya puedes iniciar sesión.</div>

        @if($success)
            <div class="auth-success">{{ $success }}</div>
        @endif

        <a href="{{ route('auth.login') }}" class="auth-btn" style="margin-top:8px;text-decoration:none;justify-content:center">
            Iniciar sesión
        </a>
    @endif
</div>
