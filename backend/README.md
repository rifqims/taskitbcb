# Gawe-Qi — Backend (Laravel 12 + PostgreSQL)

REST API untuk sistem Task Management & Ticketing IT **Gawe-Qi** (codename `taskit`).
Status: **Fase 3 — scaffold + lapisan database** (migration, seeder, factory, model dari ERD).
Auth, service layer, dan endpoint API menyusul pada langkah berikutnya.

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
