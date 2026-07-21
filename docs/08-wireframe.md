# Fase 2.2 — Wireframe (Susunan Layar)

> **Tujuan tahap ini:** Menyepakati *tata letak & alur* tiap layar utama sebelum masuk hi-fi
> dan koding. Wireframe fokus pada struktur, bukan warna. Prototipe hi-fi interaktif tersedia
> sebagai Artifact (HTML) untuk direview langsung.

## Kerangka Global (App Shell)

```
┌────────────────────────────────────────────────────────────┐
│ [≡] Gawe-Qi        🔍 Cari (⌘K)         🔔3   🌙   [Avatar▾] │  ← Topbar
├───────────┬────────────────────────────────────────────────┤
│ SIDEBAR   │  Breadcrumb: Dashboard / ...                     │
│ Dashboard │                                                  │
│ Tiket     │              AREA KONTEN                         │
│  Semua    │                                                  │
│  Task Saya│                                                  │
│ Kanban    │                                                  │
│ Kalender  │                                                  │
│ Laporan   │                                                  │
│ Audit     │                                            [ + ] │  ← FAB (Client)
└───────────┴────────────────────────────────────────────────┘
```
Sidebar menampilkan menu **sesuai role** (Client tidak melihat Audit/Laporan penuh).

## 1. Login
Kartu tengah, logo Gawe-Qi, field email+password, tombol masuk, toggle tema. Bersih, satu kolom.

## 2. Dashboard IT
```
[Total] [Baru] [Dikerjakan] [Pending] [Selesai] [Terlambat]   ← baris StatCard
[Deadline Hari Ini]  [Urgent]  [SLA Achievement %]
┌── Antrian Prioritas ───────────┐  ┌── Aktivitas Terbaru ──┐
│ TicketCard (urut Urgent→Low)   │  │ timeline ringkas       │
│ • badge prioritas + SLA 🟢🟡🔴 │  │                        │
└────────────────────────────────┘  └────────────────────────┘
```

## 3. Daftar Tiket — 3 mode tampilan
- **Table View:** kolom No, Judul, Divisi, Kategori, Prioritas, Status, SLA, Assignee, Dibuat.
  Sortable, quick-filter (chip: Baru/Dikerjakan/Urgent/Terlambat/Task Saya), search, paginate.
- **Kanban View:** kolom = status (Baru → Dikerjakan → Pending → Selesai → Ditolak); kartu
  bisa di-*drag* (IT). Tiap kartu: nomor, judul, prioritas, SLA, avatar assignee.
- **Calendar View:** tiket diletakkan pada tanggal deadline; klik → detail. Indikator warna SLA.

## 4. Buat Tiket (Client) — Dialog/Halaman
Form field (urut): Judul, Deskripsi (rich), Divisi, Nama Pengirim, Kategori, Prioritas, Deadline,
Lokasi (opsional), Lampiran & Screenshot (dropzone). Validasi inline. Tombol: Batal / Kirim.

## 5. Detail Tiket (layar terpenting)
```
┌── Header: TKT-202607-000123 · Judul · [PriorityBadge][StatusBadge][SLA] ──┐
├───────────────────────────────┬──────────────────────────────────────────┤
│  TAB: Percakapan | Timeline    │  PANEL KANAN (info)                       │
│  ┌ ChatThread (WA-like) ─────┐ │  • Status + tombol aksi                   │
│  │ bubble kiri/kanan, avatar │ │    [Ambil Task]/[Progress ▾]/[Selesai]    │
│  │ mention @, emoji, file    │ │    [Tidak Bisa Dikerjakan]                │
│  │ ...                       │ │  • Progress bar 0/25/50/75/100            │
│  └───────────────────────────┘ │  • Divisi, Kategori, Pengirim, Deadline   │
│  [ Ketik pesan… 📎 😊 @ ]      │  • Assignee + waktu mulai                  │
│                                 │  • Lampiran (thumbnail)                    │
└───────────────────────────────┴──────────────────────────────────────────┘
```
Tab **Timeline** = ActivityTimeline (dibuat, diambil, status berubah, progress, upload…).
Form **Tidak Bisa Dikerjakan** muncul sebagai dialog wajib isi: Alasan, Berita Acara,
Rekomendasi, Catatan.

## 6. Laporan (tampilan tabel — revisi sesuai permintaan)
Halaman Laporan berbentuk **tabel** dengan **toolbar** di atas dan **pagination** di bawah:
- **Search** (judul / nomor tiket).
- **Filter rentang tanggal:** Tanggal Mulai & Tanggal Akhir.
- **Sort judul:** A–Z / Z–A.
- **Filter waktu penyelesaian:** Tercepat / Terlama.
- **Filter prioritas:** Semua / Normal / High / Urgent.
- **Export:** Excel / PDF / CSV.
- Kolom tabel: No. Tiket, Judul, Divisi, Kategori, Prioritas, Status, Waktu Selesai, Teknisi, Tanggal.
- **Pagination** (‹ 1 2 3 … ›) + info "Menampilkan x–y dari N".

*(Dashboard Analytics dengan grafik interaktif — Line/Donut/Bar, Top 10 — tetap direncanakan
sebagai halaman terpisah pada fase setelah MVP.)*

## 6b. Kalender Deadline
Tampilan bulanan (grid Sen–Min). Setiap tiket muncul sebagai pill berwarna prioritas pada
**tanggal jatuh tempo SLA**-nya. Gunanya: melihat sebaran tenggat & beban kerja dalam sebulan
sekilas, lalu klik pill → detail tiket. (Menjawab "untuk apa kalender": ini **peta deadline**,
bukan penjadwalan manual.)

## 6c. Profil Akun (CRUD akun)
Halaman profil (diakses dari footer sidebar) berisi:
- **Foto:** ganti / hapus foto.
- **Data diri:** nama, email, no. telepon, divisi, jabatan → Simpan.
- **Keamanan:** ubah password.
- **Zona berbahaya:** nonaktifkan akun & **hapus akun permanen** (riwayat tiket tetap disimpan
  untuk audit — akun tidak bisa login lagi). Aksi hapus dikonfirmasi lewat dialog.

## 7. Responsif
- **Desktop:** sidebar penuh, detail 2 kolom.
- **Tablet:** sidebar ikon-only, detail tetap 2 kolom (panel kanan bisa di-*collapse*).
- **Mobile:** sidebar → drawer, detail jadi 1 kolom (tab), chat full-width, FAB tetap.

---
**Prototipe hi-fi:** lihat Artifact HTML interaktif (dark/light, dashboard, kanban, detail+chat).
Setelah Anda setujui arah visualnya, kita lanjut **Fase 3 — Scaffold Backend Laravel**.
