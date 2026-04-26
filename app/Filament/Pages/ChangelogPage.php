<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChangelogPage extends Page
{
    protected string $view = 'filament.pages.changelog-page';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Novedades';
    protected static string|\UnitEnum|null $navigationGroup = 'Cuenta';
    protected static ?int    $navigationSort  = 5;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-sparkles';
    }

    public function getTitle(): string|Htmlable { return ''; }

    /**
     * Fetches the changelog from the public GitHub releases JSON.
     * Cached for 1 hour so we don't hammer GitHub API.
     * Returns array of releases or empty array on failure.
     */
    public function getReleases(): array
    {
        return Cache::remember('nx_changelog', 3600, function () {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['Accept' => 'application/vnd.github+json'])
                    ->get('https://api.github.com/repos/nexovadigital-dev/nexova-changelog/releases');

                if (! $response->successful()) return [];

                return collect($response->json())->map(function ($r) {
                    return [
                        'version'     => ltrim($r['tag_name'] ?? '', 'v'),
                        'tag'         => $r['tag_name'] ?? '',
                        'title'       => $r['name'] ?? $r['tag_name'] ?? '',
                        'date'        => isset($r['published_at'])
                            ? \Carbon\Carbon::parse($r['published_at'])->format('d M Y')
                            : '',
                        'body'        => $r['body'] ?? '',
                        'prerelease'  => $r['prerelease'] ?? false,
                        'latest'      => false, // set below
                    ];
                })->values()->toArray();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public function getViewData(): array
    {
        $releases = $this->getReleases();

        // Mark the first stable release as latest
        foreach ($releases as &$r) {
            if (! $r['prerelease']) { $r['latest'] = true; break; }
        }

        $currentVersion = config('app.version', null);

        return compact('releases', 'currentVersion');
    }
}
