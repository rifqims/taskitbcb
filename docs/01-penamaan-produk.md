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

> ⚠️ **Keputusan Anda dibutuhkan.** Untuk saat ini, semua dokumen dan kode akan memakai
> **codename `taskit`** (sesuai nama repo `taskitbcb`) sebagai penamaan teknis netral, sehingga
> keputusan nama *brand* tidak memblokir progres. Nama brand hanya memengaruhi teks yang tampil
> ke user (logo, judul, email) dan mudah diganti terpusat lewat satu variabel konfigurasi.
