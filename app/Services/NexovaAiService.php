<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ApiSetting;
use App\Models\KnowledgeBase;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\WpPluginToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NexovaAiService
{
    /** Sufijo interno que indica que el job debe ofrecer escalación. */
    public const ESCALATE_FLAG = '__ESCALATE__';

    /** Sufijo interno que indica que el visitante debe verificar su identidad WooCommerce. */
    public const WOO_VERIFY_FLAG = '__WOO_VERIFY__';

    // -------------------------------------------------------------------------
    // Constantes de configuración por proveedor
    // -------------------------------------------------------------------------
    private const PROVIDERS = ['groq', 'gemini'];

    private const GROQ_ENDPOINT   = 'https://api.groq.com/openai/v1/chat/completions';
    private const GEMINI_ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s';

    private const GROQ_MODEL   = 'llama-3.3-70b-versatile';
    private const GEMINI_MODEL = 'gemini-1.5-flash';

    private const MAX_TOKENS    = 600;
    private const TEMPERATURE   = 0.7;
    private const HTTP_TIMEOUT  = 45; // segundos

    /**
     * Punto de entrada principal.
     * Itera los proveedores activos ordenados por prioridad.
     * Si uno falla, intenta el siguiente (fallback automático).
     */
    public function generateReply(Ticket $ticket): string
    {
        $org = $ticket->organization_id ? $ticket->organization : null;
        $widget = $ticket->widget_id ? \App\Models\ChatWidget::find($ticket->widget_id) : null;

        // ── Verificar si el bot IA está habilitado en este canal o widget ──────
        if ($ticket->platform === 'telegram' && $org) {
            $telegramConfig = $org->telegram_config ?? [];
            if (empty($telegramConfig['ai_enabled'])) {
                Log::info("[NexovaBot] IA deshabilitada en Telegram — ticket #{$ticket->id}");
                return 'El asistente automático está desactivado en este canal. Un agente te atenderá pronto.' . self::ESCALATE_FLAG;
            }
        } elseif ($widget && ! $widget->bot_enabled) {
            Log::info("[NexovaBot] Bot deshabilitado en widget #{$ticket->widget_id} — ticket #{$ticket->id}");
            return 'El asistente automático está desactivado. Un agente te atenderá pronto.' . self::ESCALATE_FLAG;
        }

        // ── Límite de mensajes por sesión ──────────────────────────────────────
        if ($org) {
            $maxPerSession = $org->max_messages_per_session ?: 30;
            $botMsgCount   = $ticket->messages()
                ->where('sender_type', 'bot')
                ->count();
            if ($botMsgCount >= $maxPerSession) {
                Log::info("[NexovaBot] Límite de mensajes por sesión alcanzado — ticket #{$ticket->id}");
                return 'Has alcanzado el límite de mensajes con el asistente.' . self::ESCALATE_FLAG;
            }
        }

        // ── Verificar cuota mensual de mensajes del bot ───────────────────────
        if ($org && ! $org->hasMonthlyBotQuota()) {
            Log::info("[NexovaBot] Cuota mensual alcanzada — ticket #{$ticket->id}");
            return 'Has alcanzado el límite de mensajes del bot para este mes. Por favor contacta soporte o actualiza tu plan.' . self::ESCALATE_FLAG;
        }

        // ── Interceptar saludos básicos (sin consumir API ni KB) ──────────────
        $greetingReply = $this->tryGreetingReply($ticket, $org);
        if ($greetingReply !== null) {
            $org?->incrementBotMessageCount();
            Log::info("[NexovaBot] Respondido con saludo — ticket #{$ticket->id}");
            return $greetingReply;
        }

        // ── Para Telegram: buscar en KB Global estructurada (widget_id IS NULL) ─────
        // Telegram usa los artículos "Global" de la Base de Conocimiento,
        // igual que los widgets usan su KB específica + la global.
        if ($ticket->platform === 'telegram' && $org) {
            // 1. KB Global estructurada (artículos con widget_id = NULL)
            $globalRagContext = $this->buildRagContext($ticket->organization_id, null);
            if ($globalRagContext) {
                $kbAnswerTelegram = $this->tryKbDirectAnswer($ticket, $globalRagContext, null);
                if ($kbAnswerTelegram !== null) {
                    sleep(random_int(1, 2));
                    $org->incrementBotMessageCount();
                    Log::info("[NexovaBot] Respondido desde KB Global (Telegram) — ticket #{$ticket->id}");
                    return $kbAnswerTelegram;
                }
            }

            // 2. Fallback: texto libre en telegram_config (retrocompatibilidad)
            $localAnswer = $this->tryTelegramLocalKbAnswer($ticket, $org);
            if ($localAnswer !== null) {
                sleep(random_int(1, 2));
                $org->incrementBotMessageCount();
                Log::info("[NexovaBot] Respondido desde memoria texto Telegram (legacy) — ticket #{$ticket->id}");
                return $localAnswer;
            }
        }

        // ── Intentar responder desde FAQ del widget (para canales web) ───────────
        $hasStoreCtx = ! empty($ticket->store_context);
        if (! $hasStoreCtx) {
            $faqAnswer = $this->tryFaqAnswer($ticket);
            if ($faqAnswer !== null) {
                sleep(random_int(1, 2));
                $org?->incrementBotMessageCount();
                Log::info("[NexovaBot] Respondido desde FAQ del widget — ticket #{$ticket->id}");
                return $faqAnswer;
            }
        }

        // ── Intentar responder desde la KB (solo si NO hay store_context) ───────
        $widgetId  = $ticket->widget_id ?: null;
        $ragContext = $this->buildRagContext($ticket->organization_id, $widgetId);
        $kbAnswer  = null;
        if ($ragContext && ! $hasStoreCtx) {
            $kbAnswer = $this->tryKbDirectAnswer($ticket, $ragContext, $widgetId);
        }
        if ($kbAnswer !== null) {
            sleep(random_int(1, 2));
            $org?->incrementBotMessageCount();
            Log::info("[NexovaBot] Respondido desde KB local — ticket #{$ticket->id}");
            return $kbAnswer;
        }


        // --- IA deshabilitada en el widget (ai_enabled = false) ---
        if ($widget && $widget->ai_enabled === false) {
            Log::info("[NexovaBot] IA deshabilitada en widget - escalando ticket #{$ticket->id}");
            return 'No tengo informacion sobre eso. Te pongo en contacto con un agente.' . self::ESCALATE_FLAG;
        }

        // ── Plan Free: IA bloqueada, solo KB ───────────────────────────────────
        // Excepción: si hay store_context (plugin WooCommerce), permitir IA para
        // responder sobre el catálogo de la tienda aunque el plan sea Free.
        if ($org && $org->isAiBlocked() && ! $hasStoreCtx) {
            Log::info("[NexovaBot] IA bloqueada por plan Free — ticket #{$ticket->id}");
            return 'No encontré información en nuestra base de conocimiento para tu consulta. ¿Te gustaría hablar con un agente?' . self::ESCALATE_FLAG;
        }

        // ── Construir providers: org keys tienen prioridad ──────────────────────
        $providers = $this->buildProviders($org);

        if ($providers->isEmpty()) {
            Log::warning('[NexovaBot] No hay proveedores de IA activos.');
            return 'El asistente IA no está configurado en este momento.' . self::ESCALATE_FLAG;
        }

        $messages = $this->buildMessageHistory($ticket, $ragContext);

        // Delay "pensando" cuando se llama a la IA real (3-5 s)
        sleep(random_int(3, 5));

        foreach ($providers as $provider) {
            ['type' => $type, 'key' => $key] = $provider;
            try {
                $reply = match ($type) {
                    'groq'   => $this->callGroq($key, $messages),
                    'gemini' => $this->callGemini($key, $messages),
                    default  => throw new \RuntimeException("Proveedor no soportado: {$type}"),
                };

                // Strip markdown formatting — bot responses must be plain text
                $reply = $this->stripMarkdown($reply);

                // WOO_VERIFY_FLAG already embedded by the AI — don't double-add ESCALATE
                if (str_contains($reply, self::WOO_VERIFY_FLAG)) {
                    $org?->incrementBotMessageCount();
                    Log::info("[NexovaBot] Respuesta con WOO_VERIFY via {$type} — ticket #{$ticket->id}");
                    return $reply;
                }

                // Only escalate if AI itself emits __ESCALATE__ flag
                $aiSuggestsEscalation = str_contains($reply, self::ESCALATE_FLAG);
                if ($aiSuggestsEscalation) {
                    $reply = trim(str_replace(self::ESCALATE_FLAG, '', $reply));
                }

                $org?->incrementBotMessageCount();
                Log::info("[NexovaBot] Respuesta OK via {$type} — ticket #{$ticket->id}");
                return $aiSuggestsEscalation ? $reply . self::ESCALATE_FLAG : $reply;

            } catch (\Throwable $e) {
                Log::warning("[NexovaBot] {$type} falló (ticket #{$ticket->id}): {$e->getMessage()}");
            }
        }

        Log::error("[NexovaBot] Todos los proveedores fallaron para ticket #{$ticket->id}.");
        return 'No pude obtener respuesta en este momento.' . self::ESCALATE_FLAG;
    }

    /**
     * Build ordered list of providers.
     * If org has own keys enabled, those go first; platform keys are fallback.
     *
     * @return \Illuminate\Support\Collection<int, array{type: string, key: string}>
     */
    private function buildProviders(?\App\Models\Organization $org): \Illuminate\Support\Collection
    {
        $list = collect();

        // Org-own keys (highest priority)
        if ($org?->ai_use_own_keys) {
            if ($key = $org->effectiveGroqKey()) {
                $list->push(['type' => 'groq',   'key' => $key]);
            }
            if ($key = $org->effectiveGeminiKey()) {
                $list->push(['type' => 'gemini', 'key' => $key]);
            }
        }

        // Platform keys (fallback)
        $platform = ApiSetting::query()
            ->where('is_active', true)
            ->whereIn('provider', self::PROVIDERS)
            ->orderBy('priority')
            ->get();

        foreach ($platform as $p) {
            // Avoid duplicating if org already added the same provider
            if ($org?->ai_use_own_keys && $list->contains('type', $p->provider)) continue;
            $list->push(['type' => $p->provider, 'key' => $p->api_key]);
        }

        return $list;
    }

    /**
     * Try to answer directly from KB content without calling the AI API.
     * Returns a string if a confident direct answer is found, null otherwise.
     * This saves API tokens when the KB has an exact match.
     */
    private function tryKbDirectAnswer(Ticket $ticket, string $ragContext, ?int $widgetId = null): ?string
    {
        // Get the last user message
        $lastMsg = $ticket->messages()
            ->where('sender_type', 'user')
            ->orderByDesc('created_at')
            ->value('content');

        if (! $lastMsg || strlen($lastMsg) < 3) return null;

        // Artículos del widget específico + artículos globales (widget_id IS NULL)
        $articles = KnowledgeBase::query()
            ->where('is_active', true)
            ->when($ticket->organization_id, fn ($q) => $q->where('organization_id', $ticket->organization_id))
            ->where(function ($q) use ($widgetId) {
                $q->whereNull('widget_id');
                if ($widgetId) {
                    $q->orWhere('widget_id', $widgetId);
                }
            })
            ->get(['title', 'content', 'source']);

        if ($articles->isEmpty()) return null;

        $msgLower = mb_strtolower($lastMsg);
        // Palabras significativas del mensaje (>3 chars, sin stopwords básicas)
        $stopwords = ['como', 'cual', 'cuál', 'que', 'qué', 'para', 'por', 'con', 'una', 'uno', 'los', 'las', 'del', 'hay', 'tiene', 'puedo', 'puede', 'quiero', 'necesito', 'favor', 'hola', 'gracias'];
        $msgWords  = array_filter(
            explode(' ', preg_replace('/[^a-záéíóúüñ\s]/u', '', $msgLower)),
            fn ($w) => mb_strlen($w) > 3 && ! in_array($w, $stopwords)
        );

        $bestScore   = 0;
        $bestArticle = null;

        foreach ($articles as $article) {
            $titleLower   = mb_strtolower($article->title);
            $contentLower = mb_strtolower($article->content);

            // Score 1: palabras del título encontradas en el mensaje
            $titleWords = array_filter(explode(' ', $titleLower), fn ($w) => mb_strlen($w) > 3);
            $titleScore = 0;
            if (! empty($titleWords)) {
                $hits = 0;
                foreach ($titleWords as $w) {
                    if (str_contains($msgLower, $w)) $hits++;
                }
                $titleScore = $hits / count($titleWords);
            }

            // Score 2: palabras del mensaje encontradas en el contenido
            $contentScore = 0;
            if (! empty($msgWords)) {
                $hits = 0;
                foreach ($msgWords as $w) {
                    if (str_contains($contentLower, $w)) $hits++;
                }
                $contentScore = $hits / count($msgWords);
            }

            // Peso: título vale más (0.7) que contenido (0.3)
            $score = ($titleScore * 0.7) + ($contentScore * 0.3);

            if ($score > $bestScore) {
                $bestScore   = $score;
                $bestArticle = $article;
            }
        }

        // Umbral: 0.45 (más sensible que el 60% anterior, pero combinado)
        if ($bestArticle && $bestScore >= 0.45) {
            return $this->stripMarkdown($bestArticle->content);
        }

        return null;
    }

    // =========================================================================
    // Store Context — convierte el JSON de WooCommerce a texto para el prompt
    // =========================================================================

    /**
     * Convierte el array store_context (enviado por el plugin WP) a un bloque
     * de texto que se inyecta en el system prompt de la IA.
     */
    private function buildStoreContextBlock(array $ctx): string
    {
        $lines = ['=== INFORMACIÓN DE LA TIENDA (usa esto para responder sobre productos, precios y servicios) ==='];

        if (! empty($ctx['store_name'])) {
            $lines[] = "Tienda: {$ctx['store_name']}";
        }
        if (! empty($ctx['store_description'])) {
            $lines[] = "Descripción: {$ctx['store_description']}";
        }
        if (! empty($ctx['store_url'])) {
            $lines[] = "URL: {$ctx['store_url']}";
        }
        if (! empty($ctx['currency'])) {
            $lines[] = "Moneda: {$ctx['currency']}";
        }

        if (! empty($ctx['payment_methods']) && is_array($ctx['payment_methods'])) {
            $methods = implode(', ', array_column($ctx['payment_methods'], 'title'));
            $lines[] = "Métodos de pago: {$methods}";
        }

        if (! empty($ctx['shipping_methods']) && is_array($ctx['shipping_methods'])) {
            $lines[] = "Métodos de envío: " . implode(', ', $ctx['shipping_methods']);
        }

        if (! empty($ctx['categories']) && is_array($ctx['categories'])) {
            $cats  = implode(', ', array_column($ctx['categories'], 'name'));
            $lines[] = "Categorías de productos: {$cats}";
        }

        if (! empty($ctx['current_product']) && is_array($ctx['current_product'])) {
            $p       = $ctx['current_product'];
            $lines[] = '';
            $lines[] = '--- PRODUCTO EN ESTA PÁGINA ---';
            $lines[] = "Nombre: {$p['name']}";
            if (! empty($p['price']))        $lines[] = "Precio: {$p['price']}";
            if (! empty($p['on_sale']))      $lines[] = "En oferta: Sí (precio regular: {$p['regular_price']})";
            if (! empty($p['sku']))          $lines[] = "SKU: {$p['sku']}";
            if (! empty($p['stock']))        $lines[] = "Stock: {$p['stock']}";
            if (! empty($p['categories']))   $lines[] = "Categorías: " . implode(', ', (array)$p['categories']);
            if (! empty($p['attributes']) && is_array($p['attributes'])) {
                foreach ($p['attributes'] as $attrName => $attrVal) {
                    $lines[] = "{$attrName}: {$attrVal}";
                }
            }
            if (! empty($p['description'])) $lines[] = "Descripción: {$p['description']}";
            if (! empty($p['url']))          $lines[] = "URL: {$p['url']}";
        }

        if (! empty($ctx['products']) && is_array($ctx['products'])) {
            $lines[] = '';
            $lines[] = '--- CATÁLOGO DE PRODUCTOS ---';
            foreach ($ctx['products'] as $p) {
                $entry = "• {$p['name']}";
                if (! empty($p['price']))       $entry .= " — {$p['price']} {$ctx['currency']}";
                if (! empty($p['sku']))         $entry .= " (SKU: {$p['sku']})";
                if (! empty($p['stock']))       $entry .= " | Stock: {$p['stock']}";
                if (! empty($p['description'])) $entry .= "\n  {$p['description']}";
                if (! empty($p['url']))         $entry .= "\n  Enlace: {$p['url']}";
                $lines[] = $entry;
            }
        }

        $lines[] = '';
        $lines[] = 'Cuando el cliente pregunte por productos, precios o disponibilidad, usa la información de arriba.';
        $lines[] = 'Si el cliente pregunta por un producto que no aparece en el catálogo, dile que puede verlo en la tienda o hablar con un agente.';
        $lines[] = 'MUY IMPORTANTE SOBRE PRECIOS: Si el precio es 0.00, NO digas que es gratis. Significa que es un servicio variable (ej: depende de la duración o modelo). Dile al cliente que el precio depende de la variación elegida y provéele obligatoriamente el enlace con formato Markdown [Ver Opciones](url).';

        return implode("\n", $lines);
    }

    // =========================================================================
    // Saludos — respuestas sin IA para mensajes de bienvenida
    // =========================================================================

    /**
     * Si el mensaje del usuario es un saludo simple, responde con una
     * bienvenida amistosa sin consumir API ni KB.
     * Retorna null si el mensaje no es un saludo.
     */
    private function tryGreetingReply(Ticket $ticket, ?\App\Models\Organization $org): ?string
    {
        $lastMsg = $ticket->messages()
            ->where('sender_type', 'user')
            ->orderByDesc('created_at')
            ->value('content');

        if (! $lastMsg) return null;

        // Solo aplica si es el PRIMER mensaje del usuario en la conversación
        $userMsgCount = $ticket->messages()->where('sender_type', 'user')->count();
        if ($userMsgCount > 1) return null;

        $greetings = [
            'hola', 'hi', 'hello', 'hey', 'buenas', 'buenos dias', 'buenos días',
            'buenas tardes', 'buenas noches', 'good morning', 'good afternoon',
            'saludos', 'qué tal', 'que tal', 'cómo estás', 'como estas',
            'ola', 'alo', 'aló',
        ];

        $normalized = mb_strtolower(trim($lastMsg));
        $normalized = preg_replace('/[^a-záéíóúüñ\s]/u', '', $normalized);
        $normalized = trim($normalized);

        $isGreeting = false;
        foreach ($greetings as $g) {
            if ($normalized === $g || str_starts_with($normalized, $g . ' ') || str_ends_with($normalized, ' ' . $g)) {
                $isGreeting = true;
                break;
            }
        }

        if (! $isGreeting) return null;

        $botName = $org?->name ? "el asistente de {$org->name}" : 'tu asistente virtual';
        return "Hola, soy {$botName}. ¿En qué puedo ayudarte?";
    }

    // =========================================================================
    // FAQ — Respuesta directa desde preguntas frecuentes del widget
    // =========================================================================

    /**
     * Busca en los faq_items del widget asociado al ticket.
     * Primero hace match exacto normalizado, luego match por palabras clave.
     * Retorna la respuesta si hay coincidencia suficiente, null si no.
     */
    private function tryFaqAnswer(Ticket $ticket): ?string
    {
        if (! $ticket->widget_id) return null;

        $widget = \App\Models\ChatWidget::find($ticket->widget_id);
        $faqs   = $widget?->faq_items ?? [];
        if (empty($faqs)) return null;

        $lastMsg = $ticket->messages()
            ->where('sender_type', 'user')
            ->orderByDesc('created_at')
            ->value('content');

        if (! $lastMsg || strlen($lastMsg) < 2) return null;

        $normalize = fn (string $s): string => mb_strtolower(
            trim(preg_replace('/[^a-záéíóúüñ\s]/u', '', $s))
        );

        $msgNorm = $normalize($lastMsg);

        // 1. Match exacto (o muy cercano) de la pregunta completa
        foreach ($faqs as $faq) {
            if (empty($faq['question']) || empty($faq['answer'])) continue;
            if ($normalize($faq['question']) === $msgNorm) {
                return $this->stripMarkdown($faq['answer']);
            }
        }

        // 2. Match por palabras clave
        $stopwords = ['como', 'cual', 'cuál', 'que', 'qué', 'para', 'por', 'con', 'una',
                      'uno', 'los', 'las', 'del', 'hay', 'tiene', 'puedo', 'puede',
                      'quiero', 'necesito', 'favor', 'hola', 'gracias', 'son', 'estan',
                      'está', 'esta', 'donde', 'cuales', 'cuáles'];

        $msgWords = array_filter(
            explode(' ', preg_replace('/[^a-záéíóúüñ\s]/u', '', $msgNorm)),
            fn ($w) => mb_strlen($w) > 2 && ! in_array($w, $stopwords)
        );

        if (empty($msgWords)) return null;

        $bestScore = 0;
        $bestFaq   = null;

        foreach ($faqs as $faq) {
            if (empty($faq['question']) || empty($faq['answer'])) continue;

            $qNorm  = $normalize($faq['question']);
            $qWords = array_filter(
                explode(' ', preg_replace('/[^a-záéíóúüñ\s]/u', '', $qNorm)),
                fn ($w) => mb_strlen($w) > 2 && ! in_array($w, $stopwords)
            );

            if (empty($qWords)) continue;

            // % de palabras del mensaje que aparecen en la pregunta FAQ
            $hits = 0;
            foreach ($msgWords as $w) {
                if (str_contains($qNorm, $w)) $hits++;
            }
            $scoreA = $hits / count($msgWords);

            // % de palabras de la pregunta FAQ que aparecen en el mensaje
            $hits2 = 0;
            foreach ($qWords as $w) {
                if (str_contains($msgNorm, $w)) $hits2++;
            }
            $scoreB = $hits2 / count($qWords);

            // Media ponderada: el mensaje cubriendo la FAQ importa más
            $score = ($scoreA * 0.5) + ($scoreB * 0.5);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFaq   = $faq;
            }
        }

        // Umbral 0.4 — suficiente para preguntas cortas tipo "métodos de pago"
        if ($bestFaq && $bestScore >= 0.4) {
            return $this->stripMarkdown($bestFaq['answer']);
        }

        return null;
    }

    // =========================================================================
    // Memoria Local Telegram — búsqueda por palabras clave sin consumir IA
    // =========================================================================

    /**
     * Busca en el texto libre de knowledge_base del telegram_config.
     * Si encuentra una sección altamente relevante, la retorna directamente.
     * Esto evita consumir la API de IA para preguntas que ya tiene respuesta.
     */
    private function tryTelegramLocalKbAnswer(Ticket $ticket, \App\Models\Organization $org): ?string
    {
        $kb = trim($org->telegram_config['knowledge_base'] ?? '');
        if ($kb === '') return null;

        $lastMsg = $ticket->messages()
            ->where('sender_type', 'user')
            ->orderByDesc('created_at')
            ->value('content');

        if (! $lastMsg || mb_strlen($lastMsg) < 3) return null;

        $stopwords = ['como', 'cual', 'cuál', 'que', 'qué', 'para', 'por', 'con', 'una',
                      'uno', 'los', 'las', 'del', 'hay', 'tiene', 'puedo', 'puede',
                      'quiero', 'necesito', 'favor', 'hola', 'gracias', 'son', 'estan',
                      'está', 'esta', 'donde', 'cuales', 'cuáles', 'dime', 'dame'];

        $msgLower = mb_strtolower($lastMsg);
        $msgWords = array_filter(
            explode(' ', preg_replace('/[^a-záéíóúüñ\s]/u', '', $msgLower)),
            fn ($w) => mb_strlen($w) > 3 && ! in_array($w, $stopwords)
        );

        if (empty($msgWords)) return null;

        // Dividir la KB en líneas/párrafos y buscar coincidencias
        $lines  = array_filter(array_map('trim', explode("\n", $kb)), fn ($l) => $l !== '');
        $hits   = [];
        foreach ($lines as $line) {
            $lineLower = mb_strtolower($line);
            $score = 0;
            foreach ($msgWords as $w) {
                if (str_contains($lineLower, $w)) $score++;
            }
            if ($score > 0) {
                $hits[] = ['line' => $line, 'score' => $score];
            }
        }

        if (empty($hits)) return null;

        // Ordenar por score descendente
        usort($hits, fn ($a, $b) => $b['score'] <=> $a['score']);

        // Umbral: al menos 2 palabras clave coincidentes O score >= 3
        $best = $hits[0];
        $totalMatched = count($msgWords);
        if ($best['score'] < 2 && ($totalMatched < 2 || $best['score'] / $totalMatched < 0.5)) {
            return null;
        }

        // Retornar las mejores líneas relacionadas (máx 3)
        $topLines = array_slice($hits, 0, 3);
        $answer   = implode("\n", array_column($topLines, 'line'));

        return $this->stripMarkdown($answer);
    }

    // =========================================================================
    // WP Store Catalog — fetched via plugin /catalog endpoint (no WC credentials)
    // =========================================================================

    /**
     * Obtiene el catálogo de la tienda conectada llamando al endpoint REST del
     * plugin de WordPress (autenticado con el token almacenado).
     * Cachea el resultado 60 minutos para no sobrecargar el WP en cada mensaje.
     */
    private function fetchStoreCatalogContext(int $orgId): string
    {
        return Cache::remember("nexova_wp_catalog_{$orgId}", 3600, function () use ($orgId) {
            $wpToken = WpPluginToken::where('organization_id', $orgId)->first();
            if (! $wpToken || empty($wpToken->site_url) || empty($wpToken->token)) {
                return '';
            }

            $url = rtrim($wpToken->site_url, '/') . '/wp-json/nexova-desk/v1/catalog';

            try {
                $response = Http::timeout(15)
                    ->withToken($wpToken->token)
                    ->get($url);

                if (! $response->successful()) {
                    Log::warning("[NexovaBot] WP Catalog fetch failed ({$response->status()}) for org #{$orgId}");
                    return '';
                }

                $data = $response->json();
                return $this->buildWpCatalogBlock($data);
            } catch (\Exception $e) {
                Log::warning("[NexovaBot] WP Catalog fetch error for org #{$orgId}: {$e->getMessage()}");
                return '';
            }
        });
    }

    /**
     * Convierte la respuesta del endpoint /catalog del plugin WP en un bloque
     * de texto para inyectar en el system prompt de la IA.
     */
    private function buildWpCatalogBlock(array $data): string
    {
        if (empty($data)) return '';

        $storeName = $data['store_name'] ?? 'la tienda';
        $currency  = $data['currency'] ?? '';
        $lines     = ["=== CATÁLOGO DE {$storeName} (usa esto para responder sobre productos, páginas y servicios) ==="];

        // Métodos de pago
        if (! empty($data['payment_methods']) && is_array($data['payment_methods'])) {
            $methods = implode(', ', array_column($data['payment_methods'], 'title'));
            $lines[] = "Métodos de pago aceptados: {$methods}";
        }

        // Productos
        if (! empty($data['products'])) {
            $lines[] = '';
            $lines[] = '--- PRODUCTOS Y SERVICIOS ---';
            foreach ($data['products'] as $p) {
                $entry = "• {$p['name']}";
                if (! empty($p['price']))       $entry .= " — {$p['price']}" . ($currency ? " {$currency}" : '');
                if (! empty($p['stock']))       $entry .= " | {$p['stock']}";
                if (! empty($p['categories'])) $entry .= " | Cat: {$p['categories']}";
                if (! empty($p['description'])) $entry .= "\n  {$p['description']}";
                if (! empty($p['url']))         $entry .= "\n  URL: [Ver / Ordenar]({$p['url']})";
                $lines[] = $entry;
            }
        }

        // Páginas
        if (! empty($data['pages'])) {
            $lines[] = '';
            $lines[] = '--- PÁGINAS DEL SITIO ---';
            foreach ($data['pages'] as $pg) {
                $entry = "• {$pg['title']}";
                if (! empty($pg['excerpt']))  $entry .= " — {$pg['excerpt']}";
                if (! empty($pg['url']))      $entry .= "\n  URL: [{$pg['title']}]({$pg['url']})";
                $lines[] = $entry;
            }
        }

        // Posts
        if (! empty($data['posts'])) {
            $lines[] = '';
            $lines[] = '--- ARTÍCULOS / BLOG ---';
            foreach ($data['posts'] as $post) {
                $entry = "• {$post['title']}";
                if (! empty($post['excerpt'])) $entry .= " — {$post['excerpt']}";
                if (! empty($post['url']))     $entry .= "\n  URL: [{$post['title']}]({$post['url']})";
                $lines[] = $entry;
            }
        }

        $lines[] = '';
        $lines[] = 'Cuando el cliente pregunte por productos, precios, páginas o servicios, usa SOLO la información de arriba.';
        $lines[] = 'Si el precio muestra variaciones (ej: "3 meses: $X | 6 meses: $Y"), indícale las opciones al cliente.';
        $lines[] = 'Si hay una URL relevante para el cliente, SIEMPRE inclúyela en formato Markdown [texto](url) para generar un botón.';

        return implode("\n", $lines);
    }

    // =========================================================================
    // RAG — Base de Conocimientos
    // =========================================================================


    /**
     * Trae todos los artículos activos y los concatena como contexto de sistema.
     * RAG básico: inyección total. Para colecciones grandes se puede añadir
     * búsqueda por embeddings más adelante.
     */
    private function buildRagContext(?int $orgId = null, ?int $widgetId = null): string
    {
        $articles = KnowledgeBase::query()
            ->where('is_active', true)
            ->when($orgId, fn ($q) => $q->where('organization_id', $orgId))
            ->where(function ($q) use ($widgetId) {
                $q->whereNull('widget_id');   // artículos globales siempre
                if ($widgetId) {
                    $q->orWhere('widget_id', $widgetId); // + los del widget específico
                }
            })
            ->get(['title', 'content', 'source', 'reference_id']);

        if ($articles->isEmpty()) {
            return '';
        }

        $body = $articles->map(function ($a) {
            $header = "### {$a->title}";
            if ($a->source === 'web_scrape' && $a->reference_id) {
                $header .= " ({$a->reference_id})";
            }
            return $header . "\n" . $a->content;
        })->implode("\n\n");

        return "=== BASE DE CONOCIMIENTOS DEL SITIO (usa SOLO esta información para responder) ===\n\n{$body}\n\n";
    }

    // =========================================================================
    // Historial de conversación
    // =========================================================================

    /**
     * Construye el array de mensajes en formato universal [role, content].
     * El system prompt incluye el contexto RAG si existe.
     */
    private function buildMessageHistory(Ticket $ticket, string $ragContext): array
    {
        $org     = $ticket->organization_id ? $ticket->organization : null;
        $orgName = $org?->name ?? 'esta empresa';
        $orgWeb  = $org?->website;

        // Widget y nombre del bot — el nombre lo define el admin en el widget
        $widget  = $ticket->widget_id ? \App\Models\ChatWidget::find($ticket->widget_id) : null;
        $botName = ($org?->name ? "{$org->name} IA" : 'Asistente IA');
        $customPrompt = '';

        if ($ticket->platform === 'telegram') {
            $telegramConfig = $org?->telegram_config ?? [];
            $botName        = $org?->name ? "{$org->name} Bot" : 'Asistente';
            // Prompt INTERNO — no es configurable por el admin, es el comportamiento del sistema
            $customPrompt = "Eres el asistente virtual oficial de {$botName}. "
                . "Tu única función es informar sobre {$botName}: sus productos, servicios, precios, políticas y funcionamiento. "
                . "REGLAS ABSOLUTAS: "
                . "(1) Responde SOLO con información que esté en tu base de conocimiento o en el contexto proporcionado. "
                . "(2) NO inventes datos, precios, horarios ni información que no tengas. "
                . "(3) Si el cliente pregunta algo fuera del ecosistema de {$botName}, indícale amablemente que no tienes esa información y ofécele hablar con un agente humano. "
                . "(4) Responde en el idioma del cliente (español o inglés). "
                . "(5) Sé amable, directo y conciso. No uses formato Markdown, EXCEPTO para enlaces. "
                . "(6) MUY IMPORTANTE: Cuando proporciones un enlace a un producto o servicio, SIEMPRE usa el formato Markdown exacto [Nombre del Producto](https://url). No pongas la URL suelta en el texto. Esto es para crear botones interactivos.";
        } elseif ($widget) {
            $botName = $widget->bot_name ?: $botName;
            $customPrompt = trim($widget->bot_system_prompt ?? '');
        }

        // Instrucción de formato que aplica SIEMPRE (custom prompt o no)
        $formatRule = " IMPORTANTE: Responde SIEMPRE en texto plano sin formato Markdown. No uses **, *, #, __, backticks ni ningún símbolo de formato. No uses emojis salvo que el usuario los use primero. Las listas deben ir con guiones simples o numeradas con punto.";

        if ($customPrompt !== '') {
            // El admin configuró un prompt personalizado — usarlo como base
            $systemPrompt = $customPrompt . $formatRule;
            if ($orgWeb && ! str_contains($customPrompt, $orgWeb)) {
                $systemPrompt .= " Sitio web: {$orgWeb}.";
            }
        } else {
            // Prompt por defecto: natural, sin instrucciones visibles al usuario
            $systemPrompt  = "Eres {$botName}, el asistente virtual de {$orgName}.";
            $systemPrompt .= " Responde en el idioma del cliente (español o inglés). Sé amable, directo y conciso.";
            $systemPrompt .= " Tu conocimiento se limita a {$orgName}: sus productos, servicios, precios, políticas e información de la organización.";
            $systemPrompt .= " Si te consultan algo ajeno a {$orgName}, indica amablemente que solo puedes ayudar con temas de la organización y sugiere hablar con un agente.";
            $systemPrompt .= " Nunca inventes datos. Si no tienes la información, dilo y ofrece conectar con un agente.";
            if ($orgWeb) {
                $systemPrompt .= " Sitio web oficial: {$orgWeb}.";
            }
            $systemPrompt .= $formatRule;
        }

        // Contexto de tienda WooCommerce (inyectado por el plugin, prioridad alta)
        $storeCtx = $ticket->store_context;
        if (! empty($storeCtx)) {
            $systemPrompt .= "\n\n" . $this->buildStoreContextBlock($storeCtx);

            // Indicar si el visitante está identificado como cliente WC o es un guest
            $ticket->loadMissing('contact');
            $wooVerified = $ticket->contact && $ticket->contact->woo_customer_id;
            if ($wooVerified) {
                $systemPrompt .= "\n\n**IDENTIDAD DEL CLIENTE:** El cliente está identificado como cliente registrado de la tienda (WooCommerce). Puedes referirte a él por su nombre si lo tienes disponible.";
            } else {
                $systemPrompt .= "\n\n**IDENTIDAD DEL CLIENTE:** El visitante NO ha iniciado sesión en la tienda. Si pregunta por sus pedidos, historial de compras, estado de envío, cuenta o cualquier información personal de su perfil de cliente, responde explicando que necesitas verificar su identidad y añade el marcador exacto __WOO_VERIFY__ al FINAL de tu respuesta (sin espacios antes ni después). No inventes información de pedidos. Para preguntas generales sobre productos, precios o la tienda, responde con normalidad sin usar el marcador.";
            }
        }

        // Base de conocimiento local del bot Telegram
        if ($ticket->platform === 'telegram') {
            $telegramKb = trim($org?->telegram_config['knowledge_base'] ?? '');
            if ($telegramKb !== '') {
                $systemPrompt .= "\n\n=== BASE DE CONOCIMIENTO DE LA ORGANIZACIÓN (usa SOLO esta información para responder sobre la empresa) ===\n\n{$telegramKb}\n\n(Si el cliente pregunta algo que no está en esta base de conocimiento and no hay contexto de tienda, indica amablemente que no tienes esa información y ofrece conectar con un agente.)";
            }

            // ── Catálogo WooCommerce via plugin (sin credenciales extra) ──────────
            $useStoreCtx = $org?->telegram_config['use_store_context'] ?? false;
            if ($useStoreCtx && $org) {
                $catalogCtx = $this->fetchStoreCatalogContext($org->id);
                if ($catalogCtx !== '') {
                    $systemPrompt .= "\n\n" . $catalogCtx;
                }
            }
        }

        // Conocimiento (KB manual + web scrape) — se agrega si existe
        if ($ragContext !== '') {
            $systemPrompt .= "\n\n{$ragContext}";
        }

        $history = Message::query()
            ->where('ticket_id', $ticket->id)
            ->orderBy('created_at')
            ->get(['sender_type', 'content']);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $msg) {
            // 'agent' también se mapea como 'assistant' para que la IA mantenga coherencia
            $messages[] = [
                'role'    => $msg->sender_type === 'user' ? 'user' : 'assistant',
                'content' => $msg->content,
            ];
        }

        return $messages;
    }

    // =========================================================================
    // Utilidades de texto
    // =========================================================================

    /**
     * Convierte respuesta Markdown a texto plano limpio.
     * Los LLMs suelen usar ** ** para negritas, # para títulos, etc.
     * El widget muestra texto plano — ningún Markdown debe llegar al usuario.
     */
    private function stripMarkdown(string $text): string
    {
        // Negritas e itálicas: **texto**, *texto*, __texto__, _texto_
        $text = preg_replace('/\*{1,3}(.+?)\*{1,3}/u', '$1', $text);
        $text = preg_replace('/_{1,3}(.+?)_{1,3}/u', '$1', $text);

        // Encabezados: ## Texto → Texto
        $text = preg_replace('/^#{1,6}\s+/mu', '', $text);

        // Código inline: `texto` → texto
        $text = preg_replace('/`([^`]+)`/', '$1', $text);

        // Bloques de código: ```...``` → solo el contenido
        $text = preg_replace('/```[\w]*\n?(.*?)```/su', '$1', $text);

        // Links: [texto](url) → texto
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);

        // Viñetas markdown: - item o * item al inicio de línea
        $text = preg_replace('/^[\*\-]\s+/mu', '• ', $text);

        // Líneas horizontales: ---
        $text = preg_replace('/^---+$/mu', '', $text);

        // Múltiples saltos de línea → máximo dos
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    // =========================================================================
    // Adaptadores de API — cada método lanza \Throwable en caso de error
    // =========================================================================

    /**
     * Groq — API compatible con OpenAI.
     * Docs: https://console.groq.com/docs/openai
     */
    private function callGroq(string $apiKey, array $messages): string
    {
        $response = Http::withToken($apiKey)
            ->timeout(self::HTTP_TIMEOUT)
            ->post(self::GROQ_ENDPOINT, [
                'model'       => self::GROQ_MODEL,
                'messages'    => $messages,
                'max_tokens'  => self::MAX_TOKENS,
                'temperature' => self::TEMPERATURE,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                "Groq HTTP {$response->status()}: " . substr($response->body(), 0, 200)
            );
        }

        $text = $response->json('choices.0.message.content');

        if (blank($text)) {
            throw new \RuntimeException('Groq devolvió una respuesta vacía.');
        }

        return trim($text);
    }

    /**
     * Google Gemini — API nativa (formato diferente a OpenAI).
     * Docs: https://ai.google.dev/api/generate-content
     *
     * Diferencias clave vs OpenAI:
     *  - El system prompt va en 'system_instruction', NO en contents[].
     *  - Los roles son 'user' y 'model' (no 'assistant').
     *  - La API key va como query param (?key=...).
     */
    private function callGemini(string $apiKey, array $messages): string
    {
        $systemText = '';
        $contents   = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemText = $msg['content'];
                continue;
            }

            $contents[] = [
                'role'  => $msg['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['content']]],
            ];
        }

        // Gemini no acepta contents vacío — si solo hay system prompt, añadimos placeholder
        if (empty($contents)) {
            $contents[] = ['role' => 'user', 'parts' => [['text' => 'Hola']]];
        }

        $payload = [
            'contents'         => $contents,
            'generationConfig' => [
                'maxOutputTokens' => self::MAX_TOKENS,
                'temperature'     => self::TEMPERATURE,
            ],
        ];

        if ($systemText !== '') {
            $payload['system_instruction'] = ['parts' => [['text' => $systemText]]];
        }

        $url = sprintf(self::GEMINI_ENDPOINT, self::GEMINI_MODEL, $apiKey);

        $response = Http::timeout(self::HTTP_TIMEOUT)->post($url, $payload);

        if ($response->failed()) {
            throw new \RuntimeException(
                "Gemini HTTP {$response->status()}: " . substr($response->body(), 0, 200)
            );
        }

        $text = $response->json('candidates.0.content.parts.0.text');

        if (blank($text)) {
            // Puede indicar bloqueo por safety filters
            $finishReason = $response->json('candidates.0.finishReason');
            throw new \RuntimeException("Gemini respuesta vacía. finishReason: {$finishReason}");
        }

        return trim($text);
    }
}
