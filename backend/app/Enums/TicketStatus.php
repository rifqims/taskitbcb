<?php

namespace App\Enums;

/**
 * Status siklus hidup tiket. Lihat state machine di docs/03-srs.md.
 * "Terlambat" bukan status tersimpan, melainkan flag turunan dari sla_due_at (BR-4).
 */
enum TicketStatus: string
{
    case Baru = 'baru';
    case SedangDikerjakan = 'sedang_dikerjakan';
    case Pending = 'pending';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru',
            self::SedangDikerjakan => 'Sedang Dikerjakan',
            self::Pending => 'Pending',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
        };
    }

    /** Status yang dianggap "terbuka" (masih dihitung SLA). */
    public function isOpen(): bool
    {
        return in_array($this, [self::Baru, self::SedangDikerjakan, self::Pending], true);
    }
}
