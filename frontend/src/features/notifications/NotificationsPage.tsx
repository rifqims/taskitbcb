import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useNavigate } from 'react-router-dom'
import { api } from '@/lib/api'
import { useAuth } from '@/lib/auth'
import { EmptyState, PriorityBadge, Skeleton } from '@/components/ui'
import { timeAgo } from '@/lib/utils'
import type { AppNotification, Paginated, Priority } from '@/types'

const LABELS: Record<string, string> = {
  ticket_created: 'Tiket baru masuk',
  ticket_accepted: 'Tiket Anda diterima teknisi',
  ticket_completed: 'Tiket Anda telah selesai',
  ticket_rejected: 'Tiket Anda ditolak',
  chat_reply: 'Ada balasan chat baru',
  mention: 'Anda disebut dalam percakapan',
}

export function NotificationsPage() {
  const qc = useQueryClient()
  const navigate = useNavigate()
  const { user } = useAuth()
  const isTech = user?.role === 'it_support' || user?.role === 'admin'

  const { data, isLoading } = useQuery({
    queryKey: ['notifications'],
    queryFn: async () => (await api.get<Paginated<AppNotification>>('/notifications')).data.data,
  })

  const invalidate = () => {
    qc.invalidateQueries({ queryKey: ['notifications'] })
    qc.invalidateQueries({ queryKey: ['unread'] })
    qc.invalidateQueries({ queryKey: ['tickets'] })
  }
  const readAll = useMutation({ mutationFn: () => api.post('/notifications/read-all'), onSuccess: invalidate })
  const readOne = useMutation({ mutationFn: (id: number) => api.post(`/notifications/${id}/read`), onSuccess: invalidate })
  const takeTicket = useMutation({
    mutationFn: (ticketId: number) => api.post(`/tickets/${ticketId}/assign`),
    onSuccess: (_res, ticketId) => { invalidate(); navigate(`/tickets/${ticketId}`) },
    onError: (_e, ticketId) => navigate(`/tickets/${ticketId}`), // sudah diambil orang lain → tetap buka detail
  })

  function openDetail(n: AppNotification) {
    if (!n.is_read) readOne.mutate(n.id)
    if (n.ticket_id) navigate(`/tickets/${n.ticket_id}`)
  }

  return (
    <div className="mx-auto max-w-[720px]">
      <div className="mb-5 flex items-center justify-between">
        <h1 className="text-[22px] font-bold tracking-tight">Notifikasi</h1>
        <button className="btn btn-sm" onClick={() => readAll.mutate()}>Tandai semua dibaca</button>
      </div>

      <div className="card overflow-hidden">
        {isLoading ? (
          <div className="flex flex-col gap-2 p-3">{[0, 1, 2].map((i) => <Skeleton key={i} className="h-16 w-full" />)}</div>
        ) : (data ?? []).length === 0 ? (
          <EmptyState title="Belum ada notifikasi" />
        ) : (
          data!.map((n) => {
            const isNewTicket = n.type === 'ticket_created'
            return (
              <div
                key={n.id}
                className="flex flex-wrap items-center gap-3 px-4 py-3.5"
                style={{ borderBottom: '1px solid var(--border)', background: n.is_read ? undefined : 'var(--primary-soft)' }}
              >
                {!n.is_read && <span className="h-2 w-2 shrink-0 rounded-full" style={{ background: 'var(--primary)' }} />}

                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2">
                    <span className="text-[13.5px] font-semibold">{LABELS[n.type] ?? n.type}</span>
                    {isNewTicket && n.data?.priority && <PriorityBadge priority={n.data.priority as Priority} />}
                  </div>
                  <div className="mt-0.5 text-xs" style={{ color: 'var(--text-muted)' }}>
                    {n.data?.ticket_number ? `${n.data.ticket_number} · ` : ''}
                    {isNewTicket && n.data?.title ? `${n.data.title} · ` : ''}
                    {timeAgo(n.created_at)}
                  </div>
                </div>

                {/* Aksi */}
                <div className="flex items-center gap-2">
                  {isNewTicket && isTech && n.ticket_id && (
                    <button
                      className="btn btn-primary btn-sm"
                      disabled={takeTicket.isPending}
                      onClick={() => { if (!n.is_read) readOne.mutate(n.id); takeTicket.mutate(n.ticket_id!) }}
                    >
                      Ambil
                    </button>
                  )}
                  {n.ticket_id && (
                    <button className="btn btn-sm" onClick={() => openDetail(n)}>Lihat Detail</button>
                  )}
                </div>
              </div>
            )
          })
        )}
      </div>
    </div>
  )
}
