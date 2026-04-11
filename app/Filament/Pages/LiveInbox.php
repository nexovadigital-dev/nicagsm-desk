<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Mail\SupportTicketMail;
use App\Mail\TicketClosedMail;
use App\Models\BannedIp;
use App\Models\Contact;
use App\Models\Department;
use App\Models\Message;
use App\Models\Tag;
use App\Models\Ticket;
use App\Services\OrgMailer;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class LiveInbox extends Page
{
    use ScopedToOrganization;
    use WithFileUploads;
    // No-static: coincide exactamente con BasePage::$view
    protected string $view = 'filament.pages.live-inbox';

    // Ancho completo: mismo tipo que BasePage::$maxContentWidth (Width|string|null)
    protected Width|string|null $maxContentWidth = 'full';

    // Propiedades que SÍ coinciden con el padre (?string / ?int) — sin conflicto
    protected static ?string $navigationLabel = 'Mensajes';
    protected static string|\UnitEnum|null $navigationGroup = 'Conversaciones';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationBadge(): ?string
    {
        $orgId = auth()->user()?->organization_id;
        $q = Ticket::whereIn('status', ['bot', 'human']);
        if ($orgId) $q->where('organization_id', $orgId);
        $count = $q->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $orgId = auth()->user()?->organization_id;
        $q = Ticket::where('status', 'human');
        if ($orgId) $q->where('organization_id', $orgId);
        return $q->exists() ? 'success' : 'warning';
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    // -------------------------------------------------------------------------
    // Estado Livewire
    // -------------------------------------------------------------------------

    public ?string $currentAgentAvatar = null;

    public bool $hasIncomingCall   = false;
    public int  $incomingCallCount = 0;

    public function mount(): void
    {
        $user = Filament::auth()->user();
        $this->currentAgentAvatar = $user?->avatar_path
            ? Storage::url($user->avatar_path)
            : null;
        $this->syncIncomingCalls();

        // Abrir ticket directo si viene de la alerta global (?ticket=ID)
        if ($ticketId = request()->integer('ticket')) {
            $this->selectTicket($ticketId);
        }
    }

    public function hydrate(): void
    {
        $this->syncIncomingCalls();
    }

    private function syncIncomingCalls(): void
    {
        $count = Ticket::where('status', 'human')
            ->whereNotNull('agent_called_at')
            ->where('agent_called_at', '>=', now()->subMinutes(15))
            ->whereDoesntHave('messages', fn ($q) => $q->where('sender_type', 'agent'))
            ->count();
        $this->hasIncomingCall   = $count > 0;
        $this->incomingCallCount = $count;
    }

    public function incomingAgentCalls(): Collection
    {
        return Ticket::where('status', 'human')
            ->whereNotNull('agent_called_at')
            ->where('agent_called_at', '>=', now()->subMinutes(15))
            ->whereDoesntHave('messages', fn ($q) => $q->where('sender_type', 'agent'))
            ->with('widget')
            ->get();
    }

    public ?int    $selectedTicketId  = null;
    public string  $inboxView         = 'active'; // 'active' | 'history'
    public string  $search            = '';
    public string  $filterDept        = 'all';
    public string  $replyContent     = '';
    #[\Livewire\Attributes\Rule(['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,pdf', 'max:8192'])]
    public         $replyAttachment  = null; // Livewire TemporaryUploadedFile
    public string  $ticketPriority    = 'normal';
    public string  $ticketCategory   = 'general';
    public string  $internalNotes    = '';
    public ?int    $ticketDepartmentId = null;

    // Support ticket modal
    public bool   $showTicketModal       = false;
    public string $ticketSubject         = '';
    public string $ticketEmailForTicket  = '';

    // Edit visitor modal
    public bool   $showVisitorModal  = false;
    public string $visitorName       = '';
    public string $visitorEmail      = '';
    public string $visitorPhone      = '';

    // Duplicate-contact resolution modal
    public bool  $showDuplicateModal = false;
    public array $duplicateContact   = [];   // {id, name, email} of existing contact found
    public array $pendingSaveData    = [];   // data waiting for resolution

    // -------------------------------------------------------------------------
    // Datos
    // -------------------------------------------------------------------------

    public function getAvailableDepartmentsProperty()
    {
        return $this->scopeToOrg(Department::query())
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    public function getTypingPreviewProperty(): ?string
    {
        if (! $this->selectedTicketId) return null;
        $data = \Illuminate\Support\Facades\Cache::get("typing_preview_{$this->selectedTicketId}");
        if (! $data || ! isset($data['text']) || $data['text'] === '') return null;
        return $data['text'];
    }

    public function getAvailableTagsProperty()
    {
        return $this->scopeToOrg(Tag::query())
            ->orderBy('name')
            ->get();
    }

    public function syncTags(array $tagIds): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;

        // Validate tag IDs belong to this org
        $orgId = $this->orgId();
        $valid = Tag::where('organization_id', $orgId)
            ->whereIn('id', array_map('intval', $tagIds))
            ->pluck('id')
            ->toArray();

        $ticket->tags()->sync($valid);
    }

    public function removeTag(int $tagId): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;
        $ticket->tags()->detach($tagId);
    }

    public function tickets(): Collection
    {
        $user  = auth()->user();
        $query = Ticket::query()
            ->with(['messages' => fn ($q) => $q->latest()->limit(1), 'widget', 'department', 'tags'])
            ->orderByDesc('updated_at');

        // Scope to org
        $this->scopeToOrg($query);

        // Agents without assign permission only see their own tickets
        if (! $this->isOrgAdmin()) {
            $perms = $user->permissions ?? [];
            if (empty($perms['assign_tickets'])) {
                $query->where('assigned_agent', $user->name);
            }
        }

        if ($this->inboxView === 'history') {
            $query->where('status', 'closed');
        } else {
            $query->whereIn('status', ['bot', 'human']);
        }

        if ($s = trim($this->search)) {
            $query->where(function ($q) use ($s) {
                $q->where('client_name',  'like', "%{$s}%")
                  ->orWhere('client_email', 'like', "%{$s}%")
                  ->orWhere('client_phone', 'like', "%{$s}%")
                  ->orWhereHas('messages', fn ($m) => $m->where('content', 'like', "%{$s}%"));
            });
        }

        if ($this->filterDept !== 'all') {
            $this->filterDept === 'none'
                ? $query->whereNull('department_id')
                : $query->where('department_id', (int) $this->filterDept);
        }

        return $query->get();
    }

    public function switchView(string $view): void
    {
        $this->inboxView        = $view;
        $this->selectedTicketId = null;
    }

    public function selectedTicket(): ?Ticket
    {
        return $this->selectedTicketId
            ? $this->findOrgTicket($this->selectedTicketId)
            : null;
    }

    /** Lookup a ticket scoped to the current org — prevents cross-org data access. */
    private function findOrgTicket(int $id): ?Ticket
    {
        $orgId = $this->orgId();
        return $orgId
            ? Ticket::where('organization_id', $orgId)->find($id)
            : Ticket::find($id); // super-admin fallback (no org)
    }

    public function chatMessages(): Collection
    {
        if (! $this->selectedTicketId) return collect();

        return Message::query()
            ->where('ticket_id', $this->selectedTicketId)
            ->orderBy('created_at')
            ->get();
    }

    // -------------------------------------------------------------------------
    // Acciones Livewire
    // -------------------------------------------------------------------------

    public function selectTicket(int $id): void
    {
        $this->selectedTicketId = $id;
        $this->replyContent     = '';

        // Cargar valores actuales del ticket en los controles
        $t = $this->findOrgTicket($id);
        if ($t) {
            $this->ticketPriority     = $t->priority       ?? 'normal';
            $this->ticketCategory     = $t->category       ?? 'general';
            $this->internalNotes      = $t->internal_notes ?? '';
            $this->ticketDepartmentId = $t->department_id;
        }
    }

    /** Guarda prioridad, categoría y notas internas. */
    public function saveTicketMeta(): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;
        $ticket->update([
            'priority'       => $this->ticketPriority,
            'category'       => $this->ticketCategory,
            'internal_notes' => $this->internalNotes,
            'department_id'  => $this->ticketDepartmentId,
        ]);
    }

    public function sendReply(): void
    {
        $content = trim($this->replyContent);
        if (! $content && ! $this->replyAttachment) return;
        if (! $this->selectedTicketId) return;

        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket || $ticket->status === 'closed') return;

        if ($ticket->status === 'bot') {
            $ticket->update(['status' => 'human']);
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($this->replyAttachment) {
            $file           = $this->replyAttachment;
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = $file->getMimeType();
            $attachmentPath = $file->store('chat-attachments', 'public');
            $this->replyAttachment = null;
        }

        Message::create([
            'ticket_id'       => $ticket->id,
            'sender_type'     => 'agent',
            'content'         => $content,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);

        $this->replyContent = '';
    }

    public function removeReplyAttachment(): void
    {
        $this->replyAttachment = null;
    }

    /** Nota interna — solo visible para agentes, nunca llega al visitante */
    public function sendNote(): void
    {
        $content = trim($this->replyContent);
        if (! $content || ! $this->selectedTicketId) return;

        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket || $ticket->status === 'closed') return;

        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'note',
            'content'     => $content,
        ]);

        $this->replyContent = '';
    }

    /** Banea la IP del visitante de este ticket */
    public function banVisitor(): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket || ! $ticket->visitor_ip) return;

        $orgId = $this->orgId();
        BannedIp::firstOrCreate(
            ['organization_id' => $orgId, 'ip' => $ticket->visitor_ip],
            ['reason' => 'Baneado desde Live Inbox']
        );

        $this->dispatch('nexova-toast', type: 'success', message: 'IP baneada: ' . $ticket->visitor_ip);
    }

    /** Desbanea la IP del visitante de este ticket */
    public function unbanVisitor(): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket || ! $ticket->visitor_ip) return;

        $orgId = $this->orgId();
        BannedIp::where('organization_id', $orgId)->where('ip', $ticket->visitor_ip)->delete();

        $this->dispatch('nexova-toast', type: 'success', message: 'IP desbaneada: ' . $ticket->visitor_ip);
    }

    /** ¿Está baneada la IP del ticket seleccionado? */
    public function isVisitorBanned(): bool
    {
        if (! $this->selectedTicketId) return false;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket || ! $ticket->visitor_ip) return false;

        return BannedIp::where('organization_id', $this->orgId())
            ->where('ip', $ticket->visitor_ip)
            ->exists();
    }

    /**
     * El agente toma la conversación manualmente ("Asignar a mí").
     * Cambia status → human, guarda su nombre y avisa al usuario con un mensaje de sistema.
     */
    public function assignToMe(): void
    {
        if (! $this->selectedTicketId) return;

        $ticket    = Ticket::find($this->selectedTicketId);
        $agentName = Filament::auth()->user()?->name ?? 'Agente';

        if (! $ticket || $ticket->status === 'closed') return;

        $ticket->update([
            'status'         => 'human',
            'assigned_agent' => $agentName,
            'agent_called_at' => null, // limpiar la alerta de llamada entrante
        ]);

        // Notificar al visitante en el widget
        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'system',
            'content'     => "Agente se unió a la conversación.",
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: "Tomaste el chat de {$ticket->client_name}");
    }

    /**
     * Rechaza la solicitud de agente — el bot retoma el flujo configurado.
     * Notifica al visitante con un mensaje amigable.
     */
    public function rejectAgentRequest(): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;

        // Respetar el flujo configurado en el widget (agent_no_response)
        $noResponse = $ticket->widget?->agent_no_response ?? 'bot';

        $ticket->update([
            'status'          => 'bot', // siempre vuelve a bot — el widget decide la UX
            'assigned_agent'  => null,
            'agent_called_at' => null,
        ]);

        // Notificar al widget para que ejecute el flujo correcto
        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'system',
            'content'     => 'Agente no disponible.',
        ]);

        // Solo añadir mensaje del bot si el flujo es "volver al bot"
        // Si es "ticket", el widget mostrará el formulario y no necesita msg del bot
        if ($noResponse !== 'ticket') {
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'bot',
                'content'     => 'En este momento no hay agentes disponibles. Puedo seguir ayudándote. ¿En qué más puedo asistirte?',
            ]);
        }

        $this->dispatch('nexova-toast', type: 'warning', message: 'Solicitud rechazada');
    }

    public function handBackToBot(): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;

        $agentName = $ticket->assigned_agent ?? 'El agente';

        $ticket->update([
            'status'          => 'bot',
            'assigned_agent'  => null,
            'agent_called_at' => null,
        ]);

        // Notificar al visitante
        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'system',
            'content'     => 'Agente abandonó la conversación.',
        ]);

        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'bot',
            'content'     => 'El agente ha finalizado la sesión. Estoy aquí para seguir ayudándote. ¿En qué puedo asistirte?',
        ]);
    }

    public function closeTicket(): void
    {
        if (! $this->selectedTicketId) return;

        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket || $ticket->status === 'closed') return;

        $ticket->update(['status' => 'closed']);

        // Send closed notification + survey link
        if ($ticket->is_support_ticket && $ticket->client_email && $ticket->ticket_reply_token) {
            $fresh      = $ticket->fresh();
            $org        = $fresh->organization;
            $mailerName = OrgMailer::mailerNameFor($org);
            $mailable   = new TicketClosedMail($fresh);
            $mailerName
                ? Mail::mailer($mailerName)->to($ticket->client_email)->queue($mailable)
                : Mail::to($ticket->client_email)->queue($mailable);
        }

        $this->selectedTicketId = null;
    }

    // -------------------------------------------------------------------------
    // Visitor — Edit modal
    // -------------------------------------------------------------------------

    public function openVisitorModal(): void
    {
        if (! $this->selectedTicketId) return;
        $t = $this->findOrgTicket($this->selectedTicketId);
        if (! $t) return;
        $this->visitorName  = $t->client_name  ?? '';
        $this->visitorEmail = $t->client_email ?? '';
        $this->visitorPhone = $t->client_phone ?? '';
        $this->showVisitorModal = true;
    }

    public function saveVisitor(): void
    {
        if (! $this->selectedTicketId) return;

        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;

        $name  = trim($this->visitorName)  ?: null;
        $email = trim($this->visitorEmail) ?: null;
        $phone = trim($this->visitorPhone) ?: null;

        // Only attempt contact linking if there is a valid email
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $orgId   = $ticket->organization_id;
            $pending = compact('name', 'email', 'phone');

            // Look for an existing contact with this email (excluding the already-linked one)
            $existing = Contact::where('organization_id', $orgId)
                ->where('email', strtolower($email))
                ->where('id', '!=', $ticket->contact_id ?? 0)
                ->first();

            if ($existing) {
                // Potential duplicate — ask the agent
                $this->duplicateContact   = ['id' => $existing->id, 'name' => $existing->name ?? '—', 'email' => $existing->email];
                $this->pendingSaveData    = $pending;
                $this->showVisitorModal   = false;
                $this->showDuplicateModal = true;
                return;
            }

            // No conflict — find or create contact and link
            $this->applyContactSave($ticket, $pending, createNew: false);
        } else {
            // No email → just update ticket fields, no contact
            $ticket->update(array_filter(['client_name' => $name, 'client_phone' => $phone]));
        }

        $this->showVisitorModal = false;
    }

    /** Agent chose to link with the existing duplicate contact. */
    public function linkWithDuplicate(): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;

        $data    = $this->pendingSaveData;
        $subject = $data['subject'] ?? null;

        $contact = Contact::find($this->duplicateContact['id'] ?? 0);
        if ($contact) {
            $contact->fill(array_filter([
                'name'  => $contact->name  ?: ($data['name']  ?? null),
                'phone' => $contact->phone ?: ($data['phone'] ?? null),
            ]))->save();
            $ticket->update([
                'contact_id'   => $contact->id,
                'client_name'  => $data['name']  ?? $ticket->client_name,
                'client_email' => $data['email'] ?? $ticket->client_email,
                'client_phone' => $data['phone'] ?? $ticket->client_phone,
            ]);
            $contact->update(['last_seen_at' => now()]);
        }

        $this->closeDuplicateModal();

        // If triggered from createSupportTicket, continue opening the ticket
        if ($subject) {
            $this->doOpenSupportTicket($ticket, $subject, $data['email'] ?? $ticket->client_email);
        }
    }

    /** Agent chose to create a brand-new contact (not a duplicate). */
    public function createNewContact(): void
    {
        if (! $this->selectedTicketId) return;
        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;

        $data    = $this->pendingSaveData;
        $subject = $data['subject'] ?? null;

        $this->applyContactSave($ticket, $data, createNew: true);
        $this->closeDuplicateModal();

        // If triggered from createSupportTicket, continue opening the ticket
        if ($subject) {
            $this->doOpenSupportTicket($ticket, $subject, $data['email'] ?? $ticket->client_email);
        }
    }

    private function applyContactSave(Ticket $ticket, array $data, bool $createNew): void
    {
        $orgId = $ticket->organization_id;
        $email = $data['email'] ?? null;
        $name  = $data['name']  ?? null;
        $phone = $data['phone'] ?? null;

        if ($createNew) {
            // Force-create ignoring any email unique constraint by nulling the link
            $contact = new Contact([
                'organization_id' => $orgId,
                'email'           => null, // avoid unique clash — agent decided it's different
                'name'            => $name,
                'phone'           => $phone,
                'source'          => 'manual',
            ]);
            // We can't use the same email if a contact with it exists, so store email in meta
            $contact->meta  = ['original_email' => $email];
            $contact->save();
        } else {
            $contact = Contact::findOrCreateByEmail(
                orgId:  $orgId,
                email:  $email,
                name:   $name,
                phone:  $phone,
                source: 'manual'
            );
        }

        $ticket->update([
            'contact_id'   => $contact->id,
            'client_name'  => $name  ?? $ticket->client_name,
            'client_email' => $email ?? $ticket->client_email,
            'client_phone' => $phone ?? $ticket->client_phone,
        ]);

        $contact->update(['last_seen_at' => now()]);
        if ($contact->total_conversations === 0) {
            $contact->increment('total_conversations');
        }
    }

    private function closeDuplicateModal(): void
    {
        $this->showDuplicateModal = false;
        $this->duplicateContact   = [];
        $this->pendingSaveData    = [];
    }

    // -------------------------------------------------------------------------
    // Support Ticket — Modal
    // -------------------------------------------------------------------------

    public function openTicketModal(): void
    {
        if (! $this->selectedTicketId) return;
        $t = $this->findOrgTicket($this->selectedTicketId);
        if (! $t) return;

        $this->ticketEmailForTicket = $t->client_email ?? '';
        $this->ticketSubject        = '';
        $this->showTicketModal      = true;
    }

    public function createSupportTicket(): void
    {
        $subject = trim($this->ticketSubject);
        if (! $this->selectedTicketId || ! $subject) return;

        $ticket = $this->findOrgTicket($this->selectedTicketId);
        if (! $ticket) return;

        $email = trim($this->ticketEmailForTicket) ?: $ticket->client_email;

        // If a valid email was provided, attempt contact linking (with duplicate check)
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $orgId   = $ticket->organization_id;
            $pending = [
                'name'    => $ticket->client_name,
                'email'   => $email,
                'phone'   => $ticket->client_phone,
                'subject' => $subject,
            ];

            $existing = Contact::where('organization_id', $orgId)
                ->where('email', strtolower($email))
                ->where('id', '!=', $ticket->contact_id ?? 0)
                ->first();

            if ($existing) {
                // Pause and ask agent to resolve duplicate before opening ticket
                $this->duplicateContact   = ['id' => $existing->id, 'name' => $existing->name ?? '—', 'email' => $existing->email];
                $this->pendingSaveData    = $pending;
                $this->showTicketModal    = false;
                $this->showDuplicateModal = true;
                return;
            }

            // No conflict — create or find contact and link
            $this->applyContactSave($ticket, $pending, createNew: false);
        }

        $this->doOpenSupportTicket($ticket, $subject, $email);
    }

    /** Opens the actual support ticket record and sends the email. Called after contact linking is resolved. */
    private function doOpenSupportTicket(Ticket $ticket, string $subject, ?string $email): void
    {
        $seq    = Ticket::where('is_support_ticket', true)->count() + 1;
        $number = 'TKT-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        $token  = Str::random(32);

        $ticket->update([
            'is_support_ticket'  => true,
            'ticket_number'      => $number,
            'ticket_subject'     => $subject,
            'ticket_reply_token' => $token,
            'ticket_opened_at'   => now(),
            'client_email'       => $email ?: $ticket->client_email,
        ]);

        if ($email) {
            $fresh      = $ticket->fresh();
            $org        = $fresh->organization;
            $mailerName = OrgMailer::mailerNameFor($org);
            $mailable   = new SupportTicketMail($fresh);
            $mailerName
                ? Mail::mailer($mailerName)->to($email)->queue($mailable)
                : Mail::to($email)->queue($mailable);
        }

        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'system',
            'content'     => "🎫 Ticket de soporte {$number} abierto — el cliente recibirá confirmación por correo.",
        ]);

        $this->showTicketModal      = false;
        $this->ticketSubject        = '';
        $this->ticketEmailForTicket = '';
    }
}
