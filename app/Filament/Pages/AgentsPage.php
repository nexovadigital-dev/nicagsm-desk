<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\AgentInvitation;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AgentsPage extends Page
{
    protected string $view = 'filament.pages.agents-page';

    protected Width|string|null $maxContentWidth = 'full';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel  = 'Agentes';
    protected static string|\UnitEnum|null $navigationGroup = 'Agentes';
    protected static ?int    $navigationSort   = 10;

    // Invite form
    public string $inviteEmail = '';
    public string $inviteRole  = 'agent';

    // Edit permissions modal
    public ?int   $editingUserId    = null;
    public array  $editPermissions  = [];

    public function getTitle(): string|Htmlable
    {
        return 'Agentes';
    }

    public function getAgents(): \Illuminate\Database\Eloquent\Collection
    {
        $orgId = Auth::user()->organization_id;

        return User::where('organization_id', $orgId)
                   ->where('id', '!=', Auth::id())
                   ->orderBy('name')
                   ->get();
    }

    public function getPendingInvitations(): \Illuminate\Database\Eloquent\Collection
    {
        $orgId = Auth::user()->organization_id;

        return AgentInvitation::where('organization_id', $orgId)
                              ->whereNull('accepted_at')
                              ->where('expires_at', '>', now())
                              ->orderByDesc('created_at')
                              ->get();
    }

    public function sendInvitation(): void
    {
        $this->validate([
            'inviteEmail' => 'required|email',
            'inviteRole'  => 'required|in:admin,agent',
        ]);

        $user = Auth::user();

        // Prevent duplicate pending invitations
        $exists = AgentInvitation::where('organization_id', $user->organization_id)
                                 ->where('email', $this->inviteEmail)
                                 ->whereNull('accepted_at')
                                 ->where('expires_at', '>', now())
                                 ->exists();

        if ($exists) {
            $this->dispatch('nexova-toast', type: 'warning', message: 'Ya hay una invitación pendiente para ese email.');
            return;
        }

        $invitation = AgentInvitation::create([
            'organization_id' => $user->organization_id,
            'invited_by'      => $user->id,
            'email'           => $this->inviteEmail,
            'role'            => $this->inviteRole,
        ]);

        // Send invitation email
        try {
            Mail::to($this->inviteEmail)->send(new \App\Mail\AgentInvitationMail($invitation));
        } catch (\Throwable $e) {
            // Log but don't fail — show the link in the UI too
        }

        $this->inviteEmail = '';
        $this->inviteRole  = 'agent';

        $this->dispatch('nexova-toast', type: 'success', message: 'Invitación enviada a ' . $invitation->email);
    }

    public function revokeInvitation(int $id): void
    {
        $orgId = Auth::user()->organization_id;
        AgentInvitation::where('id', $id)->where('organization_id', $orgId)->delete();
        $this->dispatch('nexova-toast', type: 'info', message: 'Invitación revocada.');
    }

    public function removeAgent(int $id): void
    {
        $orgId = Auth::user()->organization_id;
        $agent = User::where('id', $id)->where('organization_id', $orgId)->first();

        if (! $agent || $agent->isOwner()) return;

        $agent->update(['organization_id' => null, 'role' => null]);
        $this->dispatch('nexova-toast', type: 'success', message: 'Agente removido de la organización.');
    }

    public function openPermissions(int $id): void
    {
        $orgId = Auth::user()->organization_id;
        $agent = User::where('id', $id)->where('organization_id', $orgId)->first();
        if (! $agent) return;

        $this->editingUserId   = $id;
        $this->editPermissions = $agent->permissions ?? self::defaultPermissions();
        $this->dispatch('open-permissions-modal');
    }

    public function savePermissions(): void
    {
        if (! $this->editingUserId) return;

        $orgId = Auth::user()->organization_id;
        $agent = User::where('id', $this->editingUserId)->where('organization_id', $orgId)->first();
        if (! $agent) return;

        $agent->update(['permissions' => $this->editPermissions]);
        $this->editingUserId = null;
        $this->dispatch('close-permissions-modal');
        $this->dispatch('nexova-toast', type: 'success', message: 'Permisos actualizados.');
    }

    public static function defaultPermissions(): array
    {
        return [
            'view_inbox'          => true,
            'reply_messages'      => true,
            'close_tickets'       => true,
            'assign_tickets'      => false,
            'view_knowledge_base' => true,
            'edit_knowledge_base' => false,
            'view_reports'        => false,
        ];
    }
}
