<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreMessageRequest;
use App\Http\Resources\TicketMessageResource;
use App\Models\Ticket;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketMessageController extends Controller
{
    public function __construct(private readonly MessageService $messages) {}

    /** Daftar pesan chat sebuah tiket (kronologis). */
    public function index(Request $request, Ticket $ticket): AnonymousResourceCollection
    {
        $this->authorize('view', $ticket);

        $messages = $ticket->messages()
            ->with(['author', 'attachments', 'mentions'])
            ->orderBy('created_at')
            ->paginate(min($request->integer('per_page', 30), 100));

        return TicketMessageResource::collection($messages);
    }

    /** Kirim pesan (opsional lampiran & mention). */
    public function store(StoreMessageRequest $request, Ticket $ticket): TicketMessageResource
    {
        $message = $this->messages->post(
            $ticket,
            $request->user(),
            (string) $request->input('body', ''),
            $request->file('attachments', []),
            array_map('intval', $request->input('mentions', [])),
        );

        return new TicketMessageResource($message->load(['author', 'attachments', 'mentions']));
    }
}
