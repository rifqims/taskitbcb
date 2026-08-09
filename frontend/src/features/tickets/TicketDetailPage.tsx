import { useState, type FormEvent } from 'react'
import { useParams } from 'react-router-dom'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Send } from 'lucide-react'
import { api } from '@/lib/api'
import { useAuth } from '@/lib/auth'
import { Avatar, PriorityBadge, SlaBadge, Spinner, StatusBadge, Skeleton } from '@/components/ui'
import { formatDateTime } from '@/lib/utils'
import type { Message, Paginated, Ticket } from '@/types'

const PROGRESS_STEPS = [0, 25, 50, 75, 100]

export function TicketDetailPage() {
  const { id } = useParams()
  const { user } = useAuth()
  const qc = useQueryClient()
  const [rejecting, setRejecting] = useState(false)

  const { data: ticket, isLoading } = useQuery({
    queryKey: ['ticket', id],
    queryFn: async () => (await api.get<{ data: Ticket }>(`/tickets/${id}`)).data.data,
  })

  const invalidate = () => {
    qc.invalidateQueries({ queryKey: ['ticket', id] })
    qc.invalidateQueries({ queryKey: ['tickets'] })
  }

  const assign = useMutation({ mutationFn: () => api.post(`/tickets/${id}/assign`), onSuccess: invalidate })
  const progress = useMutation({
    mutationFn: (p: number) => api.patch(`/tickets/${id}/progress`, { progress: p }),
    onSuccess: invalidate,
  })
  const resolve = useMutation({
    mutationFn: (payload: Record<string, string>) => api.post(`/tickets/${id}/resolve`, payload),
    onSuccess: () => { setRejecting(false); invalidate() },
  })

  if (isLoading || !ticket) {
    return <div className="flex flex-col gap-3"><Skeleton className="h-9 w-1/2" /><Skeleton className="h-64 w-full" /></div>
  }

  const isAssignee = ticket.assignee?.id === user?.id
  const canAct = (user?.role === 'admin' || isAssignee) && ['sedang_dikerjakan', 'pending'].includes(ticket.status)
  const canTake = (user?.role === 'it_support' || user?.role === 'admin') && ticket.status === 'baru' && !ticket.assignee

  return (
    <div>
      <div className="mb-4 flex flex-wrap items-start gap-3">
        <div>
          <div className="mono text-xs" style={{ color: 'var(--text-faint)' }}>{ticket.ticket_number}</div>
          <h1 className="mt-0.5 text-[19px] font-bold tracking-tight">{ticket.title}</h1>
        </div>
        <div className="ml-auto flex flex-wrap items-center gap-2">
          <PriorityBadge priority={ticket.priority} />
          <StatusBadge status={ticket.status} />
          <SlaBadge indicator={ticket.sla_indicator} due={ticket.sla_due_at} />
        </div>
      </div>

      <div className="grid items-start gap-4 lg:grid-cols-[1fr_320px]">
        <ChatPanel ticketId={ticket.id} description={ticket.description} />

        <aside className="flex flex-col gap-3.5">
          {/* Aksi */}
          <div className="card p-4">
            <h3 className="mb-3 text-xs font-semibold uppercase tracking-wide" style={{ color: 'var(--text-faint)' }}>Status &amp; Aksi</h3>
            {canTake && (
              <button className="btn btn-primary w-full" onClick={() => assign.mutate()} disabled={assign.isPending}>
                {assign.isPending ? <Spinner /> : 'Ambil Task'}
              </button>
            )}
            {canAct && !rejecting && (
              <div className="flex flex-col gap-2">
                <div className="text-xs font-medium" style={{ color: 'var(--text-muted)' }}>Ubah progress</div>
                <div className="flex gap-1.5">
                  {PROGRESS_STEPS.map((p) => (
                    <button
                      key={p} onClick={() => progress.mutate(p)} disabled={progress.isPending}
                      className="flex-1 rounded-lg py-1.5 text-xs font-bold"
                      style={ticket.progress === p
                        ? { background: 'var(--grad-strong)', color: '#fff' }
                        : { border: '1px solid var(--border)', background: 'var(--surface)' }}
                    >
                      {p}
                    </button>
                  ))}
                </div>
                <button className="btn mt-1 w-full" style={{ color: 'var(--low)', borderColor: 'color-mix(in srgb, var(--low) 30%, var(--border))' }}
                  onClick={() => resolve.mutate({ outcome: 'completed' })} disabled={resolve.isPending}>
                  ✓ Tandai Selesai
                </button>
                <button className="btn btn-danger w-full" onClick={() => setRejecting(true)}>✕ Tidak Bisa Dikerjakan</button>
              </div>
            )}
            {rejecting && <RejectForm onSubmit={(v) => resolve.mutate({ outcome: 'rejected', ...v })} onCancel={() => setRejecting(false)} pending={resolve.isPending} />}
            {!canTake && !canAct && !rejecting && (
              <p className="text-[13px]" style={{ color: 'var(--text-muted)' }}>
                {ticket.status === 'selesai' ? 'Tiket telah selesai.' : ticket.status === 'ditolak' ? 'Tiket ditolak.' : 'Menunggu teknisi mengambil tiket.'}
              </p>
            )}
          </div>

          {/* Progress bar */}
          <div className="card p-4">
            <div className="mb-2 flex justify-between text-xs" style={{ color: 'var(--text-muted)' }}>
              <span>Penyelesaian</span><b style={{ color: 'var(--text)', fontSize: 14 }}>{ticket.progress}%</b>
            </div>
            <div className="h-2 overflow-hidden rounded-full" style={{ background: 'var(--surface-2)' }}>
              <div className="h-full rounded-full" style={{ width: `${ticket.progress}%`, background: 'var(--grad)' }} />
            </div>
          </div>

          {/* Detail */}
          <div className="card p-4">
            <h3 className="mb-3 text-xs font-semibold uppercase tracking-wide" style={{ color: 'var(--text-faint)' }}>Detail Tiket</h3>
            <dl className="flex flex-col gap-3 text-[13px]">
              <Row label="Divisi" value={ticket.division?.name} />
              <Row label="Kategori" value={ticket.category?.name} />
              <Row label="Pengirim" value={ticket.sender_name} />
              <Row label="Dibuat" value={formatDateTime(ticket.created_at)} />
              <Row label="Deadline SLA" value={formatDateTime(ticket.sla_due_at)} />
              <Row label="Teknisi" value={ticket.assignee?.name ?? '—'} />
            </dl>
          </div>
        </aside>
      </div>
    </div>
  )
}

function ChatPanel({ ticketId, description }: { ticketId: number; description: string }) {
  const { user } = useAuth()
  const qc = useQueryClient()
  const [body, setBody] = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['messages', ticketId],
    queryFn: async () => (await api.get<Paginated<Message>>(`/tickets/${ticketId}/messages`)).data.data,
    refetchInterval: 10000,
  })

  const send = useMutation({
    mutationFn: (text: string) => api.post(`/tickets/${ticketId}/messages`, { body: text }),
    onSuccess: () => { setBody(''); qc.invalidateQueries({ queryKey: ['messages', ticketId] }) },
  })

  function onSend(e: FormEvent) {
    e.preventDefault()
    if (body.trim()) send.mutate(body.trim())
  }

  return (
    <div className="card flex flex-col">
      <div className="border-b px-4 py-3 text-[13px]" style={{ borderColor: 'var(--border)', color: 'var(--text-muted)' }}>
        {description}
      </div>
      <div className="flex max-h-[440px] flex-col gap-3.5 overflow-y-auto p-4">
        {isLoading ? (
          <Skeleton className="h-16 w-2/3" />
        ) : (data ?? []).length === 0 ? (
          <p className="py-8 text-center text-[13px]" style={{ color: 'var(--text-faint)' }}>Belum ada percakapan. Mulai berdiskusi.</p>
        ) : (
          data!.map((m) => {
            const mine = m.author?.id === user?.id
            return (
              <div key={m.id} className={`flex max-w-[82%] gap-2.5 ${mine ? 'ml-auto flex-row-reverse' : ''}`}>
                <Avatar name={m.author?.name ?? '?'} size={26} />
                <div>
                  {!mine && <div className="mb-1 text-[11.5px] font-semibold" style={{ color: 'var(--text-muted)' }}>{m.author?.name}</div>}
                  <div
                    className="rounded-[14px] px-3 py-2 text-[13.5px]"
                    style={mine
                      ? { background: 'var(--primary)', color: 'var(--on-primary)', borderTopRightRadius: 4 }
                      : { background: 'var(--surface-2)', border: '1px solid var(--border)', borderTopLeftRadius: 4 }}
                  >
                    {m.body}
                  </div>
                </div>
              </div>
            )
          })
        )}
      </div>
      <form onSubmit={onSend} className="flex items-center gap-2.5 border-t p-3" style={{ borderColor: 'var(--border)' }}>
        <input className="input" placeholder="Tulis pesan…" value={body} onChange={(e) => setBody(e.target.value)} />
        <button type="submit" className="btn btn-primary" style={{ padding: '9px 12px' }} disabled={send.isPending || !body.trim()}>
          {send.isPending ? <Spinner /> : <Send size={16} />}
        </button>
      </form>
    </div>
  )
}

function RejectForm({ onSubmit, onCancel, pending }: {
  onSubmit: (v: Record<string, string>) => void
  onCancel: () => void
  pending: boolean
}) {
  const [v, setV] = useState({ reason: '', berita_acara: '', recommendation: '', notes: '' })
  const set = (k: string) => (e: React.ChangeEvent<HTMLTextAreaElement>) => setV({ ...v, [k]: e.target.value })
  const fields: [string, string][] = [['reason', 'Alasan'], ['berita_acara', 'Berita Acara'], ['recommendation', 'Rekomendasi'], ['notes', 'Catatan (opsional)']]

  return (
    <div className="flex flex-col gap-2.5">
      {fields.map(([k, label]) => (
        <div key={k}>
          <label className="label">{label}</label>
          <textarea className="input" rows={2} value={v[k as keyof typeof v]} onChange={set(k)} />
        </div>
      ))}
      <div className="flex gap-2">
        <button className="btn flex-1" onClick={onCancel}>Batal</button>
        <button className="btn btn-danger flex-1" disabled={pending || !v.reason || !v.berita_acara || !v.recommendation} onClick={() => onSubmit(v)}>
          {pending ? <Spinner /> : 'Kirim'}
        </button>
      </div>
    </div>
  )
}

function Row({ label, value }: { label: string; value?: string }) {
  return (
    <div className="flex items-center justify-between gap-2">
      <span style={{ color: 'var(--text-muted)' }}>{label}</span>
      <span className="text-right font-semibold">{value ?? '—'}</span>
    </div>
  )
}
