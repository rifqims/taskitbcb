<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketRating extends Model
{
    protected $fillable = ['ticket_id', 'rated_by', 'score', 'comment'];

    protected function casts(): array
    {
        return ['score' => 'integer'];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }
}
