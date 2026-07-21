<?php

namespace App\Services;

use App\Models\Ticket;
use Carbon\CarbonInterface;

/**
 * Menghasilkan nomor tiket unik & berurutan per bulan: TKT-YYYYMM-NNNNNN (BR-1).
 * WAJIB dipanggil di dalam transaksi (TicketService membungkusnya) agar lockForUpdate
 * mencegah race condition dua tiket bernomor sama.
 */
class TicketNumberGenerator
{
    public function next(CarbonInterface $when): string
    {
        $prefix = 'TKT-'.$when->format('Ym').'-';

        $last = Ticket::where('ticket_number', 'like', $prefix.'%')
            ->orderByDesc('ticket_number')
            ->lockForUpdate()
            ->value('ticket_number');

        $sequence = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}
