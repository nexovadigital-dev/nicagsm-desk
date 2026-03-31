<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Page;
use Filament\Pages\Page as FilamentPage;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class PagesManager extends FilamentPage
{
    protected string $view = 'filament.superadmin.pages.pages-manager';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Páginas';
    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 35;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-document-text';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── List ──────────────────────────────────────────────────────────────────
    public string $search       = '';
    public string $filterStatus = 'all';

    public function getPagesProperty()
    {
        return Page::when(trim($this->search), fn ($q) =>
                $q->where('title', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('title')
            ->get();
    }

    // ── Form state ────────────────────────────────────────────────────────────
    public ?int   $editingId       = null;
    public string $formTitle       = '';
    public string $formSlug        = '';
    public string $formContent     = '';
    public string $formStatus      = 'draft';
    public string $formMetaTitle   = '';
    public string $formMetaDesc    = '';

    public function openCreate(): void
    {
        $this->editingId     = null;
        $this->formTitle     = '';
        $this->formSlug      = '';
        $this->formContent   = '';
        $this->formStatus    = 'draft';
        $this->formMetaTitle = '';
        $this->formMetaDesc  = '';
        $this->dispatch('open-page-modal');
    }

    public function openEdit(int $id): void
    {
        $page = Page::find($id);
        if (! $page) return;

        $this->editingId     = $page->id;
        $this->formTitle     = $page->title;
        $this->formSlug      = $page->slug;
        $this->formContent   = $page->content;
        $this->formStatus    = $page->status;
        $this->formMetaTitle = $page->meta_title ?? '';
        $this->formMetaDesc  = $page->meta_description ?? '';
        $this->dispatch('open-page-modal');
    }

    public function updatedFormTitle(string $value): void
    {
        if (! $this->editingId) {
            $this->formSlug = \Illuminate\Support\Str::slug($value);
        }
    }

    public function savePage(): void
    {
        $this->validate([
            'formTitle'   => 'required|min:2|max:255',
            'formSlug'    => 'required|alpha_dash|max:255',
            'formContent' => 'required|min:5',
            'formStatus'  => 'required|in:draft,published',
            'formMetaTitle' => 'nullable|max:70',
            'formMetaDesc'  => 'nullable|max:160',
        ]);

        $data = [
            'title'            => trim($this->formTitle),
            'slug'             => trim($this->formSlug),
            'content'          => $this->formContent,
            'status'           => $this->formStatus,
            'meta_title'       => trim($this->formMetaTitle) ?: null,
            'meta_description' => trim($this->formMetaDesc) ?: null,
        ];

        if ($this->editingId) {
            Page::where('id', $this->editingId)->update($data);
            $this->dispatch('nexova-toast', type: 'success', message: 'Página actualizada');
        } else {
            $base = $data['slug'];
            $i    = 1;
            while (Page::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $base . '-' . $i++;
            }
            Page::create($data);
            $this->dispatch('nexova-toast', type: 'success', message: 'Página creada');
        }

        $this->dispatch('close-page-modal');
    }

    public function deletePage(int $id): void
    {
        Page::destroy($id);
        $this->dispatch('nexova-toast', type: 'info', message: 'Página eliminada');
    }

    public function toggleStatus(int $id): void
    {
        $page = Page::find($id);
        if (! $page) return;
        $page->update(['status' => $page->status === 'published' ? 'draft' : 'published']);
        $msg = $page->fresh()->status === 'published' ? 'Página publicada' : 'Página despublicada';
        $this->dispatch('nexova-toast', type: 'success', message: $msg);
    }
}
