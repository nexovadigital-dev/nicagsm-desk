<?php

namespace App\Console\Commands;

use App\Models\KnowledgeBase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Sincroniza datos del sistema externo (pagos, servicios, estado de cuenta)
 * al knowledge base de Nexova para que la IA los incluya en el contexto RAG.
 *
 * Configura en .env:
 *   EXTERNAL_API_URL=https://tu-sistema.com/api
 *   EXTERNAL_API_KEY=tu_clave_secreta
 *
 * El endpoint externo debe responder GET /nexova-context con JSON:
 *   { "articles": [ { "key": "string", "title": "string", "content": "string" }, … ] }
 */
class SyncExternal extends Command
{
    protected $signature   = 'nexova:sync-external {--dry-run : Muestra lo que se sincronizaría sin guardar}';
    protected $description = 'Sincroniza datos del sistema externo al knowledge base';

    public function handle(): int
    {
        $url = config('services.external_api.url');
        $key = config('services.external_api.key');

        if (! $url) {
            $this->error('EXTERNAL_API_URL no está configurado en .env');
            return self::FAILURE;
        }

        $this->info("Conectando a {$url}/nexova-context…");

        try {
            $response = Http::timeout(20)
                ->withHeaders(['X-Api-Key' => $key, 'Accept' => 'application/json'])
                ->get(rtrim($url, '/') . '/nexova-context');
        } catch (\Exception $e) {
            $this->error("Error de conexión: {$e->getMessage()}");
            return self::FAILURE;
        }

        if (! $response->successful()) {
            $this->error("El sistema externo respondió con HTTP {$response->status()}");
            return self::FAILURE;
        }

        $articles = $response->json('articles', []);

        if (empty($articles)) {
            $this->warn('El sistema externo no devolvió artículos.');
            return self::SUCCESS;
        }

        $dry = $this->option('dry-run');
        $updated = $created = 0;

        foreach ($articles as $article) {
            $articleKey = $article['key'] ?? null;
            $title      = $article['title'] ?? 'Sin título';
            $content    = $article['content'] ?? '';

            if (! $articleKey || ! $content) continue;

            if ($dry) {
                $this->line("  [dry-run] {$title} ({$articleKey})");
                continue;
            }

            $exists = KnowledgeBase::where('source', 'external')
                ->where('reference_id', $articleKey)
                ->first();

            if ($exists) {
                $exists->update(['title' => $title, 'content' => $content, 'is_active' => true]);
                $updated++;
            } else {
                KnowledgeBase::create([
                    'title'        => $title,
                    'content'      => $content,
                    'source'       => 'external',
                    'reference_id' => $articleKey,
                    'is_active'    => true,
                ]);
                $created++;
            }
        }

        if (! $dry) {
            $this->info("Sincronizado: {$created} nuevos, {$updated} actualizados.");
        }

        return self::SUCCESS;
    }
}
