import { cn, initials, priorityLabels, slaRemaining, statusLabels } from '@/lib/utils'
import type { Priority, SlaIndicator, Status } from '@/types'

export function PriorityBadge({ priority }: { priority: Priority }) {
  return (
    <span className={cn('chip', `p-${priority}`)}>
      <span className="dot" />
      {priorityLabels[priority]}
    </span>
  )
}

export function StatusBadge({ status }: { status: Status }) {
  return <span className={cn('chip', `s-${status}`)}>{statusLabels[status]}</span>
}

export function SlaBadge({ indicator, due }: { indicator: SlaIndicator; due: string | null }) {
  return (
    <span className={cn('inline-flex items-center gap-1.5 text-xs font-semibold', `sla-${indicator}`)}>
      <span className="inline-block h-2.5 w-2.5 rounded-full" style={{ background: 'currentColor' }} />
      {slaRemaining(due)}
    </span>
  )
}

export function Avatar({ name, size = 32 }: { name: string; size?: number }) {
  return (
    <span
      className="grid shrink-0 place-items-center rounded-full font-bold text-white"
      style={{ width: size, height: size, fontSize: size * 0.36, background: 'var(--grad-strong)' }}
    >
      {initials(name)}
    </span>
  )
}

export function Spinner() {
  return (
    <span
      className="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"
      role="status"
      aria-label="memuat"
    />
  )
}

export function Skeleton({ className }: { className?: string }) {
  return <div className={cn('skeleton', className)} />
}

export function EmptyState({ title, hint }: { title: string; hint?: string }) {
  return (
    <div className="flex flex-col items-center justify-center gap-1 py-16 text-center">
      <div className="text-sm font-semibold" style={{ color: 'var(--text-muted)' }}>{title}</div>
      {hint && <div className="text-xs" style={{ color: 'var(--text-faint)' }}>{hint}</div>}
    </div>
  )
}
