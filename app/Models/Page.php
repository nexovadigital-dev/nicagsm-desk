<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'status',
        'meta_title', 'meta_description',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function contentHtml(): string
    {
        return Str::markdown($this->content ?? '', [
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public static function generateSlug(string $title): string
    {
        $slug     = Str::slug($title);
        $original = $slug;
        $i        = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }
}
