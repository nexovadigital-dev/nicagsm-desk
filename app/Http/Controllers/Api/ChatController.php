<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBotReply;
use App\Models\ActiveVisitor;
use App\Models\BannedIp;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\ChatWidget;
use App\Models\WidgetSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // ── 0. Configuración pública del widget ──────────────────────────────────
    public function widgetConfig(Request $request): JsonResponse
    {
        // Si viene un token de widget personalizado, usarlo; si no, el global
        $token = $request->query('token');
        if ($token) {
            $cfg = ChatWidget::with('organization')->where('token', $token)->where('is_active', true)->first();
            if (! $cfg) {
                return response()->json(['error' => 'Widget not found'], 404);
            }
            // Block widget if org is disabled
            if ($cfg->organization && ! $cfg->organization->is_active) {
                return response()->json(['error' => 'Widget disabled', 'disabled' => true], 403);
            }
        } else {
            $cfg = WidgetSetting::instance();
        }

        // Determine if currently within working hours
        $isOnline = true;
        if ($cfg->working_hours_enabled && $cfg->working_hours) {
            $now     = now();
            $dayKey  = strtolower($now->format('D')); // mon, tue, ...
            $hours   = $cfg->working_hours[$dayKey] ?? null;
            if ($hours && ($hours['enabled'] ?? false)) {
                $from = \Carbon\Carbon::createFromFormat('H:i', $hours['from'] ?? '09:00');
                $to   = \Carbon\Carbon::createFromFormat('H:i', $hours['to']   ?? '18:00');
                $isOnline = $now->between($from, $to);
            } else {
                $isOnline = false;
            }
        }

        return response()->json([
            'bot_name'                => $cfg->bot_name,
            'welcome_message'         => $cfg->welcome_message,
            'accent_color'            => $cfg->accent_color,
            'show_branding'           => $cfg->show_branding,
            'require_rating'          => $cfg->require_rating,
            'rating_message'          => $cfg->rating_message,
            'sound_enabled'           => $cfg->sound_enabled ?? true,
            // New fields
            'widget_position'         => $cfg->widget_position ?? 'right',
            'widget_size'             => $cfg->widget_size ?? 'md',
            'preview_message_enabled' => $cfg->preview_message_enabled ?? false,
            'preview_message'         => $cfg->preview_message,
            'attention_effect'        => $cfg->attention_effect ?? 'none',
            'show_on'                 => $cfg->show_on ?? 'both',
            'default_screen'          => $cfg->default_screen ?? 'home',
            'faq_enabled'             => $cfg->faq_enabled ?? false,
            'faq_quick_reply'         => $cfg->faq_quick_reply ?? true,
            'faq_items'               => $cfg->faq_items ?? [],
            'social_channels'         => $cfg->social_channels ?? [],
            'is_online'               => $isOnline,
            'offline_message'         => $cfg->offline_message,
            'button_style'            => $cfg->button_style      ?? 'icon',
            'button_icon'             => $cfg->button_icon       ?? 'chat',
            'button_text'             => $cfg->button_text       ?? '',
            'button_text_color'       => $cfg->button_text_color ?? '#ffffff',
            'button_image'            => $cfg->button_image
                ? (str_starts_with($cfg->button_image, 'http') ? $cfg->button_image : \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim(str_replace('/storage/', '', $cfg->button_image), '/')))
                : null,
            'pre_chat_enabled'        => $cfg->pre_chat_enabled ?? false,
            'pre_chat_fields'         => $cfg->pre_chat_fields ?? [],
            'bot_enabled'             => $cfg->bot_enabled ?? true,
            'bot_avatar'              => $cfg->bot_avatar
                ? (str_starts_with($cfg->bot_avatar, 'http') ? $cfg->bot_avatar : \Illuminate\Support\Facades\Storage::disk('public')->url(ltrim(str_replace('/storage/', '', $cfg->bot_avatar), '/')))
                : null,
        ]);
    }

    // ── 1. Iniciar sesión ────────────────────────────────────────────────────
    public function startSession(Request $request): JsonResponse
    {
        $request->validate([
            'client_name'    => 'nullable|string|max:255',
            'client_email'   => 'nullable|email|max:255',
            'client_phone'   => 'nullable|string|max:50',
            'referrer'       => 'nullable|string|max:500',
            'page'           => 'nullable|string|max:500',
            'token'          => 'nullable|string|max:64',
            // WooCommerce identity
            'woo_customer'   => 'nullable|array',
            'woo_customer.id'     => 'nullable|integer',
            'woo_customer.email'  => 'nullable|email|max:255',
            'woo_customer.name'   => 'nullable|string|max:255',
            'woo_customer.phone'  => 'nullable|string|max:50',
            'woo_customer.avatar' => 'nullable|string|max:500',
            'woo_token'      => 'nullable|string|max:128', // HMAC signature
            // Store context inyectado por el plugin WooCommerce
            'store_context'  => 'nullable|array',
        ]);

        // Resolve organization_id from widget token
        $orgId = null;
        if ($request->token) {
            $widget = ChatWidget::with('organization')->where('token', $request->token)->where('is_active', true)->first();
            $org    = $widget?->organization;
            $orgId  = $widget?->organization_id;

            // Block if org disabled
            if ($org && ! $org->is_active) {
                return response()->json(['error' => 'Account disabled'], 403);
            }
        }

        // Check daily bot session limit
        $botStatus = 'bot';
        if (isset($org) && $org) {
            if (! $org->canStartBotSession()) {
                $botStatus = 'human'; // Limit reached — skip bot, go straight to human
            } else {
                $org->incrementBotSessions();
            }
        }

        $sessionId = Str::uuid()->toString();
        $ip        = $request->ip();

        // Detectar info del user-agent
        $ua      = $request->userAgent() ?? '';
        $uaInfo  = $this->parseUserAgent($ua);

        // Geolocalización por IP (gratis, sin API key)
        $geo = $this->geolocate($ip);

        // ── Resolve Contact ───────────────────────────────────────────────────
        $contact    = null;
        $clientName = $request->client_name;
        $clientEmail= $request->client_email;
        $clientPhone= $request->client_phone;

        // 1. WooCommerce identity (verified via HMAC)
        $woo = $request->input('woo_customer');
        if ($woo && isset($woo['id']) && $request->filled('woo_token') && isset($widget)) {
            $expected = hash_hmac(
                'sha256',
                $woo['id'] . '|' . ($woo['email'] ?? ''),
                $widget->token
            );
            if (hash_equals($expected, $request->woo_token)) {
                $contact = Contact::findOrCreateFromWooCommerce(
                    orgId:  $orgId,
                    wooId:  (int) $woo['id'],
                    email:  $woo['email']  ?? null,
                    name:   $woo['name']   ?? null,
                    phone:  $woo['phone']  ?? null,
                    avatar: $woo['avatar'] ?? null
                );
                // Use WooCommerce data as primary source
                $clientName  = $clientName  ?: $contact->name;
                $clientEmail = $clientEmail ?: $contact->email;
                $clientPhone = $clientPhone ?: $contact->phone;
            }
        }

        // 2. Email-based contact lookup — only if visitor provided a real email
        //    Anonymous visitors (no email) are NOT saved as contacts.
        if (! $contact && $clientEmail && filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            $contact = Contact::findOrCreateByEmail(
                orgId:  $orgId,
                email:  $clientEmail,
                name:   $clientName ?: null,
                phone:  $clientPhone ?: null,
                source: 'pre_chat'
            );
            $clientName = $clientName ?: $contact->name;
        }

        $clientName = $clientName ?: 'Visitante';

        // Sanitizar store_context: limitar tamaño para no sobrecargar el prompt de IA
        $storeContext = null;
        if ($request->filled('store_context')) {
            $raw = $request->input('store_context');
            // Truncar descripciones largas de productos/categorías
            if (isset($raw['products']) && is_array($raw['products'])) {
                $raw['products'] = array_slice($raw['products'], 0, 12);
                foreach ($raw['products'] as &$p) {
                    if (isset($p['description'])) {
                        $p['description'] = mb_substr($p['description'], 0, 200);
                    }
                }
                unset($p);
            }
            $storeContext = $raw;
        }

        $ticket = Ticket::create([
            'session_id'        => $sessionId,
            'platform'          => 'web',
            'status'            => $botStatus,
            'organization_id'   => $orgId,
            'widget_id'         => $widget?->id ?? null,
            'contact_id'        => $contact?->id,
            'client_name'       => $clientName,
            'client_email'      => $clientEmail,
            'client_phone'      => $clientPhone,
            'visitor_ip'        => $ip,
            'visitor_country'   => $geo['country'] ?? null,
            'visitor_city'      => $geo['city'] ?? null,
            'visitor_device'    => $uaInfo['device'],
            'visitor_os'        => $uaInfo['os'],
            'visitor_browser'   => $uaInfo['browser'],
            'visitor_referrer'  => $request->referrer,
            'visitor_page'      => $request->page,
            'store_context'     => $storeContext,
        ]);

        // Nombre de conversación
        $conversationName = ($clientName !== 'Visitante')
            ? $clientName
            : 'Visitante #' . $ticket->id;
        $ticket->update(['conversation_name' => $conversationName]);

        // Register conversation on contact
        if ($contact) {
            $contact->recordConversation();
        }

        return response()->json([
            'success'          => true,
            'session_id'       => $sessionId,
            'ticket_id'        => $ticket->id,
            'contact_id'       => $contact?->id,
            'returning_visitor'=> $contact && $contact->total_conversations > 1,
            'contact_name'     => $contact?->name,
        ]);
    }

    // ── 2. Enviar mensaje ────────────────────────────────────────────────────
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'content'    => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
        ]);

        $ticket = Ticket::where('session_id', $request->session_id)->first();

        if (! $ticket) {
            return response()->json(['error' => 'Sesión de chat no encontrada'], 404);
        }

        if ($ticket->status === 'closed') {
            return response()->json(['error' => 'Este chat está cerrado'], 422);
        }

        if (! $request->filled('content') && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Se requiere contenido o archivo'], 422);
        }

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            // Server-side limit: max 5 attachments per ticket
            $existingAttachments = Message::where('ticket_id', $ticket->id)
                ->whereNotNull('attachment_path')
                ->count();
            if ($existingAttachments >= 5) {
                return response()->json(['error' => 'Límite de 5 archivos adjuntos por conversación alcanzado'], 422);
            }

            $file           = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = $file->getMimeType();
            $attachmentPath = $file->store('chat-attachments', 'public');
        }

        $message = Message::create([
            'ticket_id'       => $ticket->id,
            'sender_type'     => 'user',
            'content'         => $request->content ?? '',
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
        ]);

        if ($ticket->status === 'bot') {
            ProcessBotReply::dispatch($ticket);
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    // ── 3. Obtener mensajes (short polling) ──────────────────────────────────
    public function getMessages(string $session_id): JsonResponse
    {
        $ticket = Ticket::where('session_id', $session_id)->first();

        if (! $ticket) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        $messages = Message::where('ticket_id', $ticket->id)
            ->orderBy('created_at')
            ->get()
            ->map(function ($m) {
                $data = $m->toArray();
                if ($m->attachment_path) {
                    $data['attachment_url'] = \Illuminate\Support\Facades\Storage::disk('public')->url($m->attachment_path);
                }
                return $data;
            });

        return response()->json([
            'success'           => true,
            'status'            => $ticket->status,
            'messages'          => $messages,
            'rating'            => $ticket->rating,
            'conversation_name' => $ticket->conversation_name,
        ]);
    }

    // ── 4. Solicitar agente humano ───────────────────────────────────────────
    public function requestAgent(Request $request): JsonResponse
    {
        $request->validate(['session_id' => 'required|string']);

        $ticket = Ticket::where('session_id', $request->session_id)->first();
        if (! $ticket) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        if ($ticket->status === 'closed') {
            return response()->json(['error' => 'El chat está cerrado'], 422);
        }

        if ($ticket->status !== 'human') {
            $ticket->update([
                'status'          => 'human',
                'agent_called_at' => now(),
            ]);
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'system',
                'content'     => 'Cliente solicitó atención con un agente.',
            ]);
        }

        // Retornar configuración de timeout del widget
        $widget  = $ticket->widget;
        $timeout = $widget?->agent_call_timeout ?? 10;
        $noResp  = $widget?->agent_no_response  ?? 'bot';

        return response()->json([
            'success'          => true,
            'status'           => 'human',
            'call_timeout_min' => $timeout,
            'no_response'      => $noResp,
        ]);
    }

    // ── 4b. Revertir a bot cuando expira el timeout sin respuesta ────────────
    public function revertToBot(Request $request): JsonResponse
    {
        $request->validate(['session_id' => 'required|string']);

        $ticket = Ticket::where('session_id', $request->session_id)->first();
        if (! $ticket) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        // Solo revertir si el ticket sigue esperando agente (no fue tomado)
        if ($ticket->status === 'human' && ! $ticket->messages()->where('sender_type', 'agent')->exists()) {
            $ticket->update([
                'status'          => 'bot',
                'agent_called_at' => null,
            ]);
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'system',
                'content'     => 'No hay agentes disponibles. El asistente IA continuará ayudándote.',
            ]);
        }

        return response()->json(['success' => true, 'status' => $ticket->fresh()->status]);
    }

    // ── 5. Calificación del chat ─────────────────────────────────────────────
    public function rateChat(Request $request): JsonResponse
    {
        $request->validate([
            'session_id'     => 'required|string',
            'rating'         => 'required|integer|min:1|max:5',
            'rating_comment' => 'nullable|string|max:500',
        ]);

        $ticket = Ticket::where('session_id', $request->session_id)->first();

        if (! $ticket) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        if ($ticket->status !== 'closed') {
            return response()->json(['error' => 'Solo se puede calificar un chat cerrado'], 422);
        }

        $ticket->update([
            'rating'         => $request->rating,
            'rating_comment' => $request->rating_comment,
        ]);

        return response()->json(['success' => true]);
    }

    // ── 6. Actualizar nombre del visitante ───────────────────────────────────
    public function updateVisitor(Request $request): JsonResponse
    {
        $request->validate([
            'session_id'   => 'required|string',
            'client_name'  => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:50',
        ]);

        $ticket = Ticket::where('session_id', $request->session_id)->first();
        if (! $ticket) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        $updateData = array_filter([
            'client_name'  => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
        ]);

        $ticket->update($updateData);

        // If email was added/changed, find or create a contact and link it
        if ($request->filled('client_email') && ! $ticket->contact_id) {
            $contact = Contact::findOrCreateByEmail(
                orgId:  $ticket->organization_id,
                email:  $request->client_email,
                name:   $ticket->client_name,
                phone:  $ticket->client_phone,
                source: 'widget'
            );
            $ticket->update(['contact_id' => $contact->id]);
            $contact->recordConversation();
        }

        return response()->json(['success' => true]);
    }

    // ── 7. Lookup de contacto (para widget — detectar visitante recurrente) ──
    public function contactLookup(Request $request): JsonResponse
    {
        $request->validate([
            'token'        => 'required|string|max:64',
            'email'        => 'nullable|email|max:255',
            'woo_customer' => 'nullable|array',
            'woo_customer.id'    => 'nullable|integer',
            'woo_customer.email' => 'nullable|email|max:255',
            'woo_token'    => 'nullable|string|max:128',
        ]);

        $widget = ChatWidget::where('token', $request->token)->where('is_active', true)->first();
        if (! $widget) {
            return response()->json(['found' => false]);
        }

        $orgId   = $widget->organization_id;
        $contact = null;

        // WooCommerce path (verified)
        $woo = $request->input('woo_customer');
        if ($woo && isset($woo['id']) && $request->filled('woo_token')) {
            $expected = hash_hmac('sha256', $woo['id'] . '|' . ($woo['email'] ?? ''), $widget->token);
            if (hash_equals($expected, $request->woo_token)) {
                $contact = Contact::where('organization_id', $orgId)
                    ->where('woo_customer_id', $woo['id'])
                    ->first();
                if (! $contact && isset($woo['email'])) {
                    $contact = Contact::where('organization_id', $orgId)
                        ->where('email', strtolower($woo['email']))
                        ->first();
                }
            }
        }

        // Email path
        if (! $contact && $request->filled('email')) {
            $contact = Contact::where('organization_id', $orgId)
                ->where('email', strtolower($request->email))
                ->first();
        }

        if (! $contact) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'        => true,
            'contact_id'   => $contact->id,
            'name'         => $contact->name,
            'email'        => $contact->email,
            'total_conversations' => $contact->total_conversations,
            'last_seen_at' => $contact->last_seen_at?->toISOString(),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    // ── Admin: polling de nuevos eventos (para sonidos/notificaciones) ────────
    public function adminNewEvents(Request $request): JsonResponse
    {
        $since     = $request->query('since');
        $sinceTime = $since ? \Carbon\Carbon::parse($since) : now()->subSeconds(10);

        // 1. Mensajes de usuario en tickets ya escalados a humano
        $newMessages = Message::with('ticket:id,conversation_name,client_name,status')
            ->whereHas('ticket', fn ($q) => $q
                ->where('platform', '!=', 'internal')
                ->where('status', 'human')
            )
            ->where('sender_type', 'user')
            ->where('created_at', '>', $sinceTime)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // 2. Tickets recién escalados a humano (bot o cliente solicitó intervención)
        //    Se detectan porque su updated_at cambió dentro de la ventana
        $newEscalations = Ticket::where('status', 'human')
            ->where('platform', '!=', 'internal')
            ->where('updated_at', '>', $sinceTime)
            ->whereNotIn('id', $newMessages->pluck('ticket_id'))
            ->get(['id', 'conversation_name', 'client_name', 'updated_at']);

        $events = $newMessages->map(fn ($m) => [
            'id'                => $m->id,
            'content'           => mb_substr($m->content, 0, 80),
            'created_at'        => $m->created_at->toISOString(),
            'conversation_name' => $m->ticket?->conversation_name ?? $m->ticket?->client_name ?? 'Visitante',
            'ticket_status'     => 'human',
            'event_type'        => 'message',
        ])->merge(
            $newEscalations->map(fn ($t) => [
                'id'                => 'esc-' . $t->id,
                'content'           => 'solicita asistencia de un agente',
                'created_at'        => $t->updated_at->toISOString(),
                'conversation_name' => $t->conversation_name ?? $t->client_name ?? 'Visitante',
                'ticket_status'     => 'human',
                'event_type'        => 'escalation',
            ])
        )->values();

        return response()->json([
            'count'       => $events->count(),
            'messages'    => $events,
            'server_time' => now()->toISOString(),
        ]);
    }

    // ── Renombrar conversación (usa primer mensaje del usuario como nombre) ───
    public function renameConversation(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id');
        $name      = trim((string) $request->input('name', ''));

        if (! $sessionId || ! $name) {
            return response()->json(['error' => 'Faltan parámetros'], 422);
        }

        // Truncar a 60 chars para que sea legible en la lista
        $name = mb_substr($name, 0, 60);

        $ticket = Ticket::where('session_id', $sessionId)->first();
        if (! $ticket) {
            return response()->json(['error' => 'Sesión no encontrada'], 404);
        }

        $ticket->update(['conversation_name' => $name]);

        return response()->json(['ok' => true, 'name' => $name]);
    }

    // ── Bulk: resumen de conversaciones por session_ids (historial widget) ───
    public function conversationsBulk(Request $request): JsonResponse
    {
        $ids = array_slice((array) $request->input('session_ids', []), 0, 20);
        if (empty($ids)) {
            return response()->json(['conversations' => []]);
        }

        $tickets = Ticket::whereIn('session_id', $ids)
            ->with(['messages' => function ($q) {
                $q->whereNotIn('sender_type', ['system'])
                  ->orderBy('created_at', 'desc')
                  ->limit(1);
            }])
            ->get()
            ->keyBy('session_id');

        $result = [];
        foreach ($ids as $sid) {
            $t = $tickets->get($sid);
            if (! $t) continue;

            $lastMsg = $t->messages->first();
            $result[] = [
                'session_id'      => $t->session_id,
                'status'          => $t->status,
                'conversation_name' => $t->conversation_name,
                'message_count'   => $t->messages()->whereNotIn('sender_type', ['system'])->count(),
                'last_message'    => $lastMsg?->content,
                'last_message_at' => $lastMsg?->created_at?->toISOString(),
                'started_at'      => $t->created_at->toISOString(),
                'rating'          => $t->rating,
            ];
        }

        return response()->json(['conversations' => $result]);
    }

    // ── Sneak-peek: visitante transmite texto mientras escribe ────────────────
    public function typingPreview(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id');
        $text      = $request->input('text', '');

        if (! $sessionId) {
            return response()->json(['ok' => false], 400);
        }

        $ticket = \App\Models\Ticket::where('session_id', $sessionId)->first();
        if (! $ticket) {
            return response()->json(['ok' => false], 404);
        }

        // Store for 30 seconds — agent panel reads this via Livewire
        \Illuminate\Support\Facades\Cache::put(
            "typing_preview_{$ticket->id}",
            ['text' => (string) $text, 'at' => now()->toIso8601String()],
            30
        );

        return response()->json(['ok' => true]);
    }

    // ── Admin: count de tickets activos (para badge del sidebar) ─────────────
    public function adminUnreadCount(): JsonResponse
    {
        $count = \App\Models\Ticket::where('status', 'human')
            ->where('platform', '!=', 'internal')
            ->count();

        return response()->json(['count' => $count]);
    }

    private function generateConversationName(): string
    {
        static $adjectives = [
            'Azul','Verde','Rojo','Dorado','Plata','Coral','Lima','Jade','Zafiro','Ámbar',
            'Gris','Rosa','Cielo','Turquesa','Índigo','Violeta','Teal','Bronce','Oliva','Crema',
        ];
        static $animals = [
            'Puma','Cóndor','Jaguar','Colibrí','Delfín','Halcón','Zorro','Búho','Lince','Lobo',
            'Tigre','León','Águila','Oso','Pantera','Serpiente','Elefante','Jirafa','Rinoceronte','Guepardo',
        ];

        return $adjectives[array_rand($adjectives)] . ' ' . $animals[array_rand($animals)];
    }

    // ── Visitor Management ───────────────────────────────────────────────────

    public function visitorPing(Request $request): JsonResponse
    {
        $token      = $request->input('widget_token');
        $visitorKey = $request->input('visitor_key');

        if (! $token || ! $visitorKey) {
            return response()->json(['ok' => false], 400);
        }

        $widget = ChatWidget::where('token', $token)->first();
        if (! $widget) {
            return response()->json(['ok' => false], 404);
        }

        $orgId = $widget->organization_id;
        $ip    = $request->ip();

        // Check if IP is banned
        if (BannedIp::where('organization_id', $orgId)->where('ip', $ip)->exists()) {
            return response()->json(['banned' => true, 'message' => 'Tu acceso a este chat ha sido restringido.']);
        }

        // Purge stale visitors (no ping in 90 seconds)
        ActiveVisitor::where('organization_id', $orgId)
            ->where('last_ping_at', '<', now()->subSeconds(90))
            ->delete();

        $existing   = ActiveVisitor::where('organization_id', $orgId)
                        ->where('visitor_key', $visitorKey)
                        ->first();

        // Build pages_visited — append if URL changed
        $currentUrl = $request->input('current_url');
        $pageTitle  = $request->input('page_title');
        $pages      = $existing ? ($existing->pages_visited ?? []) : [];
        $lastPage   = end($pages) ?: null;

        if (! $lastPage || $lastPage['url'] !== $currentUrl) {
            $pages[] = ['url' => $currentUrl, 'title' => $pageTitle, 'at' => now()->toIso8601String()];
            if (count($pages) > 20) {
                $pages = array_slice($pages, -20);
            }
        }

        $data = [
            'widget_token'   => $token,
            'current_url'    => $currentUrl,
            'page_title'     => $pageTitle,
            'referrer'       => $request->input('referrer') ?: ($existing?->referrer),
            'pages_visited'  => $pages,
            'ip'             => $ip,
            'is_idle'        => (bool) $request->input('is_idle', false),
            'tab_visible'    => (bool) $request->input('tab_visible', true),
            'chat_open'      => (bool) $request->input('chat_open', false),
            'session_id'     => $request->input('session_id'),
            'last_ping_at'   => now(),
        ];

        if (! $existing) {
            $ua             = $request->userAgent() ?? '';
            $uaInfo         = $this->parseUserAgent($ua);
            $geo            = $this->geolocate($ip);
            $data['first_seen_at'] = now();
            $data['country']       = $geo['country'] ?? null;
            $data['city']          = $geo['city']    ?? null;
            $data['device']        = $uaInfo['device'];
            $data['os']            = $uaInfo['os'];
            $data['browser']       = $uaInfo['browser'];
        }

        $visitor = ActiveVisitor::updateOrCreate(
            ['organization_id' => $orgId, 'visitor_key' => $visitorKey],
            $data
        );

        // Check proactive chat trigger from agent
        $openChat         = $visitor->proactive_open;
        $proactiveMessage = $visitor->proactive_message;

        if ($openChat) {
            $visitor->update(['proactive_open' => false, 'proactive_message' => null]);
        }

        return response()->json([
            'ok'                => true,
            'open_chat'         => $openChat,
            'proactive_message' => $proactiveMessage,
        ]);
    }

    public function visitorOffline(Request $request): JsonResponse
    {
        $token      = $request->input('widget_token');
        $visitorKey = $request->input('visitor_key');

        if ($token && $visitorKey) {
            $widget = ChatWidget::where('token', $token)->first();
            if ($widget) {
                ActiveVisitor::where('organization_id', $widget->organization_id)
                    ->where('visitor_key', $visitorKey)
                    ->delete();
            }
        }

        return response()->json(['ok' => true]);
    }

    private function parseUserAgent(string $ua): array
    {
        // Device
        $device = 'Desktop';
        if (preg_match('/Mobile|Android|iPhone/i', $ua)) $device = 'Mobile';
        if (preg_match('/iPad|Tablet/i', $ua))           $device = 'Tablet';

        // Browser (orden importa: Edge antes que Chrome)
        $browser = 'Otro';
        if (str_contains($ua, 'Edg/'))    $browser = 'Edge';
        elseif (str_contains($ua, 'OPR') || str_contains($ua, 'Opera')) $browser = 'Opera';
        elseif (str_contains($ua, 'Chrome'))   $browser = 'Chrome';
        elseif (str_contains($ua, 'Firefox'))  $browser = 'Firefox';
        elseif (str_contains($ua, 'Safari'))   $browser = 'Safari';

        // OS
        $os = 'Otro';
        if (str_contains($ua, 'Windows'))     $os = 'Windows';
        elseif (str_contains($ua, 'Mac OS'))  $os = 'macOS';
        elseif (str_contains($ua, 'Android')) $os = 'Android';
        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $os = 'iOS';
        elseif (str_contains($ua, 'Linux'))   $os = 'Linux';

        return compact('device', 'browser', 'os');
    }

    private function geolocate(string $ip): array
    {
        // Ignorar IPs locales
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return ['country' => 'Local', 'city' => 'Localhost'];
        }

        try {
            $res = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=country,city,status");
            if ($res->successful() && $res->json('status') === 'success') {
                return ['country' => $res->json('country'), 'city' => $res->json('city')];
            }
        } catch (\Exception $e) {
            Log::debug("Geolocate failed for {$ip}: {$e->getMessage()}");
        }

        return [];
    }
}
