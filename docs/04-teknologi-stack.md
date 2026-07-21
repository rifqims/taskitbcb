# Fase 1.3 — Keputusan Teknologi (dengan Perbandingan)

> **Tujuan tahap ini:** Memilih *stack* yang tepat **beserta alasan teknisnya**, karena keputusan
> ini memengaruhi semua tahap berikutnya. Kriteria Anda: open source, gratis, mudah dipelajari,
> mudah maintenance, skalabel, cepat dikembangkan.

## Ringkasan Keputusan (TL;DR)

| Lapisan | Pilihan | Alasan singkat |
|---------|---------|----------------|
| **Frontend** | React + Vite + TypeScript + Tailwind + shadcn/ui | SPA dashboard cepat, ekosistem komponen matang |
| **Backend** | **Laravel 12** (PHP 8.3) | Batteries-included → tercepat untuk 1 orang, maintenance mudah |
| **Database** | **PostgreSQL** | JSON + window function kuat untuk analitik; free tier Postgres-first |
| **ORM** | Eloquent (bawaan Laravel) | Migration/seeder/factory/relasi lengkap |
| **Auth** | Laravel Sanctum (SPA token) | Ringan, resmi, cocok SPA + API |
| **Realtime** | Laravel Reverb / polling (MVP) | Chat & progress live; mulai polling, upgrade ke WebSocket |
| **Queue/Cache** | Database driver → Redis (produksi) | Notifikasi & job SLA async |
| **Charts** | ApexCharts | Interaktif, ringan, gratis |
| **Icons** | Lucide | Konsisten dengan shadcn/ui |

## 1. Backend: Laravel 12 vs NestJS

Ini keputusan terbesar. Keduanya sangat baik; berikut perbandingan jujurnya.

| Kriteria | **Laravel 12 (PHP)** | **NestJS (Node/TS)** |
|----------|----------------------|----------------------|
| Kelengkapan bawaan | ⭐⭐⭐⭐⭐ Auth, ORM, migration, queue, mail, policy, validation, scheduler — semua ada | ⭐⭐⭐ Perlu rakit Prisma, Passport, dll sendiri |
| Kecepatan development (1 dev) | ⭐⭐⭐⭐⭐ Konvensi kuat, sedikit *boilerplate* | ⭐⭐⭐ Lebih banyak *setup* & kode |
| Kurva belajar | ⭐⭐⭐⭐ Dokumentasi terbaik di kelasnya | ⭐⭐⭐ Butuh paham DI, decorator, RxJS |
| Satu bahasa FE+BE | ❌ PHP + TS | ✅ TypeScript semua |
| Performa I/O tinggi | ⭐⭐⭐ Cukup untuk internal | ⭐⭐⭐⭐ Unggul di realtime/high-concurrency |
| Deploy free tier | ⭐⭐⭐ Perlu Docker/Nixpacks | ⭐⭐⭐⭐ Lebih ringan di platform Node |
| Ekosistem admin/report | ⭐⭐⭐⭐⭐ Excel, PDF, Nova/Filament | ⭐⭐⭐ Lebih manual |

**Rekomendasi: Laravel 12.**
Aplikasi ini **CRUD-heavy + banyak aturan bisnis + laporan**, bukan aplikasi *realtime
super tinggi*. Untuk satu IT Staff yang ingin *cepat jadi & mudah dirawat*, Laravel
memberi paling banyak "gratis" (auth, migration, policy, queue, scheduler untuk cek SLA,
export Excel/PDF). NestJS unggul saat butuh TypeScript end-to-end & concurrency ekstrem —
bukan kebutuhan dominan di sini. Kebutuhan realtime (chat/progress) ditangani Laravel
Reverb/Echo atau polling, cukup untuk skala internal.

> Jika suatu saat Anda ingin **satu bahasa (TS) penuh**, NestJS + Prisma adalah *plan B* yang
> valid — arsitektur berlapis yang kita rancang membuat migrasi konsep tetap mungkin.

## 2. Frontend: React + Vite vs Next.js

| Kriteria | **React + Vite (SPA)** | **Next.js** |
|----------|------------------------|-------------|
| Cocok untuk dashboard internal | ⭐⭐⭐⭐⭐ Tak butuh SEO/SSR | ⭐⭐⭐⭐ SSR mubazir di sini |
| Kesederhanaan (BE terpisah) | ⭐⭐⭐⭐⭐ Murni klien, pasangan bersih untuk API Laravel | ⭐⭐⭐ Ada dua "backend" (Next API + Laravel) |
| Kecepatan dev server | ⭐⭐⭐⭐⭐ Vite sangat cepat | ⭐⭐⭐⭐ |
| Deploy | ⭐⭐⭐⭐⭐ Static → Vercel/Cloudflare Pages gratis | ⭐⭐⭐⭐ Perlu runtime Node |

**Rekomendasi: React + Vite + TypeScript.** Karena backend sudah Laravel (API terpisah),
SPA murni adalah pasangan paling bersih, paling mudah di-*deploy* gratis (file statis di
Cloudflare Pages/Vercel), tanpa duplikasi lapisan server. Next.js baru menang kalau kita
butuh SSR/SEO — tidak relevan untuk aplikasi internal ber-login.

**UI kit:** **Tailwind CSS + shadcn/ui** (komponen *copy-paste* yang kita miliki penuh,
bukan dependency berat), **Lucide** untuk ikon, **ApexCharts** untuk grafik. Ini persis
gaya Linear/Notion yang Anda inginkan dan mendukung dark/light mode + Radix a11y.

## 3. Database: PostgreSQL vs MySQL

| Kriteria | **PostgreSQL** ⭐ | **MySQL** |
|----------|------------------|-----------|
| Tipe JSON/JSONB | ⭐⭐⭐⭐⭐ (bagus untuk metadata log/audit) | ⭐⭐⭐ |
| Window function (analitik) | ⭐⭐⭐⭐⭐ (laporan tren, ranking Top 10) | ⭐⭐⭐⭐ (v8+) |
| Integritas & constraint | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Free tier managed | ⭐⭐⭐⭐⭐ Neon, Supabase | ⭐⭐⭐ PlanetScale (berubah-ubah) |
| Familiaritas umum | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |

**Rekomendasi: PostgreSQL.** Modul **Analytics & Report** Anda banyak butuh agregasi,
ranking, dan tren waktu — di sinilah *window function* & JSONB Postgres bersinar. Free tier
terbaik (Neon, Supabase) juga Postgres-first. *Trade-off*: MySQL sedikit lebih familiar bagi
sebagian orang, tapi Eloquent menyembunyikan perbedaan sehingga biaya belajarnya minim.

## 4. Arsitektur Deployment (gratis) — ringkas

| Komponen | Layanan gratis | Catatan |
|----------|----------------|---------|
| Frontend (SPA statis) | **Cloudflare Pages** / Vercel | Build `vite`, output statis |
| Backend (Laravel API) | **Railway** / Render / Fly.io | Via Nixpacks/Docker |
| Database | **Neon** / Supabase (PostgreSQL) | Connection pooling |
| Storage file | **Supabase Storage** / Cloudinary | Abstraksi filesystem Laravel |
| Domain | Subdomain gratis platform, atau domain murah | HTTPS otomatis |

> Detail langkah-demi-langkah deployment akan dibahas di **Fase terakhir**, bukan sekarang —
> supaya kita tidak melompati tahap.

## 5. Ringkasan Justifikasi

Stack ini dipilih karena memberi **kecepatan development tertinggi untuk satu orang**,
**maintenance termudah** (konvensi Laravel + komponen shadcn yang kita miliki), **skalabilitas
cukup** lewat arsitektur berlapis, dan **100% bisa jalan di free tier**. Semua open source.

---
**Next:** `05-arsitektur.md` — bagaimana kode ditata (Clean Architecture, SOLID, layer).
