<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\ChatWidget;
use App\Models\KnowledgeBase;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Http;

class KnowledgeBasePage extends Page
{
    use ScopedToOrganization;
    protected string $view = 'filament.pages.knowledge-base-page';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Base de Conocimiento';
    protected static string|\UnitEnum|null $navigationGroup = 'Inteligencia';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-book-open';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── Estado de listas ──
    public ?int    $selectedWidgetId = null;   // null = ninguno seleccionado o Telegram
    public string  $selectedChannel  = '';     // '' = ninguno | 'telegram' | 'widget'
    public bool    $channelSelected  = false;
    public string  $search           = '';
    public string  $filterSource     = 'all';
    public bool    $filterActive     = false;

    // Form: create/edit
    public bool    $showForm      = false;
    public ?int    $editingId     = null;
    public string  $formTitle     = '';
    public string  $formContent   = '';
    public string  $formSource    = 'manual';
    public bool    $formActive    = true;
    public ?int    $formWidgetId  = null;
    public string  $formChannel   = '';

    public ?string $msg     = null;
    public string  $msgType = 'success';
    public bool    $isScraping = false;

    /** Selecciona un widget específico. */
    public function selectWidget(int $widgetId): void
    {
        $this->selectedWidgetId = $widgetId;
        $this->selectedChannel  = 'widget';
        $this->channelSelected  = true;
        $this->search           = '';
        $this->filterSource     = 'all';
        $this->msg              = null;
    }

    /** Selecciona el canal Telegram. */
    public function selectTelegram(): void
    {
        $this->selectedWidgetId = null;
        $this->selectedChannel  = 'telegram';
        $this->channelSelected  = true;
        $this->search           = '';
        $this->filterSource     = 'all';
        $this->msg              = null;
    }

    /** Vuelve a la pantalla de selección de canal. */
    public function backToChannels(): void
    {
        $this->channelSelected  = false;
        $this->selectedWidgetId = null;
        $this->selectedChannel  = '';
        $this->showForm         = false;
        $this->msg              = null;
    }

    public function getEntriesProperty()
    {
        if (! $this->channelSelected) return collect();

        $q = trim($this->search);
        $query = $this->scopeToOrg(KnowledgeBase::query())
            ->when($q, fn ($query) =>
                $query->where('title',   'like', "%{$q}%")
                      ->orWhere('content', 'like', "%{$q}%")
            )
            ->when($this->filterSource !== 'all', fn ($query) =>
                $query->where('source', $this->filterSource)
            )
            ->when($this->filterActive, fn ($query) =>
                $query->where('is_active', true)
            )
            ->with('widget:id,name')
            ->orderBy('updated_at', 'desc');

        if ($this->selectedChannel === 'telegram') {
            $query->where('channel', 'telegram')->whereNull('widget_id');
        } else {
            // Widget específico
            $query->whereNull('channel')->where('widget_id', $this->selectedWidgetId);
        }

        return $query->get();
    }

    /** Devuelve los widgets disponibles para la org con conteo de artículos. */
    public function getWidgetsProperty()
    {
        $orgId = $this->orgId();
        return ChatWidget::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->withCount(['knowledgeBases as articles_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get(['id', 'name', 'type']);
    }

    /** Conteo de artículos para el bot de Telegram. */
    public function getTelegramArticlesCountProperty(): int
    {
        return $this->scopeToOrg(KnowledgeBase::query())
            ->where('channel', 'telegram')
            ->whereNull('widget_id')
            ->where('is_active', true)
            ->count();
    }

    public function openCreate(): void
    {
        $this->editingId    = null;
        $this->formTitle    = '';
        $this->formContent  = '';
        $this->formSource   = 'manual';
        $this->formActive   = true;
        $this->formWidgetId = $this->selectedChannel === 'telegram' ? null : $this->selectedWidgetId;
        $this->formChannel  = $this->selectedChannel === 'telegram' ? 'telegram' : '';
        $this->showForm     = true;
        $this->msg          = null;
    }

    public function openEdit(int $id): void
    {
        $item = KnowledgeBase::find($id);
        if (!$item) return;
        $this->editingId    = $id;
        $this->formTitle    = $item->title;
        $this->formContent  = $item->content;
        $this->formSource   = $item->source ?? 'manual';
        $this->formActive   = $item->is_active;
        $this->formWidgetId = $item->widget_id;
        $this->formChannel  = $item->channel ?? '';
        $this->showForm     = true;
        $this->msg          = null;
    }

    public function save(): void
    {
        $title   = trim($this->formTitle);
        $content = trim($this->formContent);

        if ($this->formSource === 'scrape') {
            // For scrape source, formContent must be a valid URL
            if (!$title) {
                $this->msg = 'El título es obligatorio.';
                $this->msgType = 'error';
                return;
            }
            if (!filter_var($content, FILTER_VALIDATE_URL)) {
                $this->msg = 'Ingresa una URL válida para scrapear (ej: https://ejemplo.com).';
                $this->msgType = 'error';
                return;
            }
            $scraped = $this->fetchAndExtract($content);
            if ($scraped === null) {
                $this->msg = 'No se pudo obtener contenido de la URL. Verifica que sea accesible.';
                $this->msgType = 'error';
                return;
            }
            $data = [
                'title'        => $title,
                'content'      => $scraped,
                'source'       => 'scrape',
                'reference_id' => $content,
                'is_active'    => $this->formActive,
                'widget_id'    => $this->formChannel === 'telegram' ? null : ($this->formWidgetId ?: null),
                'channel'      => $this->formChannel ?: null,
            ];
        } else {
            if (!$title || !$content) {
                $this->msg     = 'El título y el contenido son obligatorios.';
                $this->msgType = 'error';
                return;
            }
            $data = [
                'title'     => $title,
                'content'   => $content,
                'source'    => $this->formSource,
                'is_active' => $this->formActive,
                'widget_id' => $this->formChannel === 'telegram' ? null : ($this->formWidgetId ?: null),
                'channel'   => $this->formChannel ?: null,
            ];
        }

        if ($this->editingId) {
            KnowledgeBase::where('id', $this->editingId)->update($data);
            $this->msg = 'Artículo actualizado.';
        } else {
            KnowledgeBase::create($data + ['organization_id' => $this->orgId()]);
            $this->msg = 'Artículo creado.';
        }

        $this->msgType = 'success';
        $this->showForm = false;
        $this->editingId = null;
    }

    /** Re-scrape an existing article using its stored reference_id (URL). */
    public function rescrape(int $id): void
    {
        $item = KnowledgeBase::find($id);
        if (!$item || $item->source !== 'scrape') return;

        $url = $item->reference_id ?: $item->content;
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->msg = 'No se encontró una URL válida en este artículo.';
            $this->msgType = 'error';
            return;
        }

        $scraped = $this->fetchAndExtract($url);
        if ($scraped === null) {
            $this->msg = 'No se pudo obtener contenido de la URL.';
            $this->msgType = 'error';
            return;
        }

        $item->update(['content' => $scraped, 'reference_id' => $url]);
        $this->msg = 'Artículo re-scrapeado correctamente (' . number_format(strlen($scraped)) . ' caracteres).';
        $this->msgType = 'success';
    }

    private function fetchAndExtract(string $url): ?string
    {
        try {
            $response = Http::timeout(30)->withHeaders(['User-Agent' => 'NexovaBot/1.0'])->get($url);
            if (!$response->successful()) return null;
            $html = $response->body();
            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
            $dom  = new \DOMDocument();
            @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
            $xpath = new \DOMXPath($dom);
            foreach (['//script','//style','//noscript','//iframe','//header','//footer','//nav','//aside'] as $sel) {
                foreach ($xpath->query($sel) as $node) { $node->parentNode?->removeChild($node); }
            }
            $body = $dom->getElementsByTagName('body')->item(0);
            $raw  = $body ? $dom->saveHTML($body) : $dom->saveHTML();
            $text = strip_tags($raw);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = preg_replace('/[ \t]+/', ' ', $text);
            $text = preg_replace('/(\r?\n\s*){3,}/', "\n\n", $text);
            $text = trim($text);
            if (strlen($text) < 50) return null;
            if (strlen($text) > 15000) $text = mb_substr($text, 0, 15000) . "\n[…contenido truncado…]";
            return $text;
        } catch (\Throwable) {
            return null;
        }
    }

    public function toggleActive(int $id): void
    {
        $item = KnowledgeBase::find($id);
        if (!$item) return;
        $item->update(['is_active' => !$item->is_active]);
    }

    public function delete(int $id): void
    {
        KnowledgeBase::destroy($id);
        $this->msg     = 'Artículo eliminado.';
        $this->msgType = 'success';
    }

    public function cancelForm(): void
    {
        $this->showForm    = false;
        $this->editingId   = null;
        $this->formWidgetId = null;
        $this->msg         = null;
    }

    public function getStatsProperty(): array
    {
        $q = $this->scopeToOrg(KnowledgeBase::query());
        return [
            'total'      => (clone $q)->count(),
            'active'     => (clone $q)->where('is_active', true)->count(),
            'manual'     => (clone $q)->where('source', 'manual')->count(),
            'scrape'     => (clone $q)->whereIn('source', ['scrape', 'web_scrape'])->count(),
            'external'   => (clone $q)->where('source', 'external')->count(),
        ];
    }

    public function getOrgWebsiteProperty(): ?string
    {
        $orgId = $this->orgId();
        if (! $orgId) return null;
        return \App\Models\Organization::find($orgId)?->website;
    }

    public function getLastWebScrapeProperty(): ?string
    {
        $last = $this->scopeToOrg(KnowledgeBase::query())
            ->where('source', 'web_scrape')
            ->orderByDesc('updated_at')
            ->value('updated_at');
        return $last ? \Illuminate\Support\Carbon::parse($last)->diffForHumans() : null;
    }

    /**
     * Crawlea el sitio web de la organización (máx 20 páginas internas)
     * y guarda cada página como artículo KB con source = 'web_scrape'.
     */
    public function scrapeOrgWebsite(): void
    {
        $orgId   = $this->orgId();
        $website = $this->orgWebsite;

        if (! $website || ! filter_var($website, FILTER_VALIDATE_URL)) {
            $this->msg     = 'La organización no tiene una URL de sitio web configurada. Agrégala en Configuración → Organización.';
            $this->msgType = 'error';
            return;
        }

        $this->isScraping = true;

        $baseHost  = parse_url($website, PHP_URL_HOST);
        $baseScheme= parse_url($website, PHP_URL_SCHEME) ?? 'https';
        $visited   = [];
        $queue     = [$website];
        $results   = []; // ['url' => ..., 'title' => ..., 'content' => ...]
        $maxPages  = 20;

        while (! empty($queue) && count($visited) < $maxPages) {
            $url = array_shift($queue);
            $normalized = strtok($url, '#'); // strip fragments
            if (in_array($normalized, $visited, true)) continue;
            $visited[] = $normalized;

            try {
                $response = Http::timeout(20)
                    ->withHeaders(['User-Agent' => 'NexovaBot/1.0 (site-crawler)'])
                    ->get($normalized);

                if (! $response->successful()) continue;
                $contentType = $response->header('Content-Type') ?? '';
                if (! str_contains($contentType, 'html')) continue;

                $html = $response->body();
                $html = mb_convert_encoding($html, 'UTF-8', 'auto');

                $dom = new \DOMDocument();
                @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
                $xpath = new \DOMXPath($dom);

                // Extraer título de la página
                $titleNode = $xpath->query('//title')->item(0);
                $pageTitle = $titleNode ? trim($titleNode->textContent) : parse_url($normalized, PHP_URL_PATH);

                // Extraer links internos para continuar el crawl
                foreach ($xpath->query('//a[@href]') as $link) {
                    $href = $link->getAttribute('href');
                    if (empty($href) || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) continue;
                    // Resolver URL relativa
                    if (str_starts_with($href, '/')) {
                        $href = $baseScheme . '://' . $baseHost . $href;
                    } elseif (! str_starts_with($href, 'http')) {
                        $href = rtrim($normalized, '/') . '/' . $href;
                    }
                    $hrefHost = parse_url($href, PHP_URL_HOST);
                    if ($hrefHost !== $baseHost) continue; // solo links internos
                    $href = strtok($href, '#');
                    if (! in_array($href, $visited, true) && ! in_array($href, $queue, true)) {
                        $queue[] = $href;
                    }
                }

                // Limpiar HTML y extraer texto
                foreach (['//script','//style','//noscript','//iframe','//header','//footer','//nav','//aside','//form'] as $sel) {
                    foreach ($xpath->query($sel) as $node) { $node->parentNode?->removeChild($node); }
                }
                $body = $dom->getElementsByTagName('body')->item(0);
                $raw  = $body ? $dom->saveHTML($body) : $dom->saveHTML();
                $text = strip_tags($raw);
                $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $text = preg_replace('/[ \t]+/', ' ', $text);
                $text = preg_replace('/(\r?\n\s*){3,}/', "\n\n", $text);
                $text = trim($text);

                if (strlen($text) < 80) continue;
                if (strlen($text) > 8000) $text = mb_substr($text, 0, 8000) . "\n[…]";

                $results[] = ['url' => $normalized, 'title' => $pageTitle ?: $normalized, 'content' => $text];

            } catch (\Throwable) {
                continue;
            }

            usleep(300_000); // 300ms entre requests
        }

        if (empty($results)) {
            $this->isScraping = false;
            $this->msg        = 'No se pudo extraer contenido del sitio. Verifica que la URL sea accesible.';
            $this->msgType    = 'error';
            return;
        }

        // Eliminar artículos web_scrape anteriores de esta org
        $this->scopeToOrg(KnowledgeBase::query())->where('source', 'web_scrape')->delete();

        // Guardar nuevos artículos
        foreach ($results as $page) {
            KnowledgeBase::create([
                'organization_id' => $orgId,
                'title'           => mb_substr($page['title'], 0, 255),
                'content'         => $page['content'],
                'source'          => 'web_scrape',
                'reference_id'    => $page['url'],
                'is_active'       => true,
            ]);
        }

        $this->isScraping = false;
        $this->msg        = "Sitio escaneado correctamente. Se importaron " . count($results) . " páginas como artículos de conocimiento.";
        $this->msgType    = 'success';
    }
}
