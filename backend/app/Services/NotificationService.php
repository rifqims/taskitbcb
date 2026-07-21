<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\User;

/**
 * Membuat notifikasi in-app (FR-20/21). Kanal email/PWA menyusul lewat interface
 * yang sama (Open/Closed — docs/05-arsitektur.md §3).
 */
class NotificationService
{
    /** @param array<string, mixed> $data */
    public function notify(User $user, string $type, ?Ticket $ticket = null, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'ticket_id' => $ticket?->id,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Notifikasi ke banyak user sekaligus (mis. daftar yang di-mention).
     *
     * @param  iterable<User>  $users
     * @param  array<string, mixed>  $data
     */
    public function notifyMany(iterable $users, string $type, ?Ticket $ticket = null, array $data = []): void
    {
        foreach ($users as $user) {
            $this->notify($user, $type, $ticket, $data);
        }
    }
}
