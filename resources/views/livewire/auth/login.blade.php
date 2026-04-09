<div>
    <div class="auth-title">Bienvenido de vuelta</div>
    <div class="auth-subtitle">Inicia sesión en tu panel de Nexova Desk.</div>

    @if($error)
        <div class="auth-error">{{ $error }}</div>
    @endif

    <div class="auth-field">
        <label class="auth-label">Email</label>
        <input type="email" wire:model="email" class="auth-input"
               placeholder="tu@empresa.com" autocomplete="email"
               wire:keydown.enter="submit">
    </div>

    <div class="auth-field" style="margin-bottom:8px">
        <label class="auth-label">Contraseña</label>
        <input type="password" wire:model="password" class="auth-input"
               placeholder="Tu contraseña" autocomplete="current-password"
               wire:keydown.enter="submit">
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
        <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;color:rgba(255,255,255,.5)">
            <input type="checkbox" wire:model="remember" style="accent-color:#22c55e">
            Recordarme
        </label>
        <a href="{{ route('auth.forgot') }}" class="auth-link" style="font-size:13px">¿Olvidaste tu contraseña?</a>
    </div>

    <button class="auth-btn" wire:click="submit" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="submit">Iniciar sesión</span>
        <span wire:loading wire:target="submit">Entrando...</span>
    </button>

</div>
