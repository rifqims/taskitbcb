# Gawe-Qi — Frontend (React + Vite + TypeScript)

SPA untuk sistem Task Management & Ticketing IT **Gawe-Qi**. Mengonsumsi REST API `backend/`.

## Stack
- **React + Vite + TypeScript**
- **Tailwind CSS v4** (design tokens gradasi biru Gawe-Qi, light/dark)
- **React Router** (routing + guard RBAC), **TanStack Query** (data + cache + polling)
- **Axios** (klien API + token interceptor), **Lucide** (ikon)

## Setup

```bash
cd frontend
npm install
cp .env.example .env      # opsional; default proxy /api -> http://localhost:8000
npm run dev               # http://localhost:5173
```

Pastikan backend berjalan di `http://localhost:8000` (`php artisan serve`). Vite mem-proxy
`/api` ke backend saat development (lihat `vite.config.ts`).

Login demo: `admin@gawe-qi.test` / `password` (atau akun lain dari seeder).

## Struktur (feature-based)
```
src/
├─ lib/            api (axios), auth (context), theme, utils
├─ components/     AppShell (sidebar+topbar role-based), ProtectedRoute, ui
├─ features/
│  ├─ auth/        LoginPage
│  ├─ dashboard/   DashboardPage (stat cards + antrian)
│  ├─ tickets/     TicketsPage (tabel+filter), TicketDetailPage (chat+aksi), CreateTicketPage
│  ├─ notifications/ NotificationsPage
│  ├─ reports/     ReportsPage (grafik + export CSV)
│  └─ profile/     ProfilePage
├─ types.ts        tipe domain (User, Ticket, Message, ...)
├─ App.tsx         definisi route
└─ main.tsx        providers (QueryClient, theme)
```

## Fitur
- Login token Sanctum + guard RBAC (menu & route menyesuaikan role).
- Dashboard ringkas, daftar tiket (filter/search/pagination), detail tiket dengan
  **chat realtime (polling)** dan aksi (Ambil / Progress / Selesai / Tolak).
- Notifikasi in-app dengan badge belum-dibaca, laporan + export CSV.
- Dark/Light mode, skeleton loading, empty state.
