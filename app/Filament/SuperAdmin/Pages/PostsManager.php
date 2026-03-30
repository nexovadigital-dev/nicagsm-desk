<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Post;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class PostsManager extends Page
{
    protected string $view = 'filament.superadmin.pages.posts-manager';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Blog / Novedades';
    protected static string|\UnitEnum|null $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 30;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-newspaper';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── List ──────────────────────────────────────────────────────────────────
    public string $search      = '';
    public string $filterStatus = 'all';

    public function getPostsProperty()
    {
        return Post::when(trim($this->search), fn ($q) =>
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterStatus !== 'all', fn ($q) => $q->where('status', $this->filterStatus))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    // ── Form state ────────────────────────────────────────────────────────────
    public ?int    $editingId    = null;
    public string  $formTitle    = '';
    public string  $formSlug     = '';
    public string  $formExcerpt  = '';
    public string  $formBody     = '';
    public string  $formCover    = '';
    public string  $formCategory = 'novedad';
    public string  $formStatus   = 'draft';
    public string  $formDate     = '';

    public function openCreate(): void
    {
        $this->editingId   = null;
        $this->formTitle   = '';
        $this->formSlug    = '';
        $this->formExcerpt = '';
        $this->formBody    = '';
        $this->formCover   = '';
        $this->formCategory = 'novedad';
        $this->formStatus  = 'draft';
        $this->formDate    = now()->format('Y-m-d\TH:i');
        $this->dispatch('open-post-modal');
    }

    public function openEdit(int $id): void
    {
        $post = Post::find($id);
        if (! $post) return;

        $this->editingId   = $post->id;
        $this->formTitle   = $post->title;
        $this->formSlug    = $post->slug;
        $this->formExcerpt = $post->excerpt ?? '';
        $this->formBody    = $post->body;
        $this->formCover   = $post->cover_image ?? '';
        $this->formCategory = $post->category;
        $this->formStatus  = $post->status;
        $this->formDate    = $post->published_at
            ? $post->published_at->format('Y-m-d\TH:i')
            : now()->format('Y-m-d\TH:i');

        $this->dispatch('open-post-modal');
    }

    public function updatedFormTitle(string $value): void
    {
        // Auto-generate slug only when creating a new post
        if (! $this->editingId) {
            $this->formSlug = \Illuminate\Support\Str::slug($value);
        }
    }

    public function savePost(): void
    {
        $this->validate([
            'formTitle'    => 'required|min:3|max:255',
            'formSlug'     => 'required|alpha_dash|max:255',
            'formBody'     => 'required|min:10',
            'formCategory' => 'required|in:novedad,evento,producto,actualización',
            'formStatus'   => 'required|in:draft,published',
        ]);

        $data = [
            'title'        => trim($this->formTitle),
            'slug'         => trim($this->formSlug),
            'excerpt'      => trim($this->formExcerpt) ?: null,
            'body'         => $this->formBody,
            'cover_image'  => trim($this->formCover) ?: null,
            'category'     => $this->formCategory,
            'status'       => $this->formStatus,
            'published_at' => $this->formDate ? \Carbon\Carbon::parse($this->formDate) : now(),
        ];

        if ($this->editingId) {
            Post::where('id', $this->editingId)->update($data);
            $this->dispatch('nexova-toast', type: 'success', message: 'Post actualizado');
        } else {
            // Ensure slug uniqueness on create
            $base = $data['slug'];
            $i    = 1;
            while (Post::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $base . '-' . $i++;
            }
            Post::create($data);
            $this->dispatch('nexova-toast', type: 'success', message: 'Post publicado');
        }

        $this->dispatch('close-post-modal');
    }

    public function deletePost(int $id): void
    {
        Post::destroy($id);
        $this->dispatch('nexova-toast', type: 'info', message: 'Post eliminado');
    }

    public function toggleStatus(int $id): void
    {
        $post = Post::find($id);
        if (! $post) return;
        $newStatus = $post->status === 'published' ? 'draft' : 'published';
        $post->update([
            'status'       => $newStatus,
            'published_at' => $newStatus === 'published' ? ($post->published_at ?? now()) : $post->published_at,
        ]);
        $msg = $newStatus === 'published' ? 'Post publicado' : 'Post guardado como borrador';
        $this->dispatch('nexova-toast', type: 'success', message: $msg);
    }
}
