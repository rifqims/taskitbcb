<?php

namespace App\Services;

use App\Enums\Priority;
use App\Models\SlaPolicy;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Menghitung batas SLA sebuah tiket berdasarkan prioritas (BR-3).
 * Mengambil durasi dari tabel sla_policies (dapat dikonfigurasi Admin),
 * fallback ke default enum bila belum ada.
 */
class SlaCalculator
{
    public function dueAt(Priority $priority, CarbonInterface $from): Carbon
    {
        return Carbon::instance($from)->addMinutes($this->minutesFor($priority));
    }

    public function minutesFor(Priority $priority): int
    {
        return SlaPolicy::where('priority', $priority->value)->value('resolution_minutes')
            ?? $priority->defaultSlaMinutes();
    }
}
