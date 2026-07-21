<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Division;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Agregasi laporan & analytics (FR-22/24). Perhitungan dibuat portable (PHP-side)
 * agar konsisten lintas driver PostgreSQL & SQLite (test). Untuk data sangat besar,
 * agregasi ini dapat dipindah ke query DB (date_trunc / window function PostgreSQL).
 */
class ReportService
{
    private const OPEN = [
        TicketStatus::Baru->value,
        TicketStatus::SedangDikerjakan->value,
        TicketStatus::Pending->value,
    ];

    /** @return array<string, mixed> */
    public function summary(?Carbon $from = null, ?Carbon $to = null): array
    {
        $base = fn () => $this->scoped($from, $to);

        $total = $base()->count();
        $completed = (clone $base())->where('status', TicketStatus::Selesai->value)->get(['created_at', 'completed_at', 'sla_due_at']);

        $avgMinutes = $completed->isNotEmpty()
            ? round($completed->avg(fn ($t) => $t->created_at->diffInMinutes($t->completed_at)))
            : 0;

        $slaMet = $completed->filter(fn ($t) => $t->sla_due_at && $t->completed_at->lessThanOrEqualTo($t->sla_due_at))->count();
        $slaAchievement = $completed->isNotEmpty() ? round($slaMet / $completed->count() * 100, 1) : 0;

        return [
            'total' => $total,
            'open' => (clone $base())->whereIn('status', self::OPEN)->count(),
            'completed' => $completed->count(),
            'rejected' => (clone $base())->where('status', TicketStatus::Ditolak->value)->count(),
            'overdue' => (clone $base())->whereIn('status', self::OPEN)
                ->whereNotNull('sla_due_at')->where('sla_due_at', '<', now())->count(),
            'avg_completion_minutes' => $avgMinutes,
            'sla_achievement_percent' => $slaAchievement,
        ];
    }

    /** @return array<string, mixed> */
    public function analytics(): array
    {
        return [
            'tickets_per_month' => $this->ticketsPerMonth(6),
            'top_categories' => $this->topBy('category_id', Category::class),
            'top_divisions' => $this->topBy('division_id', Division::class),
            'per_technician' => $this->perTechnician(),
        ];
    }

    /** @return array<int, array{month: string, count: int}> */
    private function ticketsPerMonth(int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $start = now()->startOfMonth()->subMonths($i);
            $end = (clone $start)->endOfMonth();
            $result[] = [
                'month' => $start->format('Y-m'),
                'count' => Ticket::whereBetween('created_at', [$start, $end])->count(),
            ];
        }

        return $result;
    }

    /**
     * @param  class-string  $model
     * @return array<int, array{id: int, name: string, count: int}>
     */
    private function topBy(string $column, string $model): array
    {
        $rows = Ticket::query()
            ->selectRaw("{$column} as ref_id, count(*) as total")
            ->groupBy($column)
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'ref_id');

        $names = $model::whereIn('id', $rows->keys())->pluck('name', 'id');

        return $rows->map(fn ($total, $id) => [
            'id' => (int) $id,
            'name' => $names[$id] ?? '—',
            'count' => (int) $total,
        ])->values()->all();
    }

    /** @return array<int, array<string, mixed>> */
    private function perTechnician(): array
    {
        return User::where('role', Role::ItSupport->value)->get()->map(function (User $tech) {
            $handled = Ticket::where('assigned_to', $tech->id);
            $completed = (clone $handled)->where('status', TicketStatus::Selesai->value)
                ->get(['created_at', 'completed_at', 'sla_due_at']);

            $slaMet = $completed->filter(fn ($t) => $t->sla_due_at && $t->completed_at?->lessThanOrEqualTo($t->sla_due_at))->count();

            return [
                'technician' => $tech->name,
                'assigned' => (clone $handled)->count(),
                'completed' => $completed->count(),
                'avg_completion_minutes' => $completed->isNotEmpty()
                    ? round($completed->avg(fn ($t) => $t->created_at->diffInMinutes($t->completed_at)))
                    : 0,
                'sla_achievement_percent' => $completed->isNotEmpty()
                    ? round($slaMet / $completed->count() * 100, 1)
                    : 0,
            ];
        })->all();
    }

    private function scoped(?Carbon $from, ?Carbon $to)
    {
        $q = Ticket::query();
        if ($from) {
            $q->where('created_at', '>=', $from->startOfDay());
        }
        if ($to) {
            $q->where('created_at', '<=', $to->endOfDay());
        }

        return $q;
    }
}
