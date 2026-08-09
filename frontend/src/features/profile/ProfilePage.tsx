import { useAuth } from '@/lib/auth'
import { Avatar } from '@/components/ui'

export function ProfilePage() {
  const { user, logout } = useAuth()
  if (!user) return null

  const rows: [string, string][] = [
    ['Nama', user.name],
    ['Email', user.email],
    ['Peran', user.role_label],
    ['Jabatan', user.job_title ?? '—'],
    ['Telepon', user.phone ?? '—'],
  ]

  return (
    <div className="mx-auto max-w-[560px]">
      <h1 className="mb-5 text-[22px] font-bold tracking-tight">Profil Akun</h1>

      <div className="card p-6">
        <div className="flex items-center gap-4">
          <Avatar name={user.name} size={64} />
          <div>
            <div className="text-lg font-bold">{user.name}</div>
            <span className="chip" style={{ background: 'var(--primary-soft)', color: 'var(--primary)' }}>{user.role_label}</span>
          </div>
        </div>

        <dl className="mt-6 flex flex-col divide-y" style={{ borderColor: 'var(--border)' }}>
          {rows.map(([k, v]) => (
            <div key={k} className="flex justify-between py-3 text-[13.5px]" style={{ borderColor: 'var(--border)' }}>
              <span style={{ color: 'var(--text-muted)' }}>{k}</span>
              <span className="font-semibold">{v}</span>
            </div>
          ))}
        </dl>

        <button className="btn btn-danger mt-6 w-full" onClick={logout}>Keluar dari Akun</button>
      </div>
    </div>
  )
}
