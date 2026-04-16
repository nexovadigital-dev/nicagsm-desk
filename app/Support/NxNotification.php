<?php

namespace App\Support;

use Filament\Notifications\Notification;

/**
 * Notificaciones toast reutilizables para el panel Nexova Desk Edge.
 *
 * Uso desde cualquier Resource/Page/Action:
 *   use App\Support\NxNotification;
 *
 *   NxNotification::success('Guardado', 'Los cambios se aplicaron.');
 *   NxNotification::error('Error', 'No se pudo completar la acción.');
 *   NxNotification::deleted('Contacto', 'Jose Contreras');
 */
class NxNotification
{
    // ── Genéricas ─────────────────────────────────────────────────────────────

    public static function success(string $title, ?string $body = null, int $ms = 5000): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->success()
            ->duration($ms)
            ->send();
    }

    public static function error(string $title, ?string $body = null, int $ms = 7000): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->danger()
            ->duration($ms)
            ->send();
    }

    public static function warning(string $title, ?string $body = null, int $ms = 6000): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->warning()
            ->duration($ms)
            ->send();
    }

    public static function info(string $title, ?string $body = null, int $ms = 5000): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->info()
            ->duration($ms)
            ->send();
    }

    // ── Semánticas (eventos comunes del panel) ─────────────────────────────────

    /** Registro eliminado. $entity = 'Contacto', $name = 'Jose Contreras' */
    public static function deleted(string $entity, ?string $name = null): void
    {
        $body = $name ? "{$name} fue eliminado correctamente." : null;
        Notification::make()
            ->title("{$entity} eliminado")
            ->body($body)
            ->danger()
            ->duration(5000)
            ->send();
    }

    /** Registro guardado/actualizado. */
    public static function saved(string $entity, ?string $name = null): void
    {
        $body = $name ? "Los cambios en {$name} se guardaron correctamente." : null;
        Notification::make()
            ->title("{$entity} guardado")
            ->body($body)
            ->success()
            ->duration(4000)
            ->send();
    }

    /** Acción que requiere atención del agente. */
    public static function attention(string $title, ?string $body = null): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->warning()
            ->persistent()
            ->send();
    }

    /** Ticket asignado o estado cambiado. */
    public static function ticketUpdated(string $title, ?string $body = null): void
    {
        Notification::make()
            ->title($title)
            ->body($body)
            ->success()
            ->duration(4000)
            ->send();
    }

    /**
     * Retorna un Notification::make() pre-configurado para usar en
     * ->successNotification() / ->failureNotification() de Actions.
     */
    public static function makeSuccess(string $title, ?string $body = null): Notification
    {
        return Notification::make()
            ->title($title)
            ->body($body)
            ->success()
            ->duration(5000);
    }

    public static function makeDanger(string $title, ?string $body = null): Notification
    {
        return Notification::make()
            ->title($title)
            ->body($body)
            ->danger()
            ->duration(5000);
    }
}
