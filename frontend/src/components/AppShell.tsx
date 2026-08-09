import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  LayoutDashboard, Ticket, KanbanSquare, BarChart3, ShieldCheck, Bell, Moon, LogOut, Plus,
} from 'lucide-react'
import { useAuth } from '@/lib/auth'
import { api } from '@/lib/api'
import { toggleTheme } from '@/lib/theme'
import { Avatar } from './ui'
import type { Role } from '@/types'

interface NavItem { to: string; label: string; icon: typeof Ticket; roles: Role[] }

const NAV: NavItem[] = [
  { to: '/', label: 'Dashboard', icon: LayoutDashboard, roles: ['admin', 'it_support', 'client'] },
  { to: '/tickets', label: 'Semua Tiket', icon: Ticket, roles: ['admin', 'it_support'] },
  { to: '/tickets?mine=1', label: 'Tiket Saya', icon: KanbanSquare, roles: ['it_support'] },
  { to: '/my-tickets', label: 'Tiket Saya', icon: Ticket, roles: ['client'] },
  { to: '/reports', label: 'Laporan', icon: BarChart3, roles: ['admin', 'it_support'] },
  { to: '/audit', label: 'Audit Log', icon: ShieldCheck, roles: ['admin'] },
]

export function AppShell() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const role = user?.role ?? 'client'

  const { data: unread } = useQuery({
    queryKey: ['unread'],
    queryFn: async () => (await api.get('/notifications/unread-count')).data.data.count as number,
    refetchInterval: 30000,
  })

  const items = NAV.filter((i) => i.roles.includes(role))

  return (
    <div className="grid min-h-screen" style={{ gridTemplateColumns: '242px 1fr' }}>
      {/* Sidebar */}
      <aside
        className="sticky top-0 flex h-screen flex-col gap-1 p-3"
        style={{ background: 'var(--surface)', borderRight: '1px solid var(--border)' }}
      >
        <div className="flex items-center gap-2.5 px-2.5 py-3 pb-4">
          <div
            className="grid h-9 w-9 place-items-center rounded-[10px] text-lg font-extrabold text-white"
            style={{ background: 'var(--grad)' }}
          >
            G
          </div>
          <div className="text-base font-bold tracking-tight">
            Gawe<span style={{ color: 'var(--primary)' }}>-Qi</span>
          </div>
        </div>

        <nav className="flex flex-col gap-0.5">
          {items.map((item) => (
            <NavLink
              key={item.to}
              to={item.to}
              end={item.to === '/'}
              className="nav-link flex items-center gap-3 rounded-[9px] px-3 py-2 text-[13.5px] font-medium"
            >
              <item.icon size={18} />
              {item.label}
            </NavLink>
          ))}
        </nav>

        <button
          onClick={() => navigate('/profile')}
          className="mt-auto flex items-center gap-2.5 rounded-[10px] p-2 text-left"
          style={{ borderTop: '1px solid var(--border)' }}
        >
          <Avatar name={user?.name ?? '?'} size={32} />
          <div className="min-w-0">
            <div className="truncate text-[12.5px] font-semibold">{user?.name}</div>
            <div className="text-[11px]" style={{ color: 'var(--text-faint)' }}>{user?.role_label}</div>
          </div>
        </button>
      </aside>

      {/* Main */}
      <div className="flex min-w-0 flex-col">
        <header
          className="sticky top-0 z-20 flex h-[60px] items-center gap-3 px-6 backdrop-blur"
          style={{ background: 'color-mix(in srgb, var(--surface) 82%, transparent)', borderBottom: '1px solid var(--border)' }}
        >
          <div className="text-[15px] font-bold tracking-tight">
            {role === 'client' ? 'Portal Client' : role === 'admin' ? 'Control Panel' : 'Workspace IT'}
          </div>
          <div className="ml-auto flex items-center gap-2">
            <IconBtn onClick={toggleTheme} title="Ganti tema"><Moon size={17} /></IconBtn>
            <button
              onClick={() => navigate('/notifications')}
              className="relative grid h-9 w-9 place-items-center rounded-[10px]"
              style={{ border: '1px solid var(--border)', background: 'var(--surface)' }}
              title="Notifikasi"
            >
              <Bell size={17} />
              {!!unread && (
                <span
                  className="absolute -right-1 -top-1 grid h-[18px] min-w-[18px] place-items-center rounded-full px-1 text-[10px] font-bold text-white"
                  style={{ background: 'var(--urgent)', border: '2px solid var(--surface)' }}
                >
                  {unread}
                </span>
              )}
            </button>
            {role === 'client' && (
              <button className="btn btn-primary btn-sm" onClick={() => navigate('/tickets/new')}>
                <Plus size={15} /> Buat Tiket
              </button>
            )}
            <IconBtn onClick={logout} title="Keluar"><LogOut size={17} /></IconBtn>
          </div>
        </header>

        <main className="mx-auto w-full max-w-[1180px] flex-1 px-6 pb-16 pt-6">
          <Outlet />
        </main>
      </div>
    </div>
  )
}

function IconBtn({ children, onClick, title }: { children: React.ReactNode; onClick: () => void; title: string }) {
  return (
    <button
      onClick={onClick}
      title={title}
      className="grid h-9 w-9 place-items-center rounded-[10px]"
      style={{ border: '1px solid var(--border)', background: 'var(--surface)', color: 'var(--text-muted)' }}
    >
      {children}
    </button>
  )
}
