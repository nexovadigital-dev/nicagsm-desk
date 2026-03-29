<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopedToOrganization
{
    /**
     * Return the authenticated user's organization_id (may be null for super-admins).
     */
    protected function orgId(): ?int
    {
        return auth()->user()?->organization_id;
    }

    /**
     * Apply organization scope to a query builder.
     * If orgId is null the scope is skipped (future super-admin support).
     */
    protected function scopeToOrg(Builder $query): Builder
    {
        $orgId = $this->orgId();
        return $orgId ? $query->where('organization_id', $orgId) : $query;
    }

    /**
     * Whether the current user is an owner or admin (can see all org data).
     */
    protected function isOrgAdmin(): bool
    {
        $role = auth()->user()?->role;
        return in_array($role, ['owner', 'admin']);
    }
}
