<?php

namespace App\Models;

use App\Enums\ResolutionOutcome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResolution extends Model
{
    protected $fillable = [
        'ticket_id', 'outcome', 'reason', 'berita_acara',
        'recommendation', 'notes', 'technician_id', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'outcome' => ResolutionOutcome::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
