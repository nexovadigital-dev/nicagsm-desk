<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Mail\TicketClosedMail;
use App\Mail\SupportTicketMail;
use App\Models\Contact;
use App\Models\Department;
use App\Models\Message;
use App\Models\Ticket;
use App\Services\OrgMailer;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TicketsPage extends Page
{
    use ScopedToOrganization;
    protected string $view = 'filament.pages.tickets-page';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Tickets';
    protected static string|\UnitEnum|null $navigationGroup = 'Conversaciones';
    protected static ?int    $navigationSort  = 20;


    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-ticket';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // â”€â”€ Filters â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public string  $search            = '';
    public string  $filterStatus      = 'all';
    public string  $filterPriority    = 'all';
    public string  $filterPlatform    = 'all';
    public string  $filterDepartment  = 'all';
    public int     $perPage           = 20;

    // â”€â”€ New ticket modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $showNewModal      = false;
    public string $newContactMode    = 'existing'; // 'existing' | 'new'
    public string $newContactSearch  = '';
    public ?int   $newContactId      = null;
    public string $newClientName     = '';
    public string $newClientEmail    = '';
    public string $newClientPhone    = '';
    public string $newSubject        = '';
    public string $newMessage        = '';

    // â”€â”€ Ticket list query â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function getTicketsProperty()
    {
        return $this->scopeToOrg(
                Ticket::with(['messages' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(1), 'department'])
            )
            ->where('is_support_ticket', true)
            ->when(trim($this->search), fn ($q) =>
                $q->where(fn ($s) =>
                    $s->where('client_name', 'like', '%' . $this->search . '%')
                      ->orWhere('client_email', 'like', '%' . $this->search . '%')
                      ->orWhere('ticket_number', 'like', '%' . $this->search . '%')
                      ->orWhere('ticket_subject', 'like', '%' . $this->search . '%')
                )
            )
            ->when($this->filterStatus   !== 'all', fn ($q) => $q->where('status',   $this->filterStatus))
            ->when($this->filterPriority !== 'all', fn ($q) => $q->where('priority', $this->filterPriority))
            ->when($this->filterPlatform   !== 'all', fn ($q) => $q->where('platform', $this->filterPlatform))
            ->when($this->filterDepartment !== 'all', fn ($q) =>
                $this->filterDepartment === 'none'
                    ? $q->whereNull('department_id')
                    : $q->where('department_id', (int) $this->filterDepartment)
            )
            ->orderBy('ticket_opened_at', 'desc')
            ->paginate($this->perPage);
    }

    /** Contacts matching the search for the "existing" tab. */
    public function getContactSuggestionsProperty(): \Illuminate\Support\Collection
    {
        $s = trim($this->newContactSearch);
        if (strlen($s) < 2) return collect();

        $user  = auth()->user();
        $orgId = $user?->organization_id;

        return Contact::when($orgId, fn ($q) => $q->where('organization_id', $orgId))
            ->where(fn ($q) => $q
                ->where('name',  'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%")
            )
            ->limit(8)
            ->get();
    }

    public function getAvailableDepartmentsProperty()
    {
        return $this->scopeToOrg(Department::query())
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    // â”€â”€ New ticket actions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function openNewModal(): void
    {
        $this->reset(['newContactMode','newContactSearch','newContactId','newClientName',
                      'newClientEmail','newClientPhone','newSubject','newMessage']);
        $this->newContactMode = 'existing';
        $this->showNewModal   = true;
    }

    public function selectContact(int $id): void
    {
        $contact = Contact::find($id);
        if (! $contact) return;
        $this->newContactId     = $contact->id;
        $this->newClientName    = $contact->name  ?? '';
        $this->newClientEmail   = $contact->email ?? '';
        $this->newClientPhone   = $contact->phone ?? '';
        $this->newContactSearch = $contact->name  ?? $contact->email ?? '';
    }

    public function createTicket(): void
    {
        $subject = trim($this->newSubject);
        if (! $subject) return;

        $user  = Filament::auth()->user();
        $orgId = $user?->organization_id;

        // Resolve name / email from selected contact or new form
        $name  = trim($this->newClientName)  ?: null;
        $email = trim($this->newClientEmail) ?: null;
        $phone = trim($this->newClientPhone) ?: null;

        // Contact linking
        $contactId = $this->newContactId;
        if (! $contactId && $email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $contact   = Contact::findOrCreateByEmail($orgId, $email, $name, $phone, 'manual');
            $contactId = $contact->id;
        }

        // Generate ticket number
        $seq    = Ticket::where('is_support_ticket', true)->count() + 1;
        $number = 'TKT-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        $token  = Str::random(32);

        $ticket = Ticket::create([
            'organization_id'    => $orgId,
            'contact_id'         => $contactId,
            'platform'           => 'email',
            'status'             => 'human',
            'assigned_agent'     => $user?->name,
            'client_name'        => $name  ?? 'Cliente',
            'client_email'       => $email,
            'client_phone'       => $phone,
            'is_support_ticket'  => true,
            'ticket_number'      => $number,
            'ticket_subject'     => $subject,
            'ticket_reply_token' => $token,
            'ticket_opened_at'   => now(),
        ]);

        // Optional opening message from agent
        $body = trim($this->newMessage);
        if ($body) {
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'agent',
                'content'     => $body,
            ]);
        }

        // Send opening email to client
        if ($email) {
            $org        = $ticket->organization;
            $mailerName = OrgMailer::mailerNameFor($org);
            $mailable   = new SupportTicketMail($ticket);
            $mailerName
                ? Mail::mailer($mailerName)->to($email)->send($mailable)
                : Mail::to($email)->send($mailable);
        }

        $this->showNewModal = false;
    }

    // â”€â”€ Ticket actions â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function closeTicket(int $id): void
    {
        $ticket = Ticket::find($id);
        if (! $ticket || $ticket->status === 'closed') return;

        $ticket->update(['status' => 'closed']);

        // Send closed notification + survey link if ticket has email
        if ($ticket->is_support_ticket && $ticket->client_email && $ticket->ticket_reply_token) {
            $fresh      = $ticket->fresh();
            $org        = $fresh->organization;
            $mailerName = OrgMailer::mailerNameFor($org);
            $mailable   = new TicketClosedMail($fresh);
            $mailerName
                ? Mail::mailer($mailerName)->to($ticket->client_email)->send($mailable)
                : Mail::to($ticket->client_email)->send($mailable);
        }
    }

    public function reopenTicket(int $id): void
    {
        Ticket::where('id', $id)->where('status', 'closed')->update(['status' => 'human']);
    }

    public function setPriority(int $id, string $priority): void
    {
        Ticket::where('id', $id)->update(['priority' => $priority]);
    }

    public function setDepartment(int $id, string $deptId): void
    {
        $val = $deptId === '' ? null : (int) $deptId;
        Ticket::where('id', $id)
            ->where('organization_id', $this->orgId())
            ->update(['department_id' => $val]);
    }
}

