<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Models\AgentInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AcceptInvitation extends Component
{
    public string $token = '';
    public ?AgentInvitation $invitation = null;

    public string $name     = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $error    = false;
    public bool $accepted = false;

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->invitation = AgentInvitation::where('token', $token)
                                           ->whereNull('accepted_at')
                                           ->where('expires_at', '>', now())
                                           ->with('organization')
                                           ->first();

        if (! $this->invitation) {
            $this->error = true;
        }
    }

    public function submit(): void
    {
        if ($this->error || ! $this->invitation) return;

        $this->validate([
            'name'     => 'required|string|min:2|max:100',
            'password' => 'required|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name'             => $this->name,
            'email'            => $this->invitation->email,
            'password'         => Hash::make($this->password),
            'organization_id'  => $this->invitation->organization_id,
            'role'             => $this->invitation->role,
            'permissions'      => $this->invitation->permissions ?? \App\Filament\Pages\AgentsPage::defaultPermissions(),
            'email_verified_at'=> now(),
            'email_verified_otp' => true,
        ]);

        // Mark invitation as accepted
        $this->invitation->update(['accepted_at' => now()]);

        // Log in the new user
        Auth::login($user);

        $this->accepted = true;

        $this->redirect('/admin', navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.accept-invitation')
            ->layout('layouts.auth');
    }
}
