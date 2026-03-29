<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class NotificationSettings extends Page
{
    protected string $view = 'filament.pages.notification-settings';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Notificaciones';
    protected static string|\UnitEnum|null $navigationGroup = 'Cuenta';
    protected static ?int    $navigationSort  = 20;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-bell';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // Agent notification preferences
    public bool   $notifyAllUnread       = true;
    public bool   $notifyUnassigned      = true;
    public bool   $notifyAssignedToMe    = true;
    public bool   $notifyOnlyWhenOffline = false;
    public bool   $browserPushEnabled    = true;
    public bool   $emailNotifyEnabled    = false;

    public function mount(): void
    {
        $user  = Filament::auth()->user();
        $prefs = $user->notification_prefs ?? [];

        $this->notifyAllUnread       = $prefs['all_unread']        ?? true;
        $this->notifyUnassigned      = $prefs['unassigned']        ?? true;
        $this->notifyAssignedToMe    = $prefs['assigned_to_me']    ?? true;
        $this->notifyOnlyWhenOffline = $prefs['only_when_offline'] ?? false;
        $this->browserPushEnabled    = $prefs['browser_push']      ?? true;
        $this->emailNotifyEnabled    = $prefs['email_notify']      ?? false;
    }

    public function save(): void
    {
        $user = Filament::auth()->user();
        $user->update([
            'notification_prefs' => [
                'all_unread'        => $this->notifyAllUnread,
                'unassigned'        => $this->notifyUnassigned,
                'assigned_to_me'    => $this->notifyAssignedToMe,
                'only_when_offline' => $this->notifyOnlyWhenOffline,
                'browser_push'      => $this->browserPushEnabled,
                'email_notify'      => $this->emailNotifyEnabled,
            ],
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: 'Preferencias guardadas');
    }
}
