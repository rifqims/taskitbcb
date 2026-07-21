<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Output JSON tiket yang konsisten, termasuk field turunan (indikator SLA, overdue).
 *
 * @mixin \App\Models\Ticket
 */
class TicketResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority->value,
            'priority_label' => $this->priority->label(),
            'priority_weight' => $this->priority_weight,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'progress' => $this->progress,
            'location' => $this->location,
            'sender_name' => $this->sender_name,

            'division' => $this->whenLoaded('division', fn () => [
                'id' => $this->division->id,
                'name' => $this->division->name,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'icon' => $this->category->icon,
            ]),
            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'assignee' => $this->whenLoaded('assignee', fn () => $this->assignee ? [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
            ] : null),

            'sla_due_at' => $this->sla_due_at?->toIso8601String(),
            'sla_indicator' => $this->slaIndicator(),
            'is_overdue' => $this->isOverdue(),
            'deadline' => $this->deadline?->toIso8601String(),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),

            'resolution' => $this->whenLoaded('resolution', fn () => $this->resolution ? [
                'outcome' => $this->resolution->outcome->value,
                'reason' => $this->resolution->reason,
                'berita_acara' => $this->resolution->berita_acara,
                'recommendation' => $this->resolution->recommendation,
                'notes' => $this->resolution->notes,
                'resolved_at' => $this->resolution->resolved_at?->toIso8601String(),
            ] : null),
            'activities' => TicketActivityResource::collection($this->whenLoaded('activities')),
        ];
    }
}
