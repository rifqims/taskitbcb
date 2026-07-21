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

## 6. Laporan & Analytics (fase setelah MVP, tapi dirancang sekarang)
Grid StatCard + grafik (Line: task/bulan; Donut: per kategori; Bar: Top 10 divisi/teknisi).
Filter periode (harian/mingguan/bulanan/tahunan) + tombol Export (Excel/PDF/CSV).

## 7. Responsif
- **Desktop:** sidebar penuh, detail 2 kolom.
- **Tablet:** sidebar ikon-only, detail tetap 2 kolom (panel kanan bisa di-*collapse*).
- **Mobile:** sidebar → drawer, detail jadi 1 kolom (tab), chat full-width, FAB tetap.

---
**Prototipe hi-fi:** lihat Artifact HTML interaktif (dark/light, dashboard, kanban, detail+chat).
Setelah Anda setujui arah visualnya, kita lanjut **Fase 3 — Scaffold Backend Laravel**.
