<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

/**
 * Otorisasi per-aksi pada tiket (RBAC halus). Ditemukan otomatis oleh Laravel
 * (App\Policies\TicketPolicy -> App\Models\Ticket). Lihat BR-5 di docs/03-srs.md.
 */
class TicketPolicy
{
    /** Admin & IT melihat semua; client hanya tiket miliknya. */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin() || $user->isItSupport()) {
            return true;
        }

        return $ticket->created_by === $user->id;
    }

    /** Hanya client yang boleh membuat tiket. */
    public function create(User $user): bool
    {
        return $user->isClient();
    }

    /** Ambil tiket: IT/admin, dan hanya tiket berstatus Baru (belum diambil). */
    public function assign(User $user, Ticket $ticket): bool
    {
        return ($user->isItSupport() || $user->isAdmin())
            && $ticket->status === TicketStatus::Baru
            && $ticket->assigned_to === null;
    }

    /** Ubah progress/status: hanya teknisi pemegang tiket atau admin (BR-5). */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $ticket->assigned_to === $user->id;
    }

    /** Beri rating: client pembuat tiket, saat tiket sudah selesai. */
    public function rate(User $user, Ticket $ticket): bool
    {
        return $ticket->created_by === $user->id
            && $ticket->status === TicketStatus::Selesai;
    }

    /** Hapus tiket: hanya admin (tercatat audit). */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }
}
