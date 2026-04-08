<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\ChatWidget;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Organizations extends Page
{
    protected string $view = 'filament.superadmin.pages.organizations';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Organizaciones';
    protected static string|\UnitEnum|null $navigationGroup = 'Clientes';
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-building-office-2';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public string $search        = '';
    public string $filterPlan   = 'all';
    public string $filterStatus = 'all';   // 'all' | 'active' | 'inactive'
    public bool   $filterExpiry = false;   // true = vencimiento ≤ 7 días

    // Detail / edit modal
    public ?int   $viewingOrgId    = null;
    public string $editOrgName     = '';
    public string $editOrgPlan     = 'trial';
    public bool   $editOrgActive   = true;

    // AI keys (set by super admin per org)
    public bool   $editAiUseOwnKeys = false;
    public string $editAiGroqKey    = '';
    public string $editAiGeminiKey  = '';
    public bool   $editAiHasGroq    = false;
    public bool   $editAiHasGemini  = false;

    public function getOrganizationsProperty()
    {
        return Organization::with([
                'users'                   => fn ($q) => $q->where('role', 'owner'),
                'activeSubscription.plan',
            ])
            ->withCount(['users', 'chatWidgets'])
            ->when(trim($this->search), fn ($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterPlan !== 'all', fn ($q) => $q->where('plan', $this->filterPlan))
            ->when($this->filterStatus === 'active',   fn ($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($this->filterExpiry, fn ($q) =>
                $q->whereHas('activeSubscription', fn ($s) =>
                    $s->whereBetween('ends_at', [now(), now()->addDays(7)])
                )
            )
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function getPlansProperty()
    {
        return Plan::orderBy('sort')->get();
    }

    public function openOrg(int $id): void
    {
        $org = Organization::find($id);
        if (! $org) return;
        $this->viewingOrgId     = $id;
        $this->editOrgName      = $org->name;
        $this->editOrgPlan      = $org->plan;
        $this->editOrgActive    = $org->is_active;
        $this->editAiUseOwnKeys = (bool) $org->ai_use_own_keys;
        $this->editAiGroqKey    = '';
        $this->editAiGeminiKey  = '';
        // Check if keys are already configured (without exposing them)
        $this->editAiHasGroq    = (bool) $org->getRawOriginal('ai_groq_key');
        $this->editAiHasGemini  = (bool) $org->getRawOriginal('ai_gemini_key');
        $this->dispatch('open-org-modal');
    }

    public function saveOrg(): void
    {
        $org = Organization::find($this->viewingOrgId);
        if (! $org) return;

        // If plan changed, expire any active subscription
        if ($org->plan !== $this->editOrgPlan) {
            Subscription::where('organization_id', $org->id)
                ->where('status', 'active')
                ->update(['status' => 'expired']);
        }

        $data = [
            'name'           => trim($this->editOrgName) ?: $org->name,
            'plan'           => $this->editOrgPlan,
            'is_active'      => $this->editOrgActive,
            'ai_use_own_keys' => $this->editAiUseOwnKeys,
        ];

        if (trim($this->editAiGroqKey)) {
            $data['ai_groq_key'] = encrypt(trim($this->editAiGroqKey));
            $this->editAiHasGroq = true;
        }
        if (trim($this->editAiGeminiKey)) {
            $data['ai_gemini_key'] = encrypt(trim($this->editAiGeminiKey));
            $this->editAiHasGemini = true;
        }

        $org->update($data);
        $this->editAiGroqKey   = '';
        $this->editAiGeminiKey = '';

        $this->dispatch('close-org-modal');
        $this->dispatch('nexova-toast', type: 'success', message: 'Organización actualizada');
    }

    public function clearOrgAiKey(string $provider): void
    {
        $org = Organization::find($this->viewingOrgId);
        if (! $org) return;
        $field = $provider === 'groq' ? 'ai_groq_key' : 'ai_gemini_key';
        $org->update([$field => null, 'ai_use_own_keys' => false]);
        if ($provider === 'groq') {
            $this->editAiHasGroq = false;
        } else {
            $this->editAiHasGemini = false;
        }
        $this->dispatch('nexova-toast', type: 'success', message: 'Clave eliminada');
    }

    public function activatePlan(int $orgId, string $planSlug, int $months = 1): void
    {
        $org  = Organization::find($orgId);
        $plan = Plan::where('slug', $planSlug)->first();
        if (! $org || ! $plan) return;

        // Deactivate current active subscription
        Subscription::where('organization_id', $orgId)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $sub = Subscription::create([
            'organization_id' => $orgId,
            'plan_id'         => $plan->id,
            'status'          => 'active',
            'amount_usd'      => $plan->price_usd * $months,
            'starts_at'       => now(),
            'ends_at'         => now()->addMonths($months),
            'activated_by'    => auth()->id(),
            'notes'           => "Activado manualmente por super-admin",
        ]);

        $org->update([
            'plan'                     => $planSlug,
            'is_active'                => true,
            'trial_ends_at'            => null,
            'max_bot_sessions_per_day' => $plan->max_sessions_per_day,
            'max_messages_per_session' => $plan->max_messages_per_session,
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: "Plan {$plan->name} activado para {$org->name}");
    }

    public function toggleActive(int $id): void
    {
        $org = Organization::find($id);
        if (! $org) return;
        $org->update(['is_active' => ! $org->is_active]);
        $msg = $org->is_active ? 'Organización activada' : 'Organización desactivada';
        $this->dispatch('nexova-toast', type: 'success', message: $msg);
    }

    // ── Widgets modal ─────────────────────────────────────────────────────────
    public ?int $widgetsOrgId   = null;
    public string $widgetsOrgName = '';

    public function openWidgets(int $orgId): void
    {
        $org = Organization::find($orgId);
        if (! $org) return;
        $this->widgetsOrgId   = $orgId;
        $this->widgetsOrgName = $org->name;
        $this->dispatch('open-widgets-modal');
    }

    public function getOrgWidgetsProperty()
    {
        if (! $this->widgetsOrgId) return collect();
        return ChatWidget::where('organization_id', $this->widgetsOrgId)->get();
    }

    public function assignPartner(int $orgId): void
    {
        $org  = Organization::find($orgId);
        $plan = Plan::where('slug', 'partner')->first();
        if (! $org || ! $plan) return;

        // Expire current active subscription
        Subscription::where('organization_id', $orgId)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $sub = Subscription::create([
            'organization_id' => $orgId,
            'plan_id'         => $plan->id,
            'status'          => 'active',
            'amount_usd'      => 0,
            'starts_at'       => now(),
            'ends_at'         => now()->addYears(10), // effectively no expiry
            'activated_by'    => auth()->id(),
            'notes'           => 'Plan Partner — acceso ilimitado asignado manualmente.',
        ]);

        $org->update([
            'plan'                     => 'partner',
            'is_partner'               => true,
            'is_active'                => true,
            'trial_ends_at'            => null,
            'max_bot_sessions_per_day' => 9999,
            'max_messages_per_session' => 9999,
            'partner_token'            => $org->partner_token ?? Str::random(48),
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: "Plan Partner asignado a {$org->name}");
    }

    public function revokePartner(int $orgId): void
    {
        $org = Organization::find($orgId);
        if (! $org) return;

        $freePlan = Plan::where('slug', 'free')->first();

        Subscription::where('organization_id', $orgId)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $org->update([
            'plan'                     => 'free',
            'is_partner'               => false,
            'max_bot_sessions_per_day' => $freePlan?->max_sessions_per_day ?? 20,
            'max_messages_per_session' => $freePlan?->max_messages_per_session ?? 15,
        ]);

        $this->dispatch('nexova-toast', type: 'info', message: "Acceso Partner revocado para {$org->name}");
    }

    public function impersonate(int $orgId): void
    {
        // Log in as the owner of that organization (for debugging)
        $owner = User::where('organization_id', $orgId)->where('role', 'owner')->first();
        if (! $owner) {
            $this->dispatch('nexova-toast', type: 'error', message: 'No hay propietario en esta organización');
            return;
        }

        // Store super-admin ID in session to allow returning
        $adminId = auth()->id();
        session(['superadmin_impersonating' => $adminId]);

        Log::channel('stack')->info('SuperAdmin impersonation started', [
            'admin_id'    => $adminId,
            'admin_email' => auth()->user()->email,
            'org_id'      => $orgId,
            'owner_id'    => $owner->id,
            'owner_email' => $owner->email,
            'ip'          => request()->ip(),
        ]);

        auth()->login($owner);

        $this->redirect('/app');
    }
}
