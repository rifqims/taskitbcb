export type Role = 'admin' | 'it_support' | 'client'
export type Priority = 'low' | 'medium' | 'high' | 'urgent'
export type Status = 'baru' | 'sedang_dikerjakan' | 'pending' | 'selesai' | 'ditolak'
export type SlaIndicator = 'green' | 'yellow' | 'red'

export interface User {
  id: number
  name: string
  email: string
  role: Role
  role_label: string
  division_id: number | null
  phone: string | null
  job_title: string | null
  avatar_path: string | null
  is_active: boolean
}

export interface Ticket {
  id: number
  ticket_number: string
  title: string
  description: string
  priority: Priority
  priority_label: string
  priority_weight: number
  status: Status
  status_label: string
  progress: number
  location: string | null
  sender_name: string
  division?: { id: number; name: string }
  category?: { id: number; name: string; icon: string }
  creator?: { id: number; name: string }
  assignee?: { id: number; name: string } | null
  sla_due_at: string | null
  sla_indicator: SlaIndicator
  is_overdue: boolean
  deadline: string | null
  started_at: string | null
  completed_at: string | null
  created_at: string
  resolution?: Resolution | null
  activities?: Activity[]
}

export interface Resolution {
  outcome: 'completed' | 'rejected'
  reason: string | null
  berita_acara: string | null
  recommendation: string | null
  notes: string | null
  resolved_at: string | null
}

export interface Activity {
  id: number
  action: string
  meta: Record<string, unknown> | null
  user?: { id: number; name: string } | null
  created_at: string
}

export interface Message {
  id: number
  body: string
  author?: { id: number; name: string; role: Role }
  is_mine: boolean
  attachments?: { id: number; original_name: string; mime_type: string; size: number }[]
  mentions?: number[]
  created_at: string
}

export interface AppNotification {
  id: number
  type: string
  ticket_id: number | null
  data: Record<string, string> | null
  is_read: boolean
  created_at: string
}

export interface Paginated<T> {
  data: T[]
  meta?: { current_page: number; last_page: number; per_page: number; total: number }
}

export interface Division { id: number; name: string }
export interface Category { id: number; name: string; icon: string }
