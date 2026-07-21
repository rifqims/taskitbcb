<?php

namespace App\Enums;

/**
 * Hasil penyelesaian tiket (lihat FR-13). "rejected" wajib mengisi berita acara.
 */
enum ResolutionOutcome: string
{
    case Completed = 'completed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Completed => 'Selesai',
            self::Rejected => 'Tidak Bisa Dikerjakan',
        };
    }
}
