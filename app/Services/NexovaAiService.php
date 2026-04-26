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
    // Solo Groq — múltiples keys rotan automáticamente por prioridad en api_settings
    private const PROVIDERS = ['groq'];

    private const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';
    private const GROQ_MODEL    = 'llama-3.3-70b-versatile';

    private const MAX_TOKENS    = 800;
    private const TEMPERATURE   = 0.5;
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
                Log::debug("[NexovaBot] IA deshabilitada en Telegram — ticket #{$ticket->id}");
                return 'El asistente automático está desactivado en este canal. Un agente te atenderá pronto.' . self::ESCALATE_FLAG;
            }
        } elseif ($widget && ! $widget->bot_enabled) {
            Log::debug("[NexovaBot] Bot deshabilitado en widget #{$ticket->widget_id} — ticket #{$ticket->id}");
            return 'El asistente automático está desactivado. Un agente te atenderá pronto.' . self::ESCALATE_FLAG;
        }

        // ── Límite de mensajes por sesión ──────────────────────────────────────
        if ($org) {
            $maxPerSession = $org->max_messages_per_session ?: 30;
            $botMsgCount   = $ticket->messages()
                ->where('sender_type', 'bot')
                ->count();
            if ($botMsgCount >= $maxPerSession) {
                Log::debug("[NexovaBot] Límite de mensajes por sesión alcanzado — ticket #{$ticket->id}");
                return 'Has alcanzado el límite de mensajes con el asistente.' . self::ESCALATE_FLAG;
            }
        }

        // ── Verificar cuota mensual de mensajes del bot ───────────────────────
        if ($org && ! $org->hasMonthlyBotQuota()) {
            Log::debug("[NexovaBot] Cuota mensual alcanzada — ticket #{$ticket->id}");
            return 'Has alcanzado el límite de mensajes del bot para este mes. Por favor contacta soporte o actualiza tu plan.' . self::ESCALATE_FLAG;
        }

        // ── Interceptar saludos básicos (sin consumir API ni KB) ──────────────
        $greetingReply = $this->tryGreetingReply($ticket, $org);
        if ($greetingReply !== null) {
            $org?->incrementBotMessageCount();
            Log::debug("[NexovaBot] Respondido con saludo — ticket #{$ticket->id}");
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
                    Log::debug("[NexovaBot] Respondido desde KB Global (Telegram) — ticket #{$ticket->id}");
                    return $kbAnswerTelegram;
                }
            }

            // 2. Fallback: texto libre en telegram_config (retrocompatibilidad)
            $localAnswer = $this->tryTelegramLocalKbAnswer($ticket, $org);
            if ($localAnswer !== null) {
                sleep(random_int(1, 2));
                $org->incrementBotMessageCount();
                Log::debug("[NexovaBot] Respondido desde memoria texto Telegram (legacy) — ticket #{$ticket->id}");
                return $localAnswer;
            }
        }

        // ── FAQ siempre primero — respuestas manuales de máxima prioridad ───────
        $faqAnswer = $this->tryFaqAnswer($ticket);
        if ($faqAnswer !== null) {
            sleep(random_int(1, 2));
            $org?->incrementBotMessageCount();
            Log::debug("[NexovaBot] Respondido desde FAQ del widget — ticket #{$ticket->id}");
            return $faqAnswer;
        }

        // ── Atajo de pedidos WooCommerce (cero tokens de IA) ─────────────────
        // Solo si el widget tiene woo_orders_enabled y hay customer_orders en store_context.
        $wooOrdersOn = $widget ? (bool) ($widget->woo_orders_enabled ?? false) : false;
        $orderReply  = $wooOrdersOn ? $this->tryOrderQueryReply($ticket) : null;
        if ($orderReply !== null) {
            $org?->incrementBotMessageCount();
            Log::debug("[NexovaBot] Respondido con template de pedidos — ticket #{$ticket->id}");
            return $orderReply;
        }

        // ── Atajo de productos WooCommerce (cero tokens de IA) ────────────────
        // Busca en storeContext.products directamente para preguntas de precio/producto.
        // Evita depender de Groq para consultas simples de catálogo.
        $storeCtxRaw  = is_array($ticket->store_context) ? $ticket->store_context : [];
        $wooEnabled   = $widget ? (bool) ($widget->woo_integration_enabled ?? false) : false;
        $productReply = ($wooEnabled && ! empty($storeCtxRaw['products']))
            ? $this->tryProductQueryReply($ticket, $storeCtxRaw)
            : null;
        if ($productReply !== null) {
            $org?->incrementBotMessageCount();
            Log::debug("[NexovaBot] Respondido con template de producto — ticket #{$ticket->id}");
            return $productReply;
        }

        // ── Atajo de páginas WordPress (cero tokens de IA) ───────────────────
        // Busca en storeContext.pages por título/descripción para queries informativas.
        $pageReply = (! empty($storeCtxRaw['pages']))
            ? $this->tryPageQueryReply($ticket, $storeCtxRaw)
            : null;
        if ($pageReply !== null) {
            $org?->incrementBotMessageCount();
            Log::debug("[NexovaBot] Respondido con template de página — ticket #{$ticket->id}");
            return $pageReply;
        }


        // ── Intentar responder desde la KB ────────────────────────────────────
        // Solo salta KB directa si el ticket trae store_context real (WooCommerce page data),
        // en ese caso la IA con el catálogo responde mejor sobre productos/precios.
        // Si el widget tiene woo_integration_enabled = false, tratar store_context como vacío.
        $wooEnabled  = $widget ? (bool) ($widget->woo_integration_enabled ?? false) : false;
        $hasStoreCtx = $wooEnabled && ! empty($ticket->store_context);
        $widgetId    = $ticket->widget_id ?: null;
        $ragContext   = $this->buildRagContext($ticket->organization_id, $widgetId);
        $kbAnswer    = null;
        if ($ragContext && ! $hasStoreCtx) {
            $kbAnswer = $this->tryKbDirectAnswer($ticket, $ragContext, $widgetId);
        }
        if ($kbAnswer !== null) {
            sleep(random_int(1, 2));
            $org?->incrementBotMessageCount();
            Log::debug("[NexovaBot] Respondido desde KB local — ticket #{$ticket->id}");
            return $kbAnswer;
        }


        // --- IA deshabilitada en el widget (ai_enabled = false) ---
        if ($widget && $widget->ai_enabled === false) {
            Log::debug("[NexovaBot] IA deshabilitada en widget - escalando ticket #{$ticket->id}");
            return 'No tengo informacion sobre eso. Te pongo en contacto con un agente.' . self::ESCALATE_FLAG;
        }

        // ── Plan Free: IA bloqueada, solo KB ───────────────────────────────────
        // Excepción: si hay store_context (plugin WooCommerce), permitir IA para
        // responder sobre el catálogo de la tienda aunque el plan sea Free.
        if ($org && $org->isAiBlocked() && ! $hasStoreCtx) {
            Log::debug("[NexovaBot] IA bloqueada por plan Free — ticket #{$ticket->id}");
            return 'No encontré información en nuestra base de conocimiento para tu consulta. ¿Te gustaría hablar con un agente?' . self::ESCALATE_FLAG;
        }

        // ── Construir providers: org keys tienen prioridad ──────────────────────
        $providers = $this->buildProviders($org);

        if ($providers->isEmpty()) {
            Log::warning('[NexovaBot] No hay proveedores de IA activos.');
            return 'El asistente IA no está configurado en este momento.' . self::ESCALATE_FLAG;
        }

        $messages = $this->buildMessageHistory($ticket, $ragContext);

        // Delay "pensando" antes de llamar a la IA — espacía llamadas a Groq para evitar rate limit
        sleep(random_int(5, 8));

        foreach ($providers as $provider) {
            ['type' => $type, 'key' => $key] = $provider;
            try {
                $reply = match ($type) {
                    'groq'  => $this->callGroq($key, $messages),
                    default => throw new \RuntimeException("Proveedor no soportado: {$type}"),
                };

                // Strip markdown formatting — bot responses must be plain text
                $reply = $this->stripMarkdown($reply);

                // WOO_VERIFY_FLAG already embedded by the AI — don't double-add ESCALATE
                if (str_contains($reply, self::WOO_VERIFY_FLAG)) {
                    $org?->incrementBotMessageCount();
                    Log::debug("[NexovaBot] Respuesta con WOO_VERIFY via {$type} — ticket #{$ticket->id}");
                    return $reply;
                }

                // Only escalate if AI itself emits __ESCALATE__ flag
                $aiSuggestsEscalation = str_contains($reply, self::ESCALATE_FLAG);
                if ($aiSuggestsEscalation) {
                    $reply = trim(str_replace(self::ESCALATE_FLAG, '', $reply));
                }

                $org?->incrementBotMessageCount();
                Log::debug("[NexovaBot] Respuesta OK via {$type} — ticket #{$ticket->id}");
                return $aiSuggestsEscalation ? $reply . self::ESCALATE_FLAG : $reply;

            } catch (\Throwable $e) {
                Log::warning("[NexovaBot] {$type} falló (ticket #{$ticket->id}): {$e->getMessage()}");
            }
        }

        Log::error("[NexovaBot] Todos los proveedores fallaron para ticket #{$ticket->id}.");
        sleep(random_int(3, 5));
        return 'No logré comprender tu consulta. ¿Puedes reformularla o preguntarme de otra forma?';
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

        // Platform keys — Groq keys se mezclan aleatoriamente (load balance entre múltiples keys)
        $platform = ApiSetting::query()
            ->where('is_active', true)
            ->whereIn('provider', self::PROVIDERS)
            ->orderBy('priority')
            ->get();

        // Separar keys Groq para mezclarlas y distribuir carga
        $groqKeys  = $platform->where('provider', 'groq')->shuffle();
        $otherKeys = $platform->where('provider', '!=', 'groq');

        foreach ($groqKeys->merge($otherKeys) as $p) {
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
            ->get(['title', 'content', 'source', 'reference_id']);

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
            $plain   = $this->stripMarkdown($bestArticle->content);

            // Resumen: primer párrafo o máx 280 chars
            $firstPar = trim(explode("\n\n", $plain)[0]);
            $summary  = mb_strlen($firstPar) <= 280
                ? $firstPar
                : mb_substr($firstPar, 0, 280) . '…';

            // Si el artículo viene de web scrape con URL, añadir botón "Ver más"
            $sourceUrl = ($bestArticle->source === 'web_scrape' && ! empty($bestArticle->reference_id))
                ? $bestArticle->reference_id
                : null;

            if ($sourceUrl) {
                $summary .= "\n\n[Ver información completa]({$sourceUrl})";
            }

            return $summary;
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

                // Precio: simple o rango (productos variables)
                if (! empty($p['price']))       $entry .= " — {$p['price']}";
                elseif (! empty($p['price_range'])) $entry .= " — {$p['price_range']}";

                if (! empty($p['sku']))         $entry .= " (SKU: {$p['sku']})";
                if (! empty($p['stock']))       $entry .= " | Stock: {$p['stock']}";
                if (! empty($p['categories']))  $entry .= " | Cat: {$p['categories']}";

                // Descripción corta
                if (! empty($p['description'])) $entry .= "\n  {$p['description']}";

                // Variantes de producto variable
                if (! empty($p['variants']) && is_array($p['variants'])) {
                    foreach ($p['variants'] as $v) {
                        $vLine = "    - {$v['variant']}: {$v['price']}";
                        if (! empty($v['url'])) $vLine .= " → {$v['url']}";
                        $entry .= "\n{$vLine}";
                    }
                }

                if (! empty($p['url']))         $entry .= "\n  Enlace: {$p['url']}";
                $lines[] = $entry;
            }
        }

        // Páginas del sitio (T&C, envíos, cuenta, tutoriales, etc.)
        if (! empty($ctx['pages']) && is_array($ctx['pages'])) {
            $lines[] = '';
            $lines[] = '--- PÁGINAS DEL SITIO ---';
            foreach ($ctx['pages'] as $pg) {
                if (empty($pg['title'])) continue;
                $pgLine = "• {$pg['title']}";
                if (! empty($pg['url'])) $pgLine .= " → {$pg['url']}";
                $lines[] = $pgLine;
            }
            $lines[] = 'REGLA DE PÁGINAS: Si el cliente pregunta sobre políticas, envíos, devoluciones, tutoriales, cómo hacer algo, o cualquier procedimiento del sitio: busca la página más relevante de la lista y SIEMPRE incluye su enlace como botón Markdown [Título de la página](url). Si hay varias páginas relacionadas, incluye máximo 2 botones. Si no encuentras coincidencia, indica que no tienes esa información y ofrece contacto con un agente.';
        }

        $lines[] = '';
        if (! empty($ctx['customer_orders']) && is_array($ctx['customer_orders'])) {
            $lines[] = '--- PEDIDOS RECIENTES DE ESTE CLIENTE ---';
            foreach ($ctx['customer_orders'] as $order) {
                $num     = $order['number'] ?? $order['id'] ?? '?';
                $status  = $order['status'] ?? '?';
                $total   = $order['total']  ?? '';
                $date    = $order['date']   ?? '';
                $payment = $order['payment_method'] ?? '';
                $note    = $order['customer_note'] ?? '';
                $rawOrderItems = ! empty($order['items']) && is_array($order['items'])
                    ? array_slice($order['items'], 0, 3)
                    : [];
                $orderItemNames = array_map(
                    fn($i) => is_array($i) ? ($i['name'] ?? '') : (string) $i,
                    $rawOrderItems
                );
                $items = implode(', ', array_filter($orderItemNames));
                $line  = "• Pedido {$num} — Estado: {$status}";
                if ($total)   $line .= " — Total: {$total}";
                if ($date)    $line .= " — Fecha: {$date}";
                if ($payment) $line .= " — Pago: {$payment}";
                if ($items)   $line .= "\n  Productos: {$items}";
                if ($note)    $line .= "\n  Nota del cliente: {$note}";
                $lines[] = $line;
            }
            $lines[] = 'Puedes responder preguntas sobre el estado de estos pedidos usando la información de arriba.';
            $lines[] = 'Si el cliente pregunta por un pedido que no aparece en la lista, indícale que puede consultar su historial completo en la tienda.';
        }

        $storeUrl = ! empty($ctx['shop_url']) ? $ctx['shop_url'] : (! empty($ctx['store_url']) ? $ctx['store_url'] : 'la página web');

        $lines[] = 'REGLA DE PRODUCTOS: Si el cliente menciona un producto específico o pregunta por precio/disponibilidad: (1) Muestra nombre, precio y stock. (2) Si tiene variantes, muéstralas con su precio individual. (3) SIEMPRE incluye el botón [Ver Producto](url) con el enlace directo del producto. (4) Si hay precio 0.00, NO digas gratis — es precio variable, indica que depende de la variante y provee el enlace obligatoriamente.';
        $lines[] = "REGLA DE SATURACIÓN: Si hay muchos productos similares, menciona solo 2-3 opciones destacadas y añade [Ver todos en la tienda]({$storeUrl}). No enumeres el catálogo completo.";

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

        $normalized = mb_strtolower(trim($lastMsg));
        $normalized = preg_replace('/[^a-záéíóúüñ\s]/u', '', $normalized);
        $normalized = trim($normalized);

        // Si el mensaje tiene más de 6 palabras, contiene una pregunta real — no interceptar
        $wordCount = str_word_count($normalized);
        if ($wordCount > 6) return null;

        // Si contiene palabras de intención específica, la IA debe responder
        $intentKeywords = [
            'pedido', 'pedidos', 'orden', 'ordenes', 'compra', 'compras',
            'precio', 'precios', 'producto', 'productos', 'stock',
            'envio', 'envío', 'entrega', 'pago', 'factura',
            'cuenta', 'contraseña', 'sesion', 'sesión',
            'ayuda', 'info', 'informacion', 'información',
            'costo', 'disponible', 'tutorial', 'como', 'cómo',
        ];
        foreach ($intentKeywords as $kw) {
            if (str_contains($normalized, $kw)) return null;
        }

        $greetings = [
            'hola', 'hi', 'hello', 'hey', 'buenas', 'buenos dias', 'buenos días',
            'buenas tardes', 'buenas noches', 'good morning', 'good afternoon',
            'saludos', 'qué tal', 'que tal', 'cómo estás', 'como estas',
            'ola', 'alo', 'aló',
        ];

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
                if (! empty($p['price']))            $entry .= " — {$p['price']}" . ($currency ? " {$currency}" : '');
                elseif (! empty($p['price_range']))  $entry .= " — {$p['price_range']}";
                if (! empty($p['stock']))            $entry .= " | {$p['stock']}";
                if (! empty($p['categories']))       $entry .= " | Cat: {$p['categories']}";
                if (! empty($p['description']))      $entry .= "\n  {$p['description']}";
                if (! empty($p['variants']) && is_array($p['variants'])) {
                    foreach ($p['variants'] as $v) {
                        $vLine = "    - {$v['variant']}: {$v['price']}";
                        if (! empty($v['url'])) $vLine .= " → {$v['url']}";
                        $entry .= "\n{$vLine}";
                    }
                }
                if (! empty($p['url']))              $entry .= "\n  URL: [Ver / Ordenar]({$p['url']})";
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
        $lines[] = 'REGLA EXTREMA DE SATURACIÓN: Si detectas que hay demasiados resultados o productos muy redundantes, menciona solo 1 o 2 opciones clave y explícale al cliente que encontrará el servicio adecuado navegando en la tienda. Añade un botón con Markdown para invitarle a la tienda.';

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
        $cacheKey = "nexova_rag_{$orgId}_{$widgetId}";

        return Cache::remember($cacheKey, 300, function () use ($orgId, $widgetId) {
            $articles = KnowledgeBase::query()
                ->where('is_active', true)
                ->when($orgId, fn ($q) => $q->where('organization_id', $orgId))
                ->where(function ($q) use ($widgetId) {
                    $q->whereNull('widget_id');
                    if ($widgetId) {
                        $q->orWhere('widget_id', $widgetId);
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
        });
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
        $formatRule = " FORMATO: Puedes usar **negrita** para resaltar datos importantes (nombres, precios, estados) y *cursiva* para énfasis. NO uses # para títulos ni backticks para código. Para enlaces SIEMPRE usa el formato Markdown exacto [texto del botón](url) — esto crea botones interactivos para el usuario. No uses emojis salvo que el usuario los use primero. Las listas deben ir con guiones o numeradas.";

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
            $systemPrompt .= " RAZONAMIENTO: Analiza la intención completa del mensaje del cliente aunque esté mezclada con un saludo. Si pregunta por pedidos, productos, precios, páginas o cualquier información, responde directamente al tema — no solo al saludo.";
            $systemPrompt .= " REGLA CRÍTICA FUERA DE TEMA: Si el cliente pregunta algo completamente ajeno a {$orgName} (recetas, construcción, medicina, tutoriales genéricos, etc.), NO des ninguna información sobre ese tema. Responde únicamente con: 'No tengo la capacidad de ayudarte con eso. Pero si necesitas información sobre {$orgName}, con gusto te asisto.' y ofrece ayuda sobre la organización.";
            $systemPrompt .= " Nunca inventes datos. Si no tienes la información exacta, dilo y ofrece conectar con un agente.";
            if ($orgWeb) {
                $systemPrompt .= " Sitio web oficial: {$orgWeb}.";
            }
            $systemPrompt .= $formatRule;
        }

        // Contexto de tienda WooCommerce — solo si el widget tiene la integración habilitada
        $wooIntEnabled = $widget ? (bool) ($widget->woo_integration_enabled ?? false) : false;
        $storeCtx = ($wooIntEnabled && ! empty($ticket->store_context)) ? $ticket->store_context : [];
        if (! empty($storeCtx)) {
            $systemPrompt .= "\n\n" . $this->buildStoreContextBlock($storeCtx);

            // Identidad del cliente WooCommerce + reglas de pedidos
            $ticket->loadMissing('contact');
            $wooVerified = $ticket->contact && $ticket->contact->woo_customer_id;
            $storeBase   = rtrim($storeCtx['store_url'] ?? '', '/');
            $loginUrl    = $storeBase . '/mi-cuenta';
            $ordersUrl   = $storeBase . '/mi-cuenta/pedidos/';

            if ($wooVerified) {
                $hasOrders = ! empty($storeCtx['customer_orders']);
                $systemPrompt .= "\n\n**IDENTIDAD DEL CLIENTE:** El cliente está identificado y tiene sesión activa en la tienda. Puedes referirte a él por su nombre si lo tienes disponible.";

                if ($hasOrders) {
                    $systemPrompt .= "\n\nREGLAS PARA CONSULTAS DE PEDIDOS (cliente con sesión iniciada):
- Si pregunta por \"mis pedidos\", su historial o pedidos recientes: muestra los últimos 3 pedidos del bloque PEDIDOS RECIENTES de arriba (número, estado, total, fecha). Cierra con: [Ver todos mis pedidos]({$ordersUrl})
- Si pregunta por una orden específica (ej. \"estado de la orden #123\"): busca ese número en la lista. Si aparece → muestra su estado, total e ítems. Si NO aparece → responde \"No encontré la orden #[número] en tu cuenta. Verifica el número de pedido.\" y añade: [Ver mis pedidos]({$ordersUrl})
- Nunca inventes el estado de un pedido. Usa solo la información de la lista.";
                } else {
                    $systemPrompt .= "\n\nREGLAS PARA CONSULTAS DE PEDIDOS (cliente con sesión iniciada, sin pedidos recientes cargados):
- Si pregunta por pedidos: indica que no encontramos pedidos recientes en su cuenta y ofrece el link: [Ver mis pedidos]({$ordersUrl})";
                }
            } else {
                $systemPrompt .= "\n\n**IDENTIDAD DEL CLIENTE:** El visitante NO ha iniciado sesión en la tienda.

REGLAS PARA CONSULTAS DE PEDIDOS (cliente sin sesión):
- Si pregunta por sus pedidos, historial de compras, estado de un envío, o cualquier información de su cuenta: responde \"Para consultar tus pedidos necesitas iniciar sesión en tu cuenta.\" e incluye SIEMPRE: [Iniciar sesión]({$loginUrl})
- NO inventes pedidos ni información de cuenta.
- Para preguntas generales sobre productos, precios o la tienda, responde con normalidad.";
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

        // Catalogo WooCommerce via WP plugin para canal web (platform=web sin store_context en sesion)
        if ($ticket->platform === 'web' && $org && empty($ticket->store_context)) {
            $wpcatalog = $this->fetchStoreCatalogContext($org->id);
            if ($wpcatalog !== '') {
                $systemPrompt .= "\n\n" . $wpcatalog;
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
        // Encabezados: ## Texto → Texto (negritas/cursivas se MANTIENEN — el widget las renderiza)
        $text = preg_replace('/^#{1,6}\s+/mu', '', $text);

        // Código inline: `texto` → texto
        $text = preg_replace('/`([^`]+)`/', '$1', $text);

        // Bloques de código: ```...``` → solo el contenido
        $text = preg_replace('/```[\w]*\n?(.*?)```/su', '$1', $text);

        // Links: [texto](url) — MANTENER, se usan para generar botones
        // $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);

        // Viñetas markdown: - item o * item al inicio de línea (solo si no es negrita)
        $text = preg_replace('/^- /mu', '• ', $text);

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

    // =========================================================================
    // Atajo de pedidos WooCommerce — respuesta template sin IA
    // =========================================================================

    /**
     * Si el cliente pregunta por sus pedidos y el ticket tiene historial en
     * store_context['customer_orders'], responde con un template formateado.
     * Retorna null si no aplica (sin pedidos, o el mensaje no es consulta de pedidos).
     */
    private function tryOrderQueryReply(Ticket $ticket): ?string
    {
        $ctx    = is_array($ticket->store_context) ? $ticket->store_context : [];
        $orders = $ctx['customer_orders'] ?? [];

        // Solo aplica si el ticket tiene pedidos reales del cliente
        if (empty($orders) || ! is_array($orders)) return null;

        $lastMsg = $ticket->messages()
            ->where('sender_type', 'user')
            ->orderByDesc('created_at')
            ->value('content') ?? '';

        if (! $this->isOrderQuery($lastMsg)) return null;

        $storeBase = rtrim($ctx['store_url'] ?? '', '/');
        $ordersUrl = $storeBase ? "{$storeBase}/mi-cuenta/pedidos/" : null;

        // Consulta por pedido específico: "pedido #379", "#379", "orden 379"
        $specificNum = null;
        if (preg_match('/(?:orden|pedido|order)\s*#?\s*(\d{3,})/iu', $lastMsg, $m)
            || preg_match('/#(\d{3,})/', $lastMsg, $m)) {
            $specificNum = $m[1];
        }

        if ($specificNum !== null) {
            $found = null;
            foreach ($orders as $o) {
                $oNum = ltrim((string) ($o['number'] ?? $o['id'] ?? ''), '#');
                if ($oNum === $specificNum) {
                    $found = $o;
                    break;
                }
            }
            if ($found) {
                $num     = $found['number'] ?? $found['id'] ?? '?';
                $status  = $found['status'] ?? '?';
                $total   = $found['total']  ?? '';
                $date    = $found['date']   ?? '';
                $payment = $found['payment_method'] ?? '';
                $note    = $found['customer_note']  ?? '';
                $itemLines = [];
                foreach (array_slice($found['items'] ?? [], 0, 5) as $i) {
                    if (is_array($i)) {
                        $iLine = '• ' . ($i['name'] ?? '');
                        if (! empty($i['qty']))      $iLine .= ' × ' . $i['qty'];
                        if (! empty($i['subtotal'])) $iLine .= ' — ' . $i['subtotal'];
                        if (! empty($i['variant']))  $iLine .= ' *(' . $i['variant'] . ')*';
                        $itemLines[] = $iLine;
                    } else {
                        $itemLines[] = '• ' . (string) $i;
                    }
                }
                $reply  = "**Pedido {$num}**\n";
                $reply .= "📦 Estado: **{$status}**\n";
                if ($total)   $reply .= "💰 Total: **{$total}**\n";
                if ($date)    $reply .= "📅 Fecha: {$date}\n";
                if ($payment) $reply .= "💳 Método de pago: {$payment}\n";
                if ($itemLines) {
                    $reply .= "\n🛍️ *Productos:*\n" . implode("\n", $itemLines) . "\n";
                }
                if ($note) $reply .= "\n📝 *Nota:* {$note}\n";
                if ($ordersUrl) $reply .= "\n[📋 Ver todos mis pedidos]({$ordersUrl})";
                return $reply;
            }
            $reply = "No encontré el pedido **#{$specificNum}** en tu cuenta.";
            if ($ordersUrl) $reply .= "\n\n[Ver mis pedidos]({$ordersUrl})";
            return $reply;
        }

        // Consulta general: listar pedidos recientes
        $top   = array_slice($orders, 0, 3);
        $reply = "Aquí están tus pedidos más recientes:\n\n";
        foreach ($top as $o) {
            $num     = $o['number'] ?? $o['id'] ?? '?';
            $status  = $o['status'] ?? '?';
            $total   = $o['total']  ?? '';
            $date    = $o['date']   ?? '';
            $payment = $o['payment_method'] ?? '';
            $note    = $o['customer_note']  ?? '';
            $rawItems  = ! empty($o['items']) && is_array($o['items'])
                ? array_slice($o['items'], 0, 2)
                : [];
            $itemNames = array_map(fn($i) => is_array($i) ? ($i['name'] ?? '') : (string) $i, $rawItems);
            $items     = implode(', ', array_filter($itemNames));

            $reply .= "**Pedido {$num}** — **{$status}**";
            if ($total)   $reply .= " — {$total}";
            if ($date)    $reply .= " — {$date}";
            if ($payment) $reply .= "\n💳 {$payment}";
            if ($items)   $reply .= "\n🛍️ {$items}";
            if ($note)    $reply .= "\n📝 *{$note}*";
            $reply .= "\n\n";
        }
        if ($ordersUrl) $reply .= "[📋 Ver todos mis pedidos]({$ordersUrl})";

        return rtrim($reply);
    }

    /**
     * Detecta si el mensaje es una consulta sobre pedidos.
     */
    private function isOrderQuery(string $msg): bool
    {
        $t = mb_strtolower(trim($msg));
        $patterns = [
            '/mis\s+(pedidos?|ordenes?|compras?)/',
            '/ver\s+(mis\s+)?(pedidos?|ordenes?)/',
            '/historial\s+de\s+(pedidos?|compras?|ordenes?)/',
            '/donde\s+(esta|anda|queda)\s+(mi\s+)?(pedido|orden|paquete|env[ií]o)/',
            '/estado\s+de\s+(mi\s+)?(pedido|orden|env[ií]o|compra)/',
            '/cu[aá]ndo\s+(llega|lleg[oó]|viene)\s+(mi\s+)?(pedido|orden|paquete)/',
            '/pedidos?.*cuenta/',
            '/(?:orden|pedido|order)\s*#?\s*\d{3,}/',
            '/#\d{3,}/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $t)) return true;
        }
        return false;
    }
    // =========================================================================
    // Product Catalog — respuesta directa desde storeContext sin llamar a la IA
    // =========================================================================

    /**
     * Si el cliente pregunta por un producto/precio y el ticket tiene storeContext
     * con productos del catálogo WooCommerce, busca y responde directamente.
     * Retorna null si no aplica, dejando que la IA lo maneje.
     */
    private function tryProductQueryReply(Ticket $ticket, array $storeCtx): ?string
    {
        $products = $storeCtx['products'] ?? [];
        if (empty($products)) return null;

        $lastMsg = $ticket->messages()
            ->where('sender_type', 'user')
            ->orderByDesc('created_at')
            ->value('content') ?? '';

        if (strlen($lastMsg) < 2) return null;
        if (! $this->isProductQuery($lastMsg)) return null;

        $msgLower  = mb_strtolower($lastMsg);

        // Normalizador de acentos: la comparacion sera consistente en ambos lados
        $normStr = fn(string $s): string => strtr(mb_strtolower($s), [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n',
            'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
        ]);

        $stopwords = [
            'precio', 'precios', 'costo', 'cuanto', 'tienes', 'tienen',
            'venden', 'vende', 'como', 'cual', 'que', 'esta', 'hay', 'disponible',
            'info', 'informacion', 'busco', 'necesito', 'quiero',
            'comprar', 'saber', 'sobre', 'del', 'los', 'las', 'una', 'para', 'de', 'si',
        ];
        // Normalizar mensaje antes de extraer palabras (quitar acentos Y signos)
        $msgNorm = $normStr($msgLower);
        $words   = array_values(array_filter(
            explode(' ', preg_replace('/[^a-z0-9\s]/', '', $msgNorm)),
            fn ($w) => mb_strlen($w) > 1 && ! in_array($w, $stopwords)
        ));

        if (empty($words)) return null;

        // Expandir palabras concatenadas: "unlocktools" => ["unlock","tool","tools","unlocktools"]
        $expanded = $words;
        foreach ($words as $w) {
            if (mb_strlen($w) >= 7) {
                for ($c = 3; $c <= (int)(mb_strlen($w) * 0.6); $c++) {
                    $l = mb_substr($w, 0, $c);
                    $r = mb_substr($w, $c);
                    if (mb_strlen($l) >= 3 && mb_strlen($r) >= 3) {
                        $expanded[] = $l;
                        $expanded[] = $r;
                    }
                }
            }
        }
        $searchWords = array_unique($expanded);

        $matches = [];
        foreach ($products as $p) {
            $nameLower = $normStr($p['name'] ?? '');
            $descLower = $normStr($p['description'] ?? '');
            $score     = 0;
            foreach ($searchWords as $w) {
                if (str_contains($nameLower, $w)) $score += 3; // nombre: mayor peso
                if (str_contains($descLower, $w)) $score += 1; // descripcion: peso bajo
            }
            // Buscar en nombres de variantes normalizados
            if (! empty($p['variants']) && is_array($p['variants'])) {
                foreach ($p['variants'] as $v) {
                    $varLower = $normStr($v['variant'] ?? '');
                    foreach ($searchWords as $w) {
                        if (str_contains($varLower, $w)) $score += 2; // variante: peso medio
                    }
                }
            }
            if ($score > 0) {
                $matches[] = ['product' => $p, 'score' => $score];
            }
        }

        if (empty($matches)) return null;

        usort($matches, fn ($a, $b) => $b['score'] <=> $a['score']);

        $storeUrl = rtrim($storeCtx['store_url'] ?? '', '/');

        // Redirigir a la tienda solo si hay MUCHOS productos de alta relevancia
        // (evitar redirigir cuando hay 4 matches de poca calidad)
        $highScore = array_filter($matches, fn($m) => $m['score'] >= 6);
        if (count($highScore) > 4 || count($matches) > 8) {
            $reply = 'Encontré varios productos relacionados. Te recomiendo buscar en nuestra tienda para ver todos los detalles:';
            if ($storeUrl) {
                $reply .= "\n\n[🛒 Ver catálogo completo]({$storeUrl}/tienda)";
            }
            return $reply;
        }

        $top = array_slice($matches, 0, 3);

        // Funcion auxiliar: ordenar variantes poniendo las que coinciden primero
        $sortVariants = function (array $variants, array $words): array {
            usort($variants, function ($a, $b) use ($words) {
                $aScore = 0; $bScore = 0;
                $aLow   = mb_strtolower($a['variant'] ?? '');
                $bLow   = mb_strtolower($b['variant'] ?? '');
                foreach ($words as $w) {
                    if (str_contains($aLow, $w)) $aScore++;
                    if (str_contains($bLow, $w)) $bScore++;
                }
                return $bScore <=> $aScore;
            });
            return $variants;
        };

        // Un solo resultado — mostrarlo con detalle
        if (count($top) === 1) {
            $p     = $top[0]['product'];
            $reply = '**' . ($p['name'] ?? 'Producto') . "**\n";
            if (! empty($p['price']))       $reply .= "💰 Precio: **{$p['price']}**\n";
            if (! empty($p['stock']))       $reply .= "📦 Stock: {$p['stock']}\n";
            if (! empty($p['description'])) $reply .= "\n" . mb_substr($p['description'], 0, 160) . "\n";

            // Variantes con botones - ordenadas por relevancia con el query
            if (! empty($p['variants']) && is_array($p['variants'])) {
                $sortedVars = $sortVariants($p['variants'], $words);
                $reply .= "\n**Variantes disponibles:**\n";
                foreach ($sortedVars as $v) {
                    $reply .= "• {$v['variant']} — **{$v['price']}**";
                    if (! empty($v['url'])) $reply .= "  [🛒 Ordenar]({$v['url']})";
                    $reply .= "\n";
                }
            } elseif (! empty($p['url'])) {
                $reply .= "\n[🛒 Ver producto / Ordenar]({$p['url']})";
            }
            return rtrim($reply);
        }

        // Varios resultados — listado breve
        $reply = "Encontré estos productos relacionados:\n\n";
        foreach ($top as $m) {
            $p      = $m['product'];
            $reply .= '• **' . ($p['name'] ?? '') . '**';
            if (! empty($p['price'])) $reply .= " — {$p['price']}";
            // Variantes: mostrar hasta 3, las mas relevantes primero
            if (! empty($p['variants']) && is_array($p['variants'])) {
                $sortedVars = $sortVariants($p['variants'], $words);
                $reply .= "\n";
                foreach (array_slice($sortedVars, 0, 3) as $v) {
                    $reply .= "  • {$v['variant']} — {$v['price']}";
                    if (! empty($v['url'])) $reply .= "  [🛒 Ordenar]({$v['url']})";
                    $reply .= "\n";
                }
            } elseif (! empty($p['url'])) {
                $reply .= "\n  [Ver producto]({$p['url']})";
            }
            $reply .= "\n\n";
        }
        if ($storeUrl) {
            $reply .= "[🛒 Ver toda la tienda]({$storeUrl})";
        }
        return rtrim($reply);
    }

    // =========================================================================
    // Page Search — respuesta directa desde storeContext.pages sin IA
    // =========================================================================

    /**
     * Busca en storeContext.pages para responder preguntas informativas.
     * Sin gate de isPageQuery — busca contra ALL messages dinamicamente.
     * Incluye busqueda por titulo, snippet de URL (slug) y excerpt.
     */
    private function tryPageQueryReply(Ticket $ticket, array $storeCtx): ?string
    {
        $pages = $storeCtx['pages'] ?? [];
        if (empty($pages)) return null;

        $lastMsg = $ticket->messages()
            ->where('sender_type', 'user')
            ->orderByDesc('created_at')
            ->value('content') ?? '';

        if (strlen($lastMsg) < 3) return null;

        // Normalizador de acentos — igual que tryProductQueryReply
        $normStr = fn(string $s): string => strtr(mb_strtolower($s), [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n',
            'à'=>'a','è'=>'e','ì'=>'i','ò'=>'o','ù'=>'u',
        ]);

        $msgNorm   = $normStr($lastMsg);
        $stopwords = [
            'como','donde','que','hay','tienen','tienes','sus','ver',
            'quiero','puedo','necesito','saber','sobre','cual','cuales',
            'informacion','pagina','la','el','los','las','de','se','con',
            'hacer','para','una','sobre','del',
        ];
        $words = array_values(array_filter(
            explode(' ', preg_replace('/[^a-z0-9\s]/', '', $msgNorm)),
            fn ($w) => mb_strlen($w) > 2 && ! in_array($w, $stopwords)
        ));

        if (empty($words)) return null;

        $matches = [];
        foreach ($pages as $pg) {
            $titleLow   = $normStr($pg['title'] ?? '');
            $excerptLow = $normStr($pg['excerpt'] ?? '');
            // Convertir slug de URL a palabras: "pagar-con-binance" -> "pagar con binance"
            $urlSlug    = $normStr(str_replace(['-', '_', '/'], ' ',
                basename(rtrim($pg['url'] ?? '/', '/'))));
            $score = 0;
            foreach ($words as $w) {
                if (str_contains($titleLow, $w))   $score += 4; // titulo: maxima prioridad
                if (str_contains($urlSlug, $w))    $score += 3; // slug URL: alta prioridad
                if (str_contains($excerptLow, $w)) $score += 1; // excerpt: baja
            }
            if ($score > 0) {
                $matches[] = ['page' => $pg, 'score' => $score];
            }
        }

        if (empty($matches)) return null;

        usort($matches, fn ($a, $b) => $b['score'] <=> $a['score']);

        $top   = array_slice($matches, 0, 3);
        $count = count($top);
        $reply = ($count === 1)
            ? 'Encontré información relacionada:'
            : 'Aquí algunas páginas que pueden ayudarte:';
        $reply .= "\n\n";

        foreach ($top as $m) {
            $pg     = $m['page'];
            $title  = $pg['title'] ?? 'Ver página';
            $url    = $pg['url']   ?? '#';
            $reply .= "• [📄 {$title}]({$url})";
            // Mostrar excerpt solo si es texto real (no shortcode ni URL de YouTube)
            $ex = $pg['excerpt'] ?? '';
            if (strlen($ex) > 10
                && ! str_starts_with($ex, 'http')
                && ! str_starts_with($ex, 'youtube')
                && ! str_starts_with($ex, '[')) {
                $reply .= "\n  " . mb_substr($ex, 0, 120);
            }
            $reply .= "\n\n";
        }
        return rtrim($reply);
    }


    /**
     * Detecta si el mensaje es una consulta sobre páginas informativas del sitio.
     */
    private function isPageQuery(string $msg): bool
    {
        $t = mb_strtolower(trim($msg));

        // Keywords directos - sin PCRE para evitar ValueError en PHP8/PCRE2
        // mb_strtolower normaliza el input, se buscan formas sin acento tambien
        $keywords = [
            // Envios
            "envio", "envios", "despacho", "envío", "envíos",
            // Devoluciones
            "devolucion", "devoluciones", "devolver", "reembolso",
            "devolución", "devoluciónes",
            // Politicas / terminos
            "politica", "politicas", "condiciones",
            "política", "políticas",
            "terminos", "términos",
            // Info empresa
            "nosotros", "quienes somos",
            // Contacto
            "contacto", "contactanos",
            // Tutoriales
            "tutorial", "tutoriales",
            // Garantia
            "garantia", "garantía",
            // Privacidad
            "privacidad", "reglamento",
            // Metodos de pago especificos
            "lafise", "transferencia",
            // Frases comunes
            "como hacer pagos", "como pagar",
        ];
        foreach ($keywords as $kw) {
            if (str_contains($t, $kw)) return true;
        }
        return false;
    }

    private function isProductQuery(string $msg): bool
    {
        $t = mb_strtolower(trim($msg));

        // Caso 1: empieza con "precio" + al menos una palabra mas
        // Captura: "precio unlocktools", "precio unlocktools 3 meses", "precio Xiaomi Mi"
        if (str_starts_with($t, "precio ") && mb_strlen($t) > 8) {
            return true;
        }

        // Caso 2: patrones PCRE solo con caracteres ASCII-safe
        $patterns = [
            "/precio\s+de\s+/",
            "/precio\s+del?\s+/",
            "/cuanto\s+(cuesta|vale|sale|cobran)/",
            "/tiene[ns]?\s+.{3,}/",
            "/vende[ns]?\s+.{3,}/",
            "/busco\s+.{3,}/",
            "/quiero\s+(comprar|ordenar|pedir)\s+/",
            "/info\s+(de|del?)\s+/",
            "/disponib(le|ilidad)/",
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $t)) return true;
        }
        return false;
    }
}
