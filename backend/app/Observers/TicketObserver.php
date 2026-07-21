<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Audit Log (FR-26): merekam perubahan data tiket (before/after) untuk Administrator.
 * Terdaftar via atribut #[ObservedBy] pada model Ticket.
 */
class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        $this->record('created', $ticket, null, $ticket->getAttributes());
    }

    public function updated(Ticket $ticket): void
    {
        // Hanya kolom yang berubah (getChanges) beserta nilai lamanya.
        $changes = $ticket->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $old = array_intersect_key($ticket->getOriginal(), $changes);
        $this->record('updated', $ticket, $old, $changes);
    }

    public function deleted(Ticket $ticket): void
    {
        $this->record('deleted', $ticket, $ticket->getOriginal(), null);
    }

    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     */
    private function record(string $event, Ticket $ticket, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => Ticket::class,
            'auditable_id' => $ticket->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => Request::ip(),
        ]);
    }
}
