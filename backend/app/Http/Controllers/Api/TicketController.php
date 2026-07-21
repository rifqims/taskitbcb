<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateTicketData;
use App\DTOs\ResolveTicketData;
use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ChangeStatusRequest;
use App\Http\Requests\Ticket\ResolveTicketRequest;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateProgressRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function __construct(private readonly TicketService $tickets) {}

    /**
     * Daftar tiket dengan filter, pencarian, sorting, pagination.
     * Scoping RBAC: client hanya melihat tiket miliknya (FR-18/19).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $query = Ticket::query()->with(['division', 'category', 'assignee']);

        // Client hanya tiketnya sendiri.
        if ($user->isClient()) {
            $query->where('created_by', $user->id);
        }

        // "Task Saya" untuk teknisi.
        if ($request->boolean('mine') && ($user->isItSupport() || $user->isAdmin())) {
            $query->where('assigned_to', $user->id);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($priority = $request->string('priority')->toString()) {
            $query->where('priority', $priority);
        }
        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($divisionId = $request->integer('division_id')) {
            $query->where('division_id', $divisionId);
        }
        if ($request->boolean('overdue')) {
            $query->whereIn('status', [
                TicketStatus::Baru->value,
                TicketStatus::SedangDikerjakan->value,
                TicketStatus::Pending->value,
            ])->whereNotNull('sla_due_at')->where('sla_due_at', '<', now());
        }
        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        // Sorting: default = urutan antrian kerja (BR-2); opsi lain didukung.
        match ($request->string('sort')->toString()) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            default => $query->queueOrder(),
        };

        $perPage = min($request->integer('per_page', 15), 100);

        return TicketResource::collection($query->paginate($perPage));
    }

    /** Client membuat tiket (UC1). */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->tickets->create(
            $request->user(),
            CreateTicketData::fromArray($request->validated()),
        );

        // Simpan lampiran bila ada (FR-4).
        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('tickets/'.$ticket->id, 'local');
            $ticket->attachments()->create([
                'uploaded_by' => $request->user()->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return (new TicketResource($ticket->load(['division', 'category', 'creator'])))
            ->response()
            ->setStatusCode(201);
    }

    /** Detail tiket + timeline. */
    public function show(Ticket $ticket): TicketResource
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'division', 'category', 'creator', 'assignee', 'resolution',
            'activities' => fn ($q) => $q->with('user')->latest(),
        ]);

        return new TicketResource($ticket);
    }

    /** IT mengambil tiket (UC6). */
    public function assign(Request $request, Ticket $ticket): TicketResource
    {
        $this->authorize('assign', $ticket);

        $ticket = $this->tickets->assign($ticket, $request->user());

        return new TicketResource($ticket->load(['assignee', 'division', 'category']));
    }

    /** Ubah progress (FR-12). Otorisasi di UpdateProgressRequest. */
    public function updateProgress(UpdateProgressRequest $request, Ticket $ticket): TicketResource
    {
        $ticket = $this->tickets->updateProgress($ticket, $request->user(), (int) $request->validated('progress'));

        return new TicketResource($ticket);
    }

    /** Ubah status non-terminal (Pending / lanjutkan). */
    public function changeStatus(ChangeStatusRequest $request, Ticket $ticket): TicketResource
    {
        $ticket = $this->tickets->changeStatus(
            $ticket,
            $request->user(),
            TicketStatus::from($request->validated('status')),
        );

        return new TicketResource($ticket);
    }

    /** Selesaikan / tolak tiket (UC8, FR-13). */
    public function resolve(ResolveTicketRequest $request, Ticket $ticket): TicketResource
    {
        $ticket = $this->tickets->resolve(
            $ticket,
            $request->user(),
            ResolveTicketData::fromArray($request->validated()),
        );

        return new TicketResource($ticket->load('resolution'));
    }
}
