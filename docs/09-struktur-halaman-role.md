# Fase 2.3 — Information Architecture & Struktur Halaman per Role

> **Tujuan tahap ini:** Memformalkan struktur navigasi & halaman berdasarkan **role**
> (disepakati bersama pemilik produk). Ini menjadi acuan resmi untuk membangun App Shell,
> guard RBAC di frontend, dan urutan pembuatan halaman.

## Prinsip
Menu & halaman yang tampil **mengikuti role** (least privilege). Satu App Shell yang sama,
tapi item sidebar & aksi berbeda per role.

## 1. Area Publik / Autentikasi
- **Halaman Login:** Card di tengah, logo Gawe-Qi, input email & password, opsi "Lupa Password".
  Dark/Light mode otomatis.

## 2. Area Client (Karyawan Umum)
**Sidebar:** Dashboard · Tiket Saya · Buat Tiket Baru
**Top Bar:** Global Search · Ikon Notifikasi (bel + badge merah) · Profil & Logout
**Konten Dashboard:**
- Summary Cards: Total Tiket Saya, Sedang Diproses, Selesai.
- Recent Tickets: tabel ringkas 5 tiket terakhir milik client + statusnya.
**Form Buat Tiket:** stepper/formulir rapi — memisahkan info dasar (judul, kategori) dari
detail (deskripsi, prioritas, lampiran).

## 3. Area IT Support (Workspace Teknisi)
**Sidebar:** Dashboard IT · Daftar Semua Tiket (tab: Open / In Progress / Selesai) · Tiket Saya
**Konten Dashboard:**
- **Kanban Board:** kolom To Do (Open) · Doing (In Progress) · Done (Resolved), drag-and-drop.
- **SLA Alert:** daftar tiket yang akan segera melewati batas waktu (indikator merah/kuning).
**Detail Tiket & Chat (Ruang Kerja):**
- Kiri: detail lengkap (deskripsi, pengirim, kategori, SLA).
- Kanan: chat real-time dengan client + input lampiran.
- Atas: tombol Update Progress (slider 0–100%) & tombol Resolve / Reject.

## 4. Area Administrator (Control Panel)
**Sidebar:** seluruh menu IT Support **+** Laporan & Analytics · Master Data (Users, Divisi,
Kategori) · Audit & Activity Log
**Dashboard Analytics:**
- Grafik tren tiket per bulan/minggu.
- Tabel Top 10 kategori masalah terbanyak.
- Tabel pencapaian SLA per teknisi.

## 5. Sistem Komponen (shadcn/ui)
| Komponen | Pemakaian |
|----------|-----------|
| **Data Table** | Daftar tiket: sorting, filtering, pagination (halaman Laporan & Semua Tiket) |
| **Badge** | Status/prioritas tiket (Hijau=Selesai, Merah=Urgent, dst) |
| **Avatar** | Inisial/foto pengirim & teknisi |
| **Sheet / Dialog** | Form ringkas atau preview lampiran tanpa pindah halaman |
| **Toast / Sonner** | Notifikasi sukses/gagal di pojok layar ("Tiket berhasil dibuat") |
| **Skeleton** | Efek loading modern sebelum data selesai dimuat |

> Referensi visual: prototipe hi-fi (Artifact) sudah mendemokan App Shell, dashboard,
> kanban, detail+chat, laporan tabel, kalender, dan profil dengan tema gradasi biru Gawe-Qi.
