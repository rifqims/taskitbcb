import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { Link, useSearchParams } from 'react-router-dom'
import { Search } from 'lucide-react'
import { api } from '@/lib/api'
import { PriorityBadge, StatusBadge, SlaBadge, Skeleton, EmptyState, Avatar } from '@/components/ui'
import type { Paginated, Ticket } from '@/types'

const STATUS_FILTERS = ['', 'baru', 'sedang_dikerjakan', 'pending', 'selesai', 'ditolak'] as const

export function TicketsPage() {
  const [sp] = useSearchParams()
  const mine = sp.get('mine') === '1'
  const [status, setStatus] = useState('')
  const [search, setSearch] = useState('')
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ['tickets', { mine, status, search, page }],
    queryFn: async () =>
      (await api.get<Paginated<Ticket>>('/tickets', {
        params: { mine: mine ? 1 : undefined, status: status || undefined, search: search || undefined, page },
      })).data,
  })

  const tickets = data?.data ?? []
  const meta = data?.meta

  return (
    <div>
      <div className="mb-5">
        <h1 className="text-[22px] font-bold tracking-tight">{mine ? 'Tiket Saya' : 'Semua Tiket'}</h1>
        <p className="text-[13.5px]" style={{ color: 'var(--text-muted)' }}>Kelola & pantau permintaan IT</p>
      </div>

      <div className="mb-4 flex flex-wrap items-center gap-2">
        <div className="relative">
          <Search size={15} className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2" style={{ color: 'var(--text-faint)' }} />
          <input
            className="input" placeholder="Cari judul / nomor…" style={{ paddingLeft: 32, minWidth: 220 }}
            value={search} onChange={(e) => { setSearch(e.target.value); setPage(1) }}
          />
        </div>
        <div className="flex gap-1 rounded-[10px] p-[3px]" style={{ background: 'var(--surface-2)', border: '1px solid var(--border)' }}>
          {STATUS_FILTERS.map((s) => (
            <button
              key={s || 'all'}
              onClick={() => { setStatus(s); setPage(1) }}
              className="rounded-md px-3 py-1.5 text-[12.5px] font-semibold"
              style={status === s ? { background: 'var(--surface)', color: 'var(--primary)', boxShadow: 'var(--shadow-sm)' } : { color: 'var(--text-muted)' }}
            >
              {s === '' ? 'Semua' : s === 'sedang_dikerjakan' ? 'Dikerjakan' : s[0].toUpperCase() + s.slice(1)}
            </button>
          ))}
        </div>
      </div>

      <div className="card overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-[13px]" style={{ borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ background: 'var(--surface-2)' }}>
                {['Nomor', 'Judul', 'Divisi', 'Prioritas', 'Status', 'SLA', 'Teknisi'].map((h) => (
                  <th key={h} className="px-3.5 py-3 text-left text-[11px] font-semibold uppercase tracking-wide" style={{ color: 'var(--text-muted)', borderBottom: '1px solid var(--border)' }}>
                    {h}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody>
              {isLoading ? (
                [0, 1, 2, 3, 4].map((i) => (
                  <tr key={i}><td colSpan={7} className="px-3.5 py-2"><Skeleton className="h-8 w-full" /></td></tr>
                ))
              ) : tickets.length === 0 ? (
                <tr><td colSpan={7}><EmptyState title="Tidak ada tiket" hint="Coba ubah filter atau kata kunci" /></td></tr>
              ) : (
                tickets.map((t) => (
                  <tr key={t.id} className="hover:[background:var(--surface-2)]">
                    <td className="px-3.5 py-3" style={cell}>
                      <Link to={`/tickets/${t.id}`} className="mono text-[11.5px]" style={{ color: 'var(--text-faint)' }}>{t.ticket_number}</Link>
                    </td>
                    <td className="px-3.5 py-3 font-semibold" style={{ ...cell, maxWidth: 260 }}>
                      <Link to={`/tickets/${t.id}`}>{t.title}</Link>
                    </td>
                    <td className="px-3.5 py-3" style={cell}>{t.division?.name}</td>
                    <td className="px-3.5 py-3" style={cell}><PriorityBadge priority={t.priority} /></td>
                    <td className="px-3.5 py-3" style={cell}><StatusBadge status={t.status} /></td>
                    <td className="px-3.5 py-3" style={cell}><SlaBadge indicator={t.sla_indicator} due={t.sla_due_at} /></td>
                    <td className="px-3.5 py-3" style={cell}>
                      {t.assignee ? (
                        <span className="flex items-center gap-2"><Avatar name={t.assignee.name} size={24} />{t.assignee.name}</span>
                      ) : (
                        <span style={{ color: 'var(--text-faint)' }}>—</span>
                      )}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {meta && meta.last_page > 1 && (
          <div className="flex items-center justify-between gap-3 px-4 py-3.5">
            <div className="text-[12.5px]" style={{ color: 'var(--text-muted)' }}>
              Halaman {meta.current_page} dari {meta.last_page} · {meta.total} tiket
            </div>
            <div className="flex gap-1.5">
              <button className="btn btn-sm" disabled={page <= 1} onClick={() => setPage((p) => p - 1)}>‹</button>
              <button className="btn btn-sm" disabled={page >= meta.last_page} onClick={() => setPage((p) => p + 1)}>›</button>
            </div>
          </div>
        )}
      </div>
    </div>
  )
}

const cell: React.CSSProperties = { borderBottom: '1px solid var(--border)', verticalAlign: 'middle' }
