<?php

namespace App\Models;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Model;

class SlaPolicy extends Model
{
    protected $fillable = ['priority', 'resolution_minutes'];

    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'resolution_minutes' => 'integer',
        ];
    }
}
