import { useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { Moon } from 'lucide-react'
import { useAuth } from '@/lib/auth'
import { toggleTheme } from '@/lib/theme'
import { Spinner } from '@/components/ui'
import { AxiosError } from 'axios'

export function LoginPage() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [email, setEmail] = useState('admin@gawe-qi.test')
  const [password, setPassword] = useState('password')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  async function onSubmit(e: FormEvent) {
    e.preventDefault()
    setError('')
    setLoading(true)
    try {
      await login(email, password)
      navigate('/')
    } catch (err) {
      const msg =
        err instanceof AxiosError
          ? err.response?.data?.errors?.email?.[0] ?? err.response?.data?.message
          : null
      setError(msg ?? 'Gagal masuk. Coba lagi.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="grid min-h-screen place-items-center px-4">
      <button
        onClick={toggleTheme}
        className="fixed right-5 top-5 grid h-9 w-9 place-items-center rounded-[10px]"
        style={{ border: '1px solid var(--border)', background: 'var(--surface)', color: 'var(--text-muted)' }}
        title="Ganti tema"
      >
        <Moon size={17} />
      </button>

      <div className="card w-full max-w-[380px] p-8">
        <div className="mb-6 flex flex-col items-center gap-3 text-center">
          <div
            className="grid h-12 w-12 place-items-center rounded-xl text-2xl font-extrabold text-white"
            style={{ background: 'var(--grad)' }}
          >
            G
          </div>
          <div>
            <div className="text-lg font-bold tracking-tight">
              Gawe<span style={{ color: 'var(--primary)' }}>-Qi</span>
            </div>
            <div className="text-[13px]" style={{ color: 'var(--text-muted)' }}>
              Task Management &amp; Ticketing IT
            </div>
          </div>
        </div>

        <form onSubmit={onSubmit} className="flex flex-col gap-4">
          <div>
            <label className="label" htmlFor="email">Email</label>
            <input
              id="email" type="email" className="input" value={email} autoComplete="username"
              onChange={(e) => setEmail(e.target.value)} required
            />
          </div>
          <div>
            <label className="label" htmlFor="password">Password</label>
            <input
              id="password" type="password" className="input" value={password} autoComplete="current-password"
              onChange={(e) => setPassword(e.target.value)} required
            />
          </div>

          {error && (
            <div className="rounded-lg px-3 py-2 text-[13px]" style={{ background: 'var(--urgent-soft)', color: 'var(--urgent)' }}>
              {error}
            </div>
          )}

          <button type="submit" className="btn btn-primary" disabled={loading}>
            {loading ? <Spinner /> : 'Masuk'}
          </button>
        </form>

        <p className="mt-5 text-center text-[11.5px]" style={{ color: 'var(--text-faint)' }}>
          Demo: admin@gawe-qi.test / password
        </p>
      </div>
    </div>
  )
}
