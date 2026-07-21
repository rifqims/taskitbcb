<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Laporan & Analytics (FR-22/24). Export CSV native; Excel/PDF dapat ditambah
 * lewat paket (maatwebsite/excel, dompdf) tanpa mengubah lapisan ini.
 */
class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function summary(Request $request): JsonResponse
    {
        $from = $request->date('from') ? Carbon::parse($request->date('from')) : null;
        $to = $request->date('to') ? Carbon::parse($request->date('to')) : null;

        return response()->json(['data' => $this->reports->summary($from, $to)]);
    }

    public function analytics(): JsonResponse
    {
        return response()->json(['data' => $this->reports->analytics()]);
    }

    /** Export daftar tiket ke CSV (streamed). */
    public function exportCsv(Request $request): StreamedResponse
    {
        $filename = 'laporan-tiket-'.now()->format('Ymd-His').'.csv';

        $query = Ticket::query()->with(['division', 'category', 'assignee'])->queueOrder();
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($priority = $request->string('priority')->toString()) {
            $query->where('priority', $priority);
        }

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Nomor', 'Judul', 'Divisi', 'Kategori', 'Prioritas', 'Status',
                'Progress', 'Teknisi', 'Dibuat', 'Selesai',
            ]);

            $query->chunk(200, function ($tickets) use ($out) {
                foreach ($tickets as $t) {
                    fputcsv($out, [
                        $t->ticket_number,
                        $t->title,
                        $t->division?->name,
                        $t->category?->name,
                        $t->priority->label(),
                        $t->status->label(),
                        $t->progress.'%',
                        $t->assignee?->name ?? '-',
                        $t->created_at?->format('Y-m-d H:i'),
                        $t->completed_at?->format('Y-m-d H:i') ?? '-',
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
