<?php

namespace App\Services;

use App\DTOs\CreateTicketData;
use App\DTOs\ResolveTicketData;
use App\Enums\ResolutionOutcome;
use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Logika bisnis tiket (docs/03-srs.md & docs/05-arsitektur.md §2).
 * Controller memanggil service ini; service tidak tahu soal HTTP.
 */
class TicketService
{
    public function __construct(
        private readonly TicketNumberGenerator $numbers,
        private readonly SlaCalculator $sla,
        private readonly NotificationService $notifications,
    ) {}

    /** Client membuat tiket baru (UC1). */
    public function create(User $client, CreateTicketData $data): Ticket
    {
        return DB::transaction(function () use ($client, $data) {
            $now = now();

            $ticket = Ticket::create([
                'ticket_number' => $this->numbers->next($now),
                'title' => $data->title,
                'description' => $data->description,
                'division_id' => $data->divisionId,
                'sender_name' => $data->senderName,
                'category_id' => $data->categoryId,
                'priority' => $data->priority,
                'priority_weight' => $data->priority->weight(),
                'status' => TicketStatus::Baru,
                'progress' => 0,
                'location' => $data->location,
                'created_by' => $client->id,
                'deadline' => $data->deadline,
                'sla_due_at' => $this->sla->dueAt($data->priority, $now),
            ]);

            $this->log($ticket, $client, 'created', ['priority' => $data->priority->value]);

            // Notifikasi ke semua teknisi IT & admin bahwa ada tiket baru (FR-21).
            $technicians = User::whereIn('role', [Role::ItSupport->value, Role::Admin->value])
                ->where('is_active', true)
                ->get();
            $this->notifications->notifyMany($technicians, 'ticket_created', $ticket, [
                'ticket_number' => $ticket->ticket_number,
                'title' => $ticket->title,
                'priority' => $ticket->priority->value,
                'by' => $client->name,
            ]);

            return $ticket;
        });
    }

    /**
     * IT mengambil tiket (UC6). Update atomik: hanya berhasil bila masih Baru & belum
     * di-assign — mencegah dua teknisi mengambil tiket yang sama (BR: konkurensi).
     */
    public function assign(Ticket $ticket, User $technician): Ticket
    {
        $affected = Ticket::whereKey($ticket->id)
            ->where('status', TicketStatus::Baru->value)
            ->whereNull('assigned_to')
            ->update([
                'assigned_to' => $technician->id,
                'status' => TicketStatus::SedangDikerjakan->value,
                'started_at' => now(),
            ]);

        if ($affected === 0) {
            throw new ConflictHttpException('Tiket sudah diambil teknisi lain.');
        }

        $ticket->refresh();
        $this->log($ticket, $technician, 'assigned', ['technician' => $technician->name]);

        // Notifikasi ke client bahwa tiket diterima (FR-21).
        if ($ticket->creator) {
            $this->notifications->notify($ticket->creator, 'ticket_accepted', $ticket, [
                'ticket_number' => $ticket->ticket_number,
                'technician' => $technician->name,
            ]);
        }

        return $ticket;
    }

    /** Ubah progress 0/25/50/75/100 (FR-12). */
    public function updateProgress(Ticket $ticket, User $actor, int $progress): Ticket
    {
        $from = $ticket->progress;
        $ticket->update(['progress' => $progress]);
        $this->log($ticket, $actor, 'progress_changed', ['from' => $from, 'to' => $progress]);

        return $ticket;
    }

    /** Ubah status non-terminal (mis. tandai Pending / lanjutkan). */
    public function changeStatus(Ticket $ticket, User $actor, TicketStatus $status): Ticket
    {
        $from = $ticket->status->value;
        $ticket->update(['status' => $status]);
        $this->log($ticket, $actor, 'status_changed', ['from' => $from, 'to' => $status->value]);

        return $ticket;
    }

    /** Selesaikan atau tolak tiket (UC8, FR-13). */
    public function resolve(Ticket $ticket, User $technician, ResolveTicketData $data): Ticket
    {
        return DB::transaction(function () use ($ticket, $technician, $data) {
            $completed = $data->outcome === ResolutionOutcome::Completed;

            $ticket->update([
                'status' => $completed ? TicketStatus::Selesai : TicketStatus::Ditolak,
                'progress' => $completed ? 100 : $ticket->progress,
                'completed_at' => now(),
            ]);

            $ticket->resolution()->updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'outcome' => $data->outcome,
                    'reason' => $data->reason,
                    'berita_acara' => $data->beritaAcara,
                    'recommendation' => $data->recommendation,
                    'notes' => $data->notes,
                    'technician_id' => $technician->id,
                    'resolved_at' => now(),
                ],
            );

            $this->log($ticket, $technician, 'resolved', ['outcome' => $data->outcome->value]);

            // Notifikasi ke client: tiket selesai / ditolak (FR-21).
            if ($ticket->creator) {
                $this->notifications->notify(
                    $ticket->creator,
                    $completed ? 'ticket_completed' : 'ticket_rejected',
                    $ticket,
                    ['ticket_number' => $ticket->ticket_number],
                );
            }

            return $ticket;
        });
    }

    /** @param array<string, mixed> $meta */
    private function log(Ticket $ticket, User $actor, string $action, array $meta = []): void
    {
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => $actor->id,
            'action' => $action,
            'meta' => $meta,
        ]);
    }
}
