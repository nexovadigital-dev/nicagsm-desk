<?php

namespace App\Console\Commands;

use App\Models\KnowledgeBase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ScrapeUrl extends Command
{
    protected $signature   = 'nexova:scrape-url {url : URL a indexar} {--org= : ID de la organización} {--title= : Título personalizado} {--force : Re-indexar aunque ya exista}';
    protected $description = 'Descarga una URL, extrae el texto y lo guarda en la base de conocimiento';

    public function handle(): int
    {
        $url = $this->argument('url');

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            $this->error("URL inválida: {$url}");
            return self::FAILURE;
        }

        $orgId = $this->option('org') ? (int) $this->option('org') : null;

        $existing = KnowledgeBase::where('source', 'url')
            ->where('reference_id', $url)
            ->when($orgId, fn ($q) => $q->where('organization_id', $orgId))
            ->first();

        if ($existing && ! $this->option('force')) {
            $this->warn("Ya existe un artículo para esta URL (ID #{$existing->id}). Usa --force para re-indexar.");
            return self::SUCCESS;
        }

        $this->info("Descargando {$url}…");

        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'NexovaBot/1.0'])
                ->get($url);
        } catch (\Exception $e) {
            $this->error("Error al descargar la URL: {$e->getMessage()}");
            return self::FAILURE;
        }

        if (! $response->successful()) {
            $this->error("La URL respondió con HTTP {$response->status()}");
            return self::FAILURE;
        }

        $html = $response->body();

        $pageTitle = $this->option('title');
        if (! $pageTitle) {
            preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $m);
            $pageTitle = $m[1] ?? parse_url($url, PHP_URL_HOST) ?? $url;
            $pageTitle = html_entity_decode(trim(strip_tags($pageTitle)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $text = $this->extractText($html);

        if (strlen($text) < 50) {
            $this->warn("Texto extraído demasiado corto. Verifica la URL.");
            return self::FAILURE;
        }

        $data = [
            'title'           => $pageTitle,
            'content'         => $text,
            'source'          => 'url',
            'reference_id'    => $url,
            'is_active'       => true,
            'organization_id' => $orgId,
        ];

        if ($existing) {
            $existing->update($data);
            $this->info("Artículo #{$existing->id} actualizado: \"{$pageTitle}\"");
        } else {
            $kb = KnowledgeBase::create($data);
            $this->info("Artículo #{$kb->id} creado: \"{$pageTitle}\"");
        }

        $this->line("  Caracteres indexados: " . number_format(strlen($text)));
        return self::SUCCESS;
    }

    private function extractText(string $html): string
    {
        $html = mb_convert_encoding($html, 'UTF-8', 'auto');
        $dom  = new \DOMDocument();
        @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($dom);

        // Eliminar nodos no-contenido
        foreach (['//script','//style','//noscript','//iframe','//header','//footer','//nav','//aside'] as $sel) {
            foreach ($xpath->query($sel) as $node) {
                $node->parentNode?->removeChild($node);
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $raw  = $body ? $dom->saveHTML($body) : $dom->saveHTML();

        $text = strip_tags($raw);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/(\r?\n\s*){3,}/', "\n\n", $text);
        $text = trim($text);

        // Limitar a 15 000 caracteres para no saturar el prompt
        if (strlen($text) > 15000) {
            $text = mb_substr($text, 0, 15000) . "\n[…contenido truncado…]";
        }

        return $text;
    }
}
