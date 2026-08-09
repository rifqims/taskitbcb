import { useQuery } from '@tanstack/react-query'
import { Download } from 'lucide-react'
import { api } from '@/lib/api'
import { getToken } from '@/lib/api'
import { Skeleton } from '@/components/ui'

interface Summary {
  total: number; open: number; completed: number; rejected: number
  overdue: number; avg_completion_minutes: number; sla_achievement_percent: number
}
interface Analytics {
  tickets_per_month: { month: string; count: number }[]
  top_categories: { name: string; count: number }[]
  per_technician: { technician: string; assigned: number; completed: number; sla_achievement_percent: number }[]
}

export function ReportsPage() {
  const { data: summary, isLoading } = useQuery({
    queryKey: ['report-summary'],
    queryFn: async () => (await api.get<{ data: Summary }>('/reports/summary')).data.data,
  })
  const { data: analytics } = useQuery({
    queryKey: ['report-analytics'],
    queryFn: async () => (await api.get<{ data: Analytics }>('/reports/analytics')).data.data,
  })

  const cards = summary
    ? [
        { k: 'Total Tiket', v: summary.total },
        { k: 'Terbuka', v: summary.open },
        { k: 'Selesai', v: summary.completed },
        { k: 'Ditolak', v: summary.rejected },
        { k: 'Terlambat', v: summary.overdue },
        { k: 'SLA Tercapai', v: `${summary.sla_achievement_percent}%` },
      ]
    : []

  const maxMonth = Math.max(1, ...(analytics?.tickets_per_month.map((m) => m.count) ?? [1]))

  async function exportCsv() {
    const res = await api.get('/reports/export', { responseType: 'blob', headers: { Authorization: `Bearer ${getToken()}` } })
    const url = URL.createObjectURL(res.data)
    const a = document.createElement('a')
    a.href = url; a.download = 'laporan-tiket.csv'; a.click()
    URL.revokeObjectURL(url)
  }

  return (
    <div>
      <div className="mb-5 flex items-center justify-between">
        <div>
          <h1 className="text-[22px] font-bold tracking-tight">Laporan &amp; Analytics</h1>
          <p className="text-[13.5px]" style={{ color: 'var(--text-muted)' }}>Ringkasan performa & tren tiket</p>
        </div>
        <button className="btn btn-sm" onClick={exportCsv}><Download size={15} /> Export CSV</button>
      </div>

      <div className="mb-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
        {isLoading
          ? [0, 1, 2, 3, 4, 5].map((i) => <div key={i} className="card p-4"><Skeleton className="h-12 w-full" /></div>)
          : cards.map((c) => (
              <div key={c.k} className="card p-4">
                <div className="text-xs font-medium" style={{ color: 'var(--text-muted)' }}>{c.k}</div>
                <div className="mt-2 text-[24px] font-bold leading-none tracking-tight">{c.v}</div>
              </div>
            ))}
      </div>

      <div className="grid gap-4 lg:grid-cols-2">
        <div className="card p-5">
          <h2 className="mb-4 text-[14.5px] font-semibold">Tiket per Bulan</h2>
          <div className="flex h-40 items-end gap-2">
            {analytics?.tickets_per_month.map((m) => (
              <div key={m.month} className="flex flex-1 flex-col items-center gap-1.5">
                <div className="text-[11px] font-semibold" style={{ color: 'var(--text-muted)' }}>{m.count}</div>
                <div className="w-full rounded-t-md" style={{ height: `${(m.count / maxMonth) * 100}%`, minHeight: 4, background: 'var(--grad)' }} />
                <div className="text-[10px]" style={{ color: 'var(--text-faint)' }}>{m.month.slice(5)}</div>
              </div>
            ))}
          </div>
        </div>

        <div className="card p-5">
          <h2 className="mb-4 text-[14.5px] font-semibold">Performa Teknisi (SLA)</h2>
          <div className="flex flex-col gap-3">
            {analytics?.per_technician.map((t) => (
              <div key={t.technician} className="flex items-center gap-3 text-[13px]">
                <span className="w-28 truncate font-medium">{t.technician}</span>
                <div className="h-2 flex-1 overflow-hidden rounded-full" style={{ background: 'var(--surface-2)' }}>
                  <div className="h-full rounded-full" style={{ width: `${t.sla_achievement_percent}%`, background: 'var(--grad)' }} />
                </div>
                <span className="w-24 text-right" style={{ color: 'var(--text-muted)' }}>{t.completed} selesai · {t.sla_achievement_percent}%</span>
              </div>
            ))}
            {analytics?.per_technician.length === 0 && <p className="text-[13px]" style={{ color: 'var(--text-faint)' }}>Belum ada data.</p>}
          </div>
        </div>
      </div>
    </div>
  )
}
