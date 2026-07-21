# Fase 1.0 — Rekomendasi Penamaan Produk

> **Tujuan tahap ini:** Memilih nama produk yang profesional, mudah diingat, dan aman
> secara branding (tidak bentrok dengan produk besar), sebelum kita mengunci identitas
> visual, domain, dan struktur kode.

## Kenapa nama itu penting lebih awal?

Nama produk masuk ke banyak tempat yang mahal untuk diubah belakangan: nama package/namespace
kode, nama database, nama repo, domain, judul email notifikasi, dan branding UI. Menetapkannya
di awal menghindari *rename* besar-besaran nanti.

## Evaluasi nama "B-Task"

- **Kelebihan:** singkat, mudah diucapkan.
- **Kekurangan:** terlalu generik, sulit di-*trademark*, dan banyak produk memakai pola "X-Task".
  Sebagai sistem *internal enterprise* ini tidak fatal, tapi kita bisa lebih baik.

## Rekomendasi Nama (diurutkan berdasarkan rekomendasi)

| # | Nama | Makna / Rasional | Kesan |
|---|------|------------------|-------|
| 1 | **HelixDesk** ⭐ | "Helix" = alur berputar rapi (tiket mengalir teratur); "Desk" = service desk. | Modern, enterprise, mudah di-branding |
| 2 | **TaskPilot** | Memandu (pilot) setiap task IT dari masuk sampai selesai. | Ramah, jelas fungsinya |
| 3 | **Servio** | Dari "service" — pendek, brandable, terdengar seperti SaaS. | Elegan, netral |
| 4 | **OpsBoard** | Papan operasional IT (Kanban + monitoring). | Deskriptif, teknis |
| 5 | **Ticketa** | Turunan "ticket", mudah diingat. | Sederhana, langsung |

**Rekomendasi utama saya: `HelixDesk`** — cukup unik untuk branding, terdengar profesional,
dan tidak mengunci kita hanya ke kata "task" atau "ticket" sehingga produk bisa berkembang
(misal nanti menambah asset management atau knowledge base).

## ✅ KEPUTUSAN FINAL (21 Jul 2026)

Brand yang dipilih pemilik produk: **`Gawe-Qi`**.

- **Makna:** "Gawe" (Jawa/Indonesia) = *kerja/pekerjaan*; "Qi" memberi kesan energi & modern.
  Nama ini lokal, khas, dan mudah diingat oleh pengguna internal — pas untuk sistem
  manajemen pekerjaan IT.
- **Penerapan teknis:** kode & database tetap memakai **codename `taskit`** (netral, sesuai
  repo `taskitbcb`). Nama brand **Gawe-Qi** hanya muncul di teks yang tampil ke user (logo,
  judul aplikasi, email, dokumen) dan disimpan di **satu variabel konfigurasi** (`APP_BRAND_NAME`)
  sehingga mudah diganti terpusat tanpa menyentuh logika.
