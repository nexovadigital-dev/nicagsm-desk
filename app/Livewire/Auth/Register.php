<?php

namespace App\Livewire\Auth;

use App\Models\EmailVerification;
use App\Models\Organization;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class Register extends Component
{
    // Step 1
    public string $name     = '';
    public string $email    = '';
    public string $password = '';
    public string $orgName  = '';

    // Step 2 — OTP
    public string $otp = '';

    public string $step = 'form'; // form | verify

    public string $error   = '';
    public string $success = '';

    public function submit(): void
    {
        $this->error   = '';
        $this->success = '';

        // Check if registrations are open
        $settings = SystemSetting::instance();
        if (! $settings->allow_registrations) {
            $this->error = $settings->registration_closed_message;
            return;
        }

        // Basic validation
        if (strlen(trim($this->name)) < 2) { $this->error = 'El nombre es obligatorio.'; return; }
        if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) { $this->error = 'Email inválido.'; return; }
        if (strlen($this->password) < 8) { $this->error = 'La contraseña debe tener mínimo 8 caracteres.'; return; }
        if (strlen(trim($this->orgName)) < 2) { $this->error = 'El nombre de tu empresa/marca es obligatorio.'; return; }

        if (User::where('email', $this->email)->exists()) {
            $this->error = 'Este email ya está registrado.';
            return;
        }

        // Generate OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store pending data
        EmailVerification::where('email', $this->email)->delete();
        EmailVerification::create([
            'email'        => $this->email,
            'otp'          => Hash::make($otp),
            'pending_data' => [
                'name'     => trim($this->name),
                'password' => Hash::make($this->password),
                'org_name' => trim($this->orgName),
            ],
            'expires_at' => now()->addMinutes(15),
        ]);

        // Send email
        try {
            Mail::raw(
                "Tu código de verificación para Nexova Desk es: {$otp}\n\nVence en 15 minutos.",
                fn ($m) => $m->to($this->email)->subject('Código de verificación — Nexova Desk')
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("OTP email failed for {$this->email}: {$e->getMessage()} — OTP: {$otp}");
            // En producción sin SMTP configurado, mostrar mensaje genérico (no exponer OTP)
            if (app()->isLocal()) {
                $this->success = "Dev mode — OTP: {$otp}";
            }
        }

        $this->step = 'verify';
    }

    public function verify(): void
    {
        $this->error = '';

        $record = EmailVerification::where('email', $this->email)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record || ! Hash::check($this->otp, $record->otp)) {
            $this->error = 'Código incorrecto o expirado.';
            return;
        }

        $data = $record->pending_data;

        // Create organization
        $org = Organization::create([
            'name'          => $data['org_name'],
            'plan'          => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        // Create owner user
        $user = User::create([
            'organization_id'    => $org->id,
            'role'               => 'owner',
            'name'               => $data['name'],
            'email'              => $this->email,
            'password'           => $data['password'],
            'email_verified_at'  => now(),
            'email_verified_otp' => true,
            'availability'       => 'online',
        ]);

        $record->delete();

        // Auto-login and redirect to admin
        auth()->login($user);

        $this->redirect('/app');
    }

    public function resend(): void
    {
        $this->otp   = '';
        $this->error = '';
        $this->step  = 'form';
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.auth');
    }
}
