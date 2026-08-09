import { useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { AxiosError } from 'axios'
import { api } from '@/lib/api'
import { useAuth } from '@/lib/auth'
import { Spinner } from '@/components/ui'
import type { Category, Division, Priority } from '@/types'

const PRIORITIES: { value: Priority; label: string }[] = [
  { value: 'low', label: '🟢 Low' },
  { value: 'medium', label: '🟡 Medium' },
  { value: 'high', label: '🟠 High' },
  { value: 'urgent', label: '🔴 Urgent' },
]

export function CreateTicketPage() {
  const { user } = useAuth()
  const navigate = useNavigate()
  const qc = useQueryClient()
  const [error, setError] = useState('')

  const { data: refs } = useQuery({
    queryKey: ['references'],
    queryFn: async () => (await api.get<{ data: { divisions: Division[]; categories: Category[] } }>('/references')).data.data,
  })

  const [form, setForm] = useState({
    title: '', description: '', division_id: '', sender_name: user?.name ?? '',
    category_id: '', priority: 'medium' as Priority, location: '',
  })
  const set = (k: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) =>
    setForm({ ...form, [k]: e.target.value })

  const create = useMutation({
    mutationFn: () => api.post('/tickets', form),
    onSuccess: (res) => {
      qc.invalidateQueries({ queryKey: ['tickets'] })
      navigate(`/tickets/${res.data.data.id}`)
    },
    onError: (err) => {
      setError(err instanceof AxiosError ? err.response?.data?.message ?? 'Gagal membuat tiket' : 'Gagal membuat tiket')
    },
  })

  function onSubmit(e: FormEvent) {
    e.preventDefault()
    setError('')
    create.mutate()
  }

  return (
    <div className="mx-auto max-w-[720px]">
      <div className="mb-5">
        <h1 className="text-[22px] font-bold tracking-tight">Buat Tiket Baru</h1>
        <p className="text-[13.5px]" style={{ color: 'var(--text-muted)' }}>Jelaskan kebutuhan Anda agar tim IT dapat membantu</p>
      </div>

      <form onSubmit={onSubmit} className="card flex flex-col gap-4 p-6">
        <div>
          <label className="label">Judul</label>
          <input className="input" required value={form.title} onChange={set('title')} placeholder="mis. Printer lantai 3 tidak bisa cetak" />
        </div>
        <div>
          <label className="label">Deskripsi</label>
          <textarea className="input" required rows={4} value={form.description} onChange={set('description')} placeholder="Jelaskan detail masalah…" />
        </div>
        <div className="grid gap-4 sm:grid-cols-2">
          <div>
            <label className="label">Divisi</label>
            <select className="input" required value={form.division_id} onChange={set('division_id')}>
              <option value="">Pilih divisi…</option>
              {refs?.divisions.map((d) => <option key={d.id} value={d.id}>{d.name}</option>)}
            </select>
          </div>
          <div>
            <label className="label">Kategori</label>
            <select className="input" required value={form.category_id} onChange={set('category_id')}>
              <option value="">Pilih kategori…</option>
              {refs?.categories.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
            </select>
          </div>
          <div>
            <label className="label">Nama Pengirim</label>
            <input className="input" required value={form.sender_name} onChange={set('sender_name')} />
          </div>
          <div>
            <label className="label">Prioritas</label>
            <select className="input" value={form.priority} onChange={set('priority')}>
              {PRIORITIES.map((p) => <option key={p.value} value={p.value}>{p.label}</option>)}
            </select>
          </div>
          <div className="sm:col-span-2">
            <label className="label">Lokasi (opsional)</label>
            <input className="input" value={form.location} onChange={set('location')} placeholder="mis. Gedung A lantai 3" />
          </div>
        </div>

        {error && (
          <div className="rounded-lg px-3 py-2 text-[13px]" style={{ background: 'var(--urgent-soft)', color: 'var(--urgent)' }}>{error}</div>
        )}

        <div className="flex justify-end gap-2">
          <button type="button" className="btn" onClick={() => navigate(-1)}>Batal</button>
          <button type="submit" className="btn btn-primary" disabled={create.isPending}>
            {create.isPending ? <Spinner /> : 'Kirim Tiket'}
          </button>
        </div>
      </form>
    </div>
  )
}
