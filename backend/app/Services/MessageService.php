<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Percakapan pada tiket (FR-15..17): pesan, mention, lampiran, + memicu notifikasi.
 */
class MessageService
{
    public function __construct(private readonly NotificationService $notifications) {}

    /**
     * @param  array<int, UploadedFile>  $files
     * @param  array<int, int>  $mentionIds  id user yang di-mention
     */
    public function post(Ticket $ticket, User $author, string $body, array $files = [], array $mentionIds = []): TicketMessage
    {
        return DB::transaction(function () use ($ticket, $author, $body, $files, $mentionIds) {
            $message = $ticket->messages()->create([
                'user_id' => $author->id,
                'body' => $body,
            ]);

            foreach ($files as $file) {
                $message->attachments()->create([
                    'path' => $file->store('tickets/'.$ticket->id.'/chat', 'local'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            // Mention: catat + notifikasi (kecuali diri sendiri).
            $mentioned = collect($mentionIds)->unique()->reject(fn ($id) => $id === $author->id);
            foreach ($mentioned as $userId) {
                $message->mentions()->create(['mentioned_user_id' => $userId]);
            }
            if ($mentioned->isNotEmpty()) {
                $this->notifications->notifyMany(
                    User::whereIn('id', $mentioned)->get(),
                    'mention',
                    $ticket,
                    ['ticket_number' => $ticket->ticket_number, 'by' => $author->name],
                );
            }

            // Notifikasi balasan chat ke pihak lawan (client <-> teknisi).
            $recipientId = $author->id === $ticket->created_by ? $ticket->assigned_to : $ticket->created_by;
            if ($recipientId && $recipientId !== $author->id) {
                $this->notifications->notify(
                    User::find($recipientId),
                    'chat_reply',
                    $ticket,
                    ['ticket_number' => $ticket->ticket_number, 'by' => $author->name],
                );
            }

            return $message;
        });
    }
}
