<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body',
        'cover_image', 'category', 'status', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'novedad'        => 'Novedad',
            'evento'         => 'Evento',
            'producto'       => 'Producto',
            'actualizacion'  => 'Actualizacion',
            default          => ucfirst($this->category),
        };
    }

    public function categoryColor(): array
    {
        return match ($this->category) {
            'novedad'        => ['bg' => '#dbeafe', 'color' => '#1e40af'],
            'evento'         => ['bg' => '#fef3c7', 'color' => '#92400e'],
            'producto'       => ['bg' => '#dcfce7', 'color' => '#15803d'],
            'actualizacion'  => ['bg' => '#f3f4f6', 'color' => '#374151'],
            default          => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
        };
    }

    /** Render body markdown to HTML (uses Laravel built-in Str::markdown). */
    public function bodyHtml(): string
    {
        return Str::markdown($this->body ?? '', [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /** Auto-generate slug from title if not set. */
    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}
