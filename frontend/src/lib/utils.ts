import type { Priority, Status } from '@/types'

/** Gabung className bersyarat. */
export function cn(...parts: (string | false | null | undefined)[]): string {
  return parts.filter(Boolean).join(' ')
}

export const priorityLabels: Record<Priority, string> = {
  low: 'Low', medium: 'Medium', high: 'High', urgent: 'Urgent',
}
export const statusLabels: Record<Status, string> = {
  baru: 'Baru',
  sedang_dikerjakan: 'Sedang Dikerjakan',
  pending: 'Pending',
  selesai: 'Selesai',
  ditolak: 'Ditolak',
}

/** Waktu relatif sederhana dalam Bahasa Indonesia. */
export function timeAgo(iso: string): string {
  const diff = Date.now() - new Date(iso).getTime()
  const m = Math.floor(diff / 60000)
  if (m < 1) return 'baru saja'
  if (m < 60) return `${m} menit lalu`
  const h = Math.floor(m / 60)
  if (h < 24) return `${h} jam lalu`
  const d = Math.floor(h / 24)
  return `${d} hari lalu`
}

export function formatDateTime(iso: string | null): string {
  if (!iso) return '-'
  return new Date(iso).toLocaleString('id-ID', {
    day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit',
  })
}

export function initials(name: string): string {
  return name.split(' ').slice(0, 2).map((w) => w[0]).join('').toUpperCase()
}

/** Sisa waktu SLA yang mudah dibaca. */
export function slaRemaining(due: string | null): string {
  if (!due) return '-'
  const diff = new Date(due).getTime() - Date.now()
  const abs = Math.abs(diff)
  const h = Math.floor(abs / 3600000)
  const m = Math.floor((abs % 3600000) / 60000)
  const label = h >= 24 ? `${Math.floor(h / 24)}h ${h % 24}j` : `${h}j ${m}m`
  return diff < 0 ? `Lewat ${label}` : `${label} tersisa`
}
