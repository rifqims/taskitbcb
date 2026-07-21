<?php

namespace App\Enums;

/**
 * Prioritas tiket. weight() dipakai untuk sorting antrian (BR-2):
 * urut priority_weight DESC, created_at ASC.
 * Lihat docs/03-srs.md & docs/06-database-erd.md.
 */
enum Priority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function weight(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
            self::Urgent => 4,
        };
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** Durasi SLA default dalam menit (dapat ditimpa oleh sla_policies). */
    public function defaultSlaMinutes(): int
    {
        return match ($this) {
            self::Low => 3 * 24 * 60,     // 3 hari
            self::Medium => 2 * 24 * 60,  // 2 hari
            self::High => 1 * 24 * 60,    // 1 hari
            self::Urgent => 4 * 60,       // 4 jam
        };
    }
}
