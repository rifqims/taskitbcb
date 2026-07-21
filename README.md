# taskit — Task Management & Ticketing System (Enterprise Grade)

> Codename teknis: **taskit** · Nama brand: **Gawe-Qi**

Pusat seluruh permintaan IT Support perusahaan — menggantikan permintaan lewat WhatsApp yang
mudah hilang. Satu tempat untuk mengelola, memprioritaskan, mengerjakan, mengomunikasikan,
mendokumentasikan, dan mengevaluasi setiap pekerjaan IT.

## Status Proyek

🏗️ **Fase 1 — Analisis & Fondasi Desain: SELESAI (menunggu review).**
Belum ada kode aplikasi; sesuai kesepakatan kita mengerjakan **bertahap** dan menunggu
persetujuan di setiap fase. Lihat **[docs/00-roadmap.md](docs/00-roadmap.md)**.

## Dokumentasi (baca berurutan)

| # | Dokumen | Isi |
|---|---------|-----|
| 0 | [Roadmap & Status](docs/00-roadmap.md) | Peta tahapan + keputusan yang dibutuhkan |
| 1 | [Penamaan Produk](docs/01-penamaan-produk.md) | Rekomendasi nama brand |
| 2 | [Analisis Kebutuhan](docs/02-analisis-kebutuhan.md) | Aktor, FR/NFR, prioritas rilis (MoSCoW) |
| 3 | [SRS](docs/03-srs.md) | Use case, aturan bisnis, state machine tiket |
| 4 | [Keputusan Teknologi](docs/04-teknologi-stack.md) | Laravel vs NestJS, Postgres vs MySQL, dll |
| 5 | [Arsitektur](docs/05-arsitektur.md) | Clean Architecture, SOLID, struktur folder |
| 6 | [Database & ERD](docs/06-database-erd.md) | ERD, normalisasi, index, foreign key |

## Rencana Stack (rekomendasi, menunggu konfirmasi)

- **Frontend:** React + Vite + TypeScript + Tailwind CSS + shadcn/ui + Lucide + ApexCharts
- **Backend:** Laravel 12 (PHP 8.3) — REST API, Sanctum, Eloquent, Queue, Policy
- **Database:** PostgreSQL
- **Deployment (gratis):** Cloudflare Pages (FE) · Railway/Render (BE) · Neon/Supabase (DB)

Alasan pemilihan lengkap ada di [docs/04-teknologi-stack.md](docs/04-teknologi-stack.md).
