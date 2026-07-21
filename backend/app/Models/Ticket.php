<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number', 'title', 'description',
        'division_id', 'sender_name', 'category_id',
        'priority', 'priority_weight', 'status', 'progress', 'location',
        'created_by', 'assigned_to',
        'deadline', 'sla_due_at', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'status' => TicketStatus::class,
            'progress' => 'integer',
            'priority_weight' => 'integer',
            'deadline' => 'datetime',
            'sla_due_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // ---------- Relasi ----------

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function resolution(): HasOne
    {
        return $this->hasOne(TicketResolution::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(TicketRating::class);
    }

    // ---------- Scope & Helper ----------

    /**
     * Urutan antrian kerja (BR-2): Urgent -> Low, prioritas sama = FIFO (created_at ASC).
     */
    public function scopeQueueOrder(Builder $query): Builder
    {
        return $query->orderByDesc('priority_weight')->orderBy('created_at');
    }

    /** Tiket terbuka (belum selesai/ditolak) yang sudah melewati batas SLA. */
    public function isOverdue(): bool
    {
        return $this->status->isOpen()
            && $this->sla_due_at !== null
            && now()->greaterThan($this->sla_due_at);
    }

    /**
     * Indikator SLA untuk dashboard (BR-3): 'green' | 'yellow' | 'red'.
     */
    public function slaIndicator(): string
    {
        if (! $this->status->isOpen() || $this->sla_due_at === null) {
            return 'green';
        }
        if (now()->greaterThan($this->sla_due_at)) {
            return 'red';
        }
        $policy = SlaPolicy::where('priority', $this->priority->value)->first();
        $totalMinutes = $policy?->resolution_minutes ?? $this->priority->defaultSlaMinutes();
        $remaining = now()->diffInMinutes($this->sla_due_at, false);

        return $remaining <= ($totalMinutes / 2) ? 'yellow' : 'green';
    }
}
