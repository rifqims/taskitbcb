# Gawe-Qi — Backend (Laravel 12 + PostgreSQL)

REST API untuk sistem Task Management & Ticketing IT **Gawe-Qi** (codename `taskit`).
Status: **Fase 3 — backend SELESAI** (33 test lulus): database, Auth/RBAC, Modul Tiket,
Chat & Notifikasi, Activity/Audit Log, Laporan/Analytics, dan dokumentasi OpenAPI.
Dokumentasi API lengkap: [`docs/api/openapi.yaml`](../docs/api/openapi.yaml).

## Ringkasan modul & endpoint
| Modul | Endpoint |
|-------|----------|
| Auth | `POST /auth/login`, `GET /auth/me`, `POST /auth/logout` |
| Tiket | `GET/POST /tickets`, `GET /tickets/{id}`, `POST .../assign`, `PATCH .../progress`, `PATCH .../status`, `POST .../resolve` |
| Chat | `GET/POST /tickets/{id}/messages` |
| Notifikasi | `GET /notifications`, `GET /notifications/unread-count`, `POST /notifications/{id}/read`, `POST /notifications/read-all` |
| Laporan | `GET /reports/summary`, `GET /reports/analytics`, `GET /reports/export` (CSV) |
| Log (admin) | `GET /admin/activity-logs`, `GET /admin/audit-logs` |

## Endpoint Tiket
| Method | Endpoint | Akses | Fungsi |
|--------|----------|-------|--------|
| GET | `/api/tickets` | token | daftar (filter: status, priority, category_id, division_id, mine, overdue, search; sort: default antrian/newest/oldest/title_asc/title_desc; paginate) |
| POST | `/api/tickets` | client | buat tiket (+ lampiran) — nomor & SLA otomatis |
| GET | `/api/tickets/{id}` | pemilik / IT / admin | detail + timeline |
| POST | `/api/tickets/{id}/assign` | IT / admin | ambil & kunci tiket (atomik) |
| PATCH | `/api/tickets/{id}/progress` | assignee / admin | ubah progress (0/25/50/75/100) |
| PATCH | `/api/tickets/{id}/status` | assignee / admin | tandai Pending / lanjutkan |
| POST | `/api/tickets/{id}/resolve` | assignee / admin | Selesai / Tidak Bisa Dikerjakan (berita acara) |

## Endpoint Auth (tersedia sekarang)
| Method | Endpoint | Akses | Fungsi |
|--------|----------|-------|--------|
| POST | `/api/auth/login` | publik (rate-limit 6/mnt) | login → token Sanctum + data user |
| GET | `/api/auth/me` | token | profil user login |
| POST | `/api/auth/logout` | token | cabut token aktif |
| GET | `/api/admin/ping` | role: admin | contoh guard RBAC |
| GET | `/api/it/ping` | role: admin, it_support | contoh guard RBAC |

Login contoh:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Accept: application/json" \
  -d "email=admin@gawe-qi.test&password=password"
# gunakan token: -H "Authorization: Bearer <token>"
```

Guard RBAC: `->middleware('role:admin,it_support')`. Otorisasi per-tiket di `app/Policies/TicketPolicy.php`.
Jalankan test: `php artisan test` (11 test auth & RBAC lulus).

## Prasyarat
- PHP 8.3+ · Composer
- PostgreSQL 14+ (utama). Untuk test cepat bisa pakai SQLite.

## Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Sesuaikan kredensial PostgreSQL di .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD),
# lalu buat database-nya:  createdb gaweqi

php artisan migrate --seed
php artisan serve            # http://localhost:8000
```

## Akun demo (setelah seed) — password semua: `password`
| Email | Role |
|-------|------|
| admin@gawe-qi.test | Administrator |
| rifqi@gawe-qi.test | IT Support |
| agus@gawe-qi.test | IT Support |
| bagus@gawe-qi.test | Client (Sales) |
| dewi@gawe-qi.test | Client (HRD) |

## Struktur (lapisan database saat ini)
```
app/
├─ Enums/         Role, Priority, TicketStatus, ResolutionOutcome
└─ Models/        User, Division, Category, SlaPolicy, Ticket (+ child models),
                  Notification, ActivityLog, AuditLog
database/
├─ migrations/    14 tabel domain (lihat docs/06-database-erd.md)
├─ seeders/       Division, Category, SlaPolicy, User, Ticket (data demo)
└─ factories/     User, Division, Category, Ticket
```

## Catatan teknis
- **priority_weight** disimpan (turunan dari priority) untuk sorting antrian ter-index — lihat
  `Ticket::scopeQueueOrder()` (BR-2).
- **Indikator SLA** 🟢🟡🔴 dihitung `Ticket::slaIndicator()` (BR-3); status "Terlambat" bersifat
  turunan dari `sla_due_at` (BR-4), bukan kolom.
- **CHECK constraint** (progress 0–100, rating 1–5) aktif di PostgreSQL; dilewati di SQLite.
- Nama brand terpusat di `config('app.brand_name')` (env `APP_BRAND_NAME`).

> Migrasi & seeder telah diverifikasi jalan (18 migrasi + 5 seeder sukses pada SQLite test).
