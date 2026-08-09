import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import { api } from '@/lib/api'
import { EmptyState, Skeleton } from '@/components/ui'
import { timeAgo } from '@/lib/utils'
import type { AppNotification, Paginated } from '@/types'

const LABELS: Record<string, string> = {
  ticket_accepted: 'Tiket Anda diterima teknisi',
  ticket_completed: 'Tiket Anda telah selesai',
  ticket_rejected: 'Tiket Anda ditolak',
  chat_reply: 'Ada balasan chat baru',
  mention: 'Anda disebut dalam percakapan',
}

export function NotificationsPage() {
  const qc = useQueryClient()
  const { data, isLoading } = useQuery({
    queryKey: ['notifications'],
    queryFn: async () => (await api.get<Paginated<AppNotification>>('/notifications')).data.data,
  })

  const invalidate = () => {
    qc.invalidateQueries({ queryKey: ['notifications'] })
    qc.invalidateQueries({ queryKey: ['unread'] })
  }
  const readAll = useMutation({ mutationFn: () => api.post('/notifications/read-all'), onSuccess: invalidate })
  const readOne = useMutation({ mutationFn: (id: number) => api.post(`/notifications/${id}/read`), onSuccess: invalidate })

  return (
    <div className="mx-auto max-w-[720px]">
      <div className="mb-5 flex items-center justify-between">
        <h1 className="text-[22px] font-bold tracking-tight">Notifikasi</h1>
        <button className="btn btn-sm" onClick={() => readAll.mutate()}>Tandai semua dibaca</button>
      </div>

      <div className="card divide-y" style={{ borderColor: 'var(--border)' }}>
        {isLoading ? (
          <div className="flex flex-col gap-2 p-3">{[0, 1, 2].map((i) => <Skeleton key={i} className="h-12 w-full" />)}</div>
        ) : (data ?? []).length === 0 ? (
          <EmptyState title="Belum ada notifikasi" />
        ) : (
          data!.map((n) => {
            const inner = (
              <div
                className="flex items-center gap-3 px-4 py-3.5"
                style={{ borderBottom: '1px solid var(--border)', background: n.is_read ? undefined : 'var(--primary-soft)' }}
              >
                {!n.is_read && <span className="h-2 w-2 shrink-0 rounded-full" style={{ background: 'var(--primary)' }} />}
                <div className="min-w-0 flex-1">
                  <div className="text-[13.5px] font-semibold">{LABELS[n.type] ?? n.type}</div>
                  <div className="text-xs" style={{ color: 'var(--text-muted)' }}>
                    {n.data?.ticket_number ? `${n.data.ticket_number} · ` : ''}{timeAgo(n.created_at)}
                  </div>
                </div>
              </div>
            )
            return n.ticket_id ? (
              <Link key={n.id} to={`/tickets/${n.ticket_id}`} onClick={() => !n.is_read && readOne.mutate(n.id)}>{inner}</Link>
            ) : (
              <div key={n.id} onClick={() => !n.is_read && readOne.mutate(n.id)}>{inner}</div>
            )
          })
        )}
      </div>
    </div>
  )
}
