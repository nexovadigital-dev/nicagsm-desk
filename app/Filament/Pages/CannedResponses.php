<?php
declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\CannedResponse;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;

class CannedResponses extends Page
{
    use ScopedToOrganization;
    protected string $view = 'filament.pages.canned-responses';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Respuestas Rápidas';
    protected static string|\UnitEnum|null $navigationGroup = 'Agentes';
    protected static ?int $navigationSort = 30;

    // ── Create form ───────────────────────────────────────────────────────
    public string $newShortcut = '';
    public string $newContent  = '';

    // ── Search ────────────────────────────────────────────────────────────
    public string $search = '';

    // ── Inline edit ───────────────────────────────────────────────────────
    public ?int   $editingId    = null;
    public string $editShortcut = '';
    public string $editContent  = '';

    // ──────────────────────────────────────────────────────────────────────

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-bolt';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Respuestas Rápidas';
    }

    /**
     * Computed property — the blade uses $this->cannedResponses
     */
    public function getCannedResponsesProperty(): Collection
    {
        $query = $this->scopeToOrg(CannedResponse::query())->orderBy('shortcut');

        if ($this->search !== '') {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('shortcut', 'like', $term)
                  ->orWhere('content', 'like', $term);
            });
        }

        return $query->get();
    }

    public function create(): void
    {
        $shortcut = trim($this->newShortcut);
        $content  = trim($this->newContent);

        if ($shortcut === '' || $content === '') {
            $this->dispatch('nexova-toast', type: 'error', message: 'El atajo y el contenido son obligatorios');
            return;
        }

        $exists = $this->scopeToOrg(CannedResponse::where('shortcut', $shortcut))->exists();
        if ($exists) {
            $this->dispatch('nexova-toast', type: 'error', message: "El atajo /{$shortcut} ya existe");
            return;
        }

        CannedResponse::create([
            'shortcut'        => $shortcut,
            'content'         => $content,
            'organization_id' => $this->orgId(),
        ]);

        $this->newShortcut = '';
        $this->newContent  = '';

        $this->dispatch('nexova-toast', type: 'success', message: 'Respuesta rápida agregada');
    }

    public function startEdit(int $id): void
    {
        $item = CannedResponse::find($id);

        if (! $item) {
            return;
        }

        $this->editingId    = $id;
        $this->editShortcut = $item->shortcut;
        $this->editContent  = $item->content;
    }

    public function saveEdit(): void
    {
        $item = CannedResponse::find($this->editingId);

        if (! $item) {
            $this->cancelEdit();
            return;
        }

        $shortcut = trim($this->editShortcut);
        $content  = trim($this->editContent);

        if ($shortcut === '' || $content === '') {
            $this->dispatch('nexova-toast', type: 'error', message: 'El atajo y el contenido son obligatorios');
            return;
        }

        $duplicate = CannedResponse::where('shortcut', $shortcut)
            ->where('id', '!=', $this->editingId)
            ->exists();

        if ($duplicate) {
            $this->dispatch('nexova-toast', type: 'error', message: "El atajo /{$shortcut} ya está en uso");
            return;
        }

        $item->update([
            'shortcut' => $shortcut,
            'content'  => $content,
        ]);

        $this->cancelEdit();
        $this->dispatch('nexova-toast', type: 'success', message: 'Respuesta actualizada');
    }

    public function cancelEdit(): void
    {
        $this->editingId    = null;
        $this->editShortcut = '';
        $this->editContent  = '';
    }

    public function delete(int $id): void
    {
        CannedResponse::find($id)?->delete();

        if ($this->editingId === $id) {
            $this->cancelEdit();
        }

        $this->dispatch('nexova-toast', type: 'success', message: 'Respuesta eliminada');
    }

    // ── Aliases for backward compatibility ────────────────────────────────

    public function add(): void
    {
        $this->create();
    }

    public function update(int $id): void
    {
        $this->editingId = $id;
        $this->saveEdit();
    }
}
