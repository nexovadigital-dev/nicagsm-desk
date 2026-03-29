<div>
@if($step === 'form')
    {{-- Step 1: Formulario --}}
    <div class="trial-badge">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="10" height="10" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        14 días gratis · Sin tarjeta de crédito
    </div>

    <div class="auth-title">Crea tu cuenta</div>
    <div class="auth-subtitle">Empieza a atender a tus clientes con IA en minutos.</div>

    @if($error)
        <div class="auth-error">{{ $error }}</div>
    @endif

    <div class="auth-field">
        <label class="auth-label">Tu nombre</label>
        <input type="text" wire:model="name" class="auth-input" placeholder="Juan García" autocomplete="name">
    </div>

    <div class="auth-field">
        <label class="auth-label">Nombre de tu empresa / marca</label>
        <input type="text" wire:model="orgName" class="auth-input" placeholder="Mi Tienda Online">
    </div>

    <div class="auth-field">
        <label class="auth-label">Email de trabajo</label>
        <input type="email" wire:model="email" class="auth-input" placeholder="tu@empresa.com" autocomplete="email">
    </div>

    <div class="auth-field" style="margin-bottom:22px">
        <label class="auth-label">Contraseña</label>
        <input type="password" wire:model="password" class="auth-input" placeholder="Mínimo 8 caracteres" autocomplete="new-password">
    </div>

    <button class="auth-btn" wire:click="submit" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="submit">Crear cuenta gratis</span>
        <span wire:loading wire:target="submit">Enviando código...</span>
    </button>

    <div class="auth-link-row">
        ¿Ya tienes cuenta? <a href="{{ route('auth.login') }}" class="auth-link">Inicia sesión</a>
    </div>

@elseif($step === 'verify')
    {{-- Step 2: OTP --}}
    <div class="auth-title">Verifica tu email</div>
    <div class="auth-subtitle">Enviamos un código de 6 dígitos a<br><strong style="color:rgba(255,255,255,.7)">{{ $email }}</strong></div>

    @if($error)
        <div class="auth-error">{{ $error }}</div>
    @endif
    @if($success)
        <div class="auth-success">{{ $success }}</div>
    @endif

    <div class="auth-field" style="margin-bottom:22px">
        <label class="auth-label">Código de verificación</label>
        <input type="text" wire:model="otp" class="auth-input otp-input"
               placeholder="000000" maxlength="6"
               autocomplete="one-time-code" inputmode="numeric">
        <div class="otp-hint">Revisa tu bandeja de entrada y spam.<br>El código vence en 15 minutos.</div>
    </div>

    <button class="auth-btn" wire:click="verify" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="verify">Verificar y entrar</span>
        <span wire:loading wire:target="verify">Verificando...</span>
    </button>

    <div class="auth-link-row">
        <button type="button" wire:click="resend" style="background:none;border:none;cursor:pointer;padding:0" class="auth-link">
            ¿No recibiste el código? Volver atrás
        </button>
    </div>
@endif
</div>
