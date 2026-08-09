# Fase 6 — Panduan Deployment (Gratis, dari Nol ke Publik)

> **Tujuan:** Menerbitkan Gawe-Qi agar dapat diakses publik memakai layanan gratis.
> Tiga komponen di-deploy terpisah: **Database (Neon)**, **Backend (Railway/Render)**,
> **Frontend (Cloudflare Pages/Vercel)**. Batas layanan gratis bisa berubah — cek dokumentasi
> masing-masing saat mengerjakan.

## Arsitektur target
```
[Cloudflare Pages]  --HTTPS-->  [Railway: Laravel API]  -->  [Neon: PostgreSQL]
   frontend (SPA)                 backend (REST)               database
```

---

## 1. Database — Neon (PostgreSQL, gratis)
1. Daftar di https://neon.tech → **New Project** → pilih region terdekat (mis. Singapore).
2. Salin **connection string** (format `postgresql://user:pass@host/dbname?sslmode=require`).
3. Catat host, database, user, password — dipakai env backend.

## 2. Backend — Railway (atau Render/Fly.io)
1. Push repo ke GitHub (branch ini sudah siap).
2. Di https://railway.app → **New Project → Deploy from GitHub repo** → pilih `taskitbcb`.
3. Set **Root Directory = `backend`**. Railway mendeteksi PHP (Nixpacks). Jika perlu, tambah
   file `backend/nixpacks.toml` untuk memaksa PHP 8.3 + ekstensi `pdo_pgsql`.
4. **Environment variables** (Settings → Variables):
   ```
   APP_NAME=Gawe-Qi
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=            # generate: php artisan key:generate --show
   APP_URL=https://<backend-domain>
   FRONTEND_URL=https://<frontend-domain>
   DB_CONNECTION=pgsql
   DB_HOST=<neon-host>
   DB_PORT=5432
   DB_DATABASE=<neon-db>
   DB_USERNAME=<neon-user>
   DB_PASSWORD=<neon-pass>
   DB_SSLMODE=require
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```
5. **Start / release command:**
   ```
   php artisan migrate --force --seed && php artisan serve --host 0.0.0.0 --port $PORT
   ```
   (Untuk produksi sungguhan gunakan `php-fpm`/nginx atau `frankenphp`; `artisan serve` cukup untuk MVP.)
6. Deploy → catat domain backend (mis. `https://gaweqi-api.up.railway.app`).

## 3. CORS & Sanctum
Frontend beda domain, jadi izinkan origin frontend di backend:
- `config/cors.php`: pastikan `paths` memuat `api/*` dan `allowed_origins` = `[env('FRONTEND_URL')]`.
- Karena memakai **token Bearer** (bukan cookie), cukup CORS; tak perlu domain stateful Sanctum.
- Set `APP_URL` & `FRONTEND_URL` sesuai domain nyata.

## 4. Penyimpanan File
- MVP: disk `local` (ephemeral di Railway — hilang saat redeploy). Untuk permanen gunakan
  **Supabase Storage** atau **Cloudinary**:
  - Tambah paket S3 (`league/flysystem-aws-s3-v3`) & set disk `s3` ke endpoint Supabase.
  - Ganti `->store(..., 'local')` menjadi disk cloud via `config('filesystems.default')`.

## 5. Frontend — Cloudflare Pages (atau Vercel)
1. Di https://pages.cloudflare.com → **Create project → Connect GitHub** → pilih repo.
2. **Build settings:**
   - Root directory: `frontend`
   - Build command: `npm run build`
   - Output directory: `dist`
3. **Environment variable:** `VITE_API_URL = https://<backend-domain>/api`
4. **SPA routing:** tambah `frontend/public/_redirects` berisi:
   ```
   /*  /index.html  200
   ```
   (agar refresh di rute dalam tidak 404). Untuk Vercel, tambah `vercel.json` rewrite ke `/`.
5. Deploy → dapat domain (mis. `https://gaweqi.pages.dev`). Masukkan domain ini ke
   `FRONTEND_URL` backend, lalu redeploy backend.

## 6. Verifikasi
1. Buka domain frontend → halaman Login muncul.
2. Login `admin@gawe-qi.test` / `password` (dari seeder).
3. Buat tiket (akun client), ambil (akun IT), chat, selesaikan → cek notifikasi & laporan.

## 7. Domain & HTTPS
- HTTPS otomatis di Cloudflare Pages & Railway.
- Domain kustom gratis: subdomain platform. Untuk domain sendiri, arahkan DNS (CNAME) ke Pages.

## 8. Maintenance
- **Backup DB:** Neon punya branching/point-in-time (cek paket). Jadwalkan `pg_dump` berkala.
- **Log:** pantau via dashboard Railway; `APP_DEBUG=false` di produksi.
- **Update:** push ke branch → CI/deploy otomatis. Jalankan `migrate --force` pada release.
- **Kunci rahasia:** jangan commit `.env`; semua secret via dashboard env platform.

---
Selesai — aplikasi dapat diakses publik. Untuk skala lebih besar: pindah `artisan serve` ke
FrankenPHP/nginx, aktifkan Redis (queue & cache), dan gunakan storage cloud permanen.
