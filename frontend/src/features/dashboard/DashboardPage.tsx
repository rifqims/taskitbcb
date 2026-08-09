import { useQuery } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import { api } from '@/lib/api'
import { useAuth } from '@/lib/auth'
import { PriorityBadge, SlaBadge, Skeleton, EmptyState } from '@/components/ui'
import type { Paginated, Ticket } from '@/types'

const OPEN = ['baru', 'sedang_dikerjakan', 'pending']

export function DashboardPage() {
  const { user } = useAuth()
  const { data, isLoading } = useQuery({
    queryKey: ['tickets', 'dashboard'],
    queryFn: async () => (await api.get<Paginated<Ticket>>('/tickets', { params: { per_page: 100 } })).data,
  })

  const tickets = data?.data ?? []
  const count = (fn: (t: Ticket) => boolean) => tickets.filter(fn).length

  const stats = [
    { k: 'Total', v: tickets.length },
    { k: 'Baru', v: count((t) => t.status === 'baru') },
    { k: 'Dikerjakan', v: count((t) => t.status === 'sedang_dikerjakan') },
    { k: 'Selesai', v: count((t) => t.status === 'selesai') },
    { k: 'Terlambat', v: count((t) => t.is_overdue), danger: true },
    { k: 'Urgent Aktif', v: count((t) => t.priority === 'urgent' && OPEN.includes(t.status)), accent: true },
  ]

  const queue = tickets.filter((t) => OPEN.includes(t.status)).slice(0, 8)

  return (
    <div>
      <div className="mb-5">
        <h1 className="text-[22px] font-bold tracking-tight">Halo, {user?.name?.split(' ')[0]} 👋</h1>
        <p className="text-[13.5px]" style={{ color: 'var(--text-muted)' }}>
          {user?.role === 'client' ? 'Ringkasan tiket yang Anda kirimkan' : 'Ringkasan pekerjaan IT hari ini'}
        </p>
      </div>

      <div className="mb-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
        {stats.map((s) => (
          <div
            key={s.k}
            className="card p-4"
            style={s.accent ? { background: 'var(--grad-strong)', border: 'none', color: '#fff' } : undefined}
          >
            <div className="text-xs font-medium" style={{ color: s.accent ? 'rgba(255,255,255,.85)' : 'var(--text-muted)' }}>
              {s.k}
            </div>
            {isLoading ? (
              <Skeleton className="mt-2 h-7 w-10" />
            ) : (
              <div className="mt-2 text-[27px] font-bold leading-none tracking-tight" style={s.danger ? { color: 'var(--urgent)' } : undefined}>
                {s.v}
              </div>
            )}
          </div>
        ))}
      </div>

      <div className="card">
        <div className="flex items-center justify-between border-b px-[18px] py-[15px]" style={{ borderColor: 'var(--border)' }}>
          <h2 className="text-[14.5px] font-semibold">Antrian Prioritas</h2>
          <Link to="/tickets" className="text-[12.5px] font-semibold" style={{ color: 'var(--primary)' }}>
            Lihat semua →
          </Link>
        </div>
        <div className="p-2">
          {isLoading ? (
            <div className="flex flex-col gap-2 p-2">
              {[0, 1, 2, 3].map((i) => <Skeleton key={i} className="h-12 w-full" />)}
            </div>
          ) : queue.length === 0 ? (
            <EmptyState title="Tidak ada tiket aktif" hint="Semua pekerjaan sudah tuntas 🎉" />
          ) : (
            queue.map((t) => (
              <Link
                key={t.id} to={`/tickets/${t.id}`}
                className="flex items-center gap-3 rounded-[11px] px-3 py-[11px] hover:[background:var(--surface-2)]"
              >
                <PriorityBadge priority={t.priority} />
                <div className="min-w-0 flex-1">
                  <div className="mono text-[11px]" style={{ color: 'var(--text-faint)' }}>{t.ticket_number}</div>
                  <div className="truncate text-[13.5px] font-semibold">{t.title}</div>
                  <div className="mt-0.5 text-xs" style={{ color: 'var(--text-muted)' }}>
                    {t.division?.name} · {t.category?.name}
                  </div>
                </div>
                <SlaBadge indicator={t.sla_indicator} due={t.sla_due_at} />
              </Link>
            ))
          )}
        </div>
      </div>
    </div>
  )
}
