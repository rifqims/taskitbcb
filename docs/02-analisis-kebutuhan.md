# Fase 1.1 — Requirement Analysis (Analisis Kebutuhan)

> **Tujuan tahap ini:** Menerjemahkan permintaan bisnis menjadi daftar kebutuhan yang
> terukur dan tidak ambigu, memisahkan mana yang *wajib (MVP)* dan mana yang *bisa menyusul*.
> Ini adalah fondasi; SRS, ERD, dan desain UI semuanya diturunkan dari sini.

## 1. Latar Belakang & Masalah

Saat ini permintaan IT masuk lewat WhatsApp/chat yang tersebar, sehingga:

- Permintaan **hilang** atau terlupakan.
- Tidak ada **prioritas** yang jelas → yang ramai duluan, bukan yang penting duluan.
- Tidak ada **histori** dan **bukti** pekerjaan.
- Tidak ada **ukuran performa** tim IT (berapa cepat, berapa banyak, SLA tercapai?).

**Sasaran sistem:** menjadi *single source of truth* untuk seluruh permintaan IT — dari
pengajuan, pengerjaan, komunikasi, penyelesaian, hingga pelaporan dan evaluasi performa.

## 2. Aktor & Peran (RBAC)

| Aktor | Deskripsi | Hak akses inti |
|-------|-----------|----------------|
| **Administrator** | Pemilik sistem / kepala IT | Kelola user & role, lihat audit log, semua laporan, konfigurasi SLA/kategori, hapus data |
| **IT Support** | Teknisi yang mengerjakan tiket | Ambil tiket, ubah status/progress, chat, tutup/tolak tiket, lihat dashboard operasional |
| **Client** | Karyawan pengaju dari divisi mana pun | Buat tiket, lihat progres tiket miliknya, chat, beri rating penyelesaian |

> Prinsip: **least privilege** — setiap role hanya melihat & melakukan yang relevan.

## 3. Kebutuhan Fungsional (Functional Requirements)

Diberi kode `FR-x` agar bisa dilacak ke SRS, ke test case, dan ke tabel database.

### A. Autentikasi & Pengguna
- **FR-1** User login memakai akun perusahaan (email + password).
- **FR-2** RBAC: hak akses ditentukan oleh role.
- **FR-3** Admin mengelola user (buat, nonaktifkan, ubah role).

### B. Manajemen Tiket
- **FR-4** Client membuat tiket dengan field: **Nomor Tiket (auto), Judul, Deskripsi, Divisi,
  Nama Pengirim, Kategori, Prioritas, Deadline, Lampiran, Screenshot, Lokasi (opsional)**.
- **FR-5** Nomor tiket digenerate otomatis & unik (format `TKT-YYYYMM-000123`).
- **FR-6** Kategori: Hardware, Software, Printer, Internet, Network, Email, Website, Server,
  CCTV, Aplikasi, Lainnya. (Disimpan sebagai data referensi agar bisa ditambah tanpa deploy.)
- **FR-7** Prioritas: Low, Medium, High, Urgent.
- **FR-8** Antrian diurutkan: Urgent → High → Medium → Low; prioritas sama = **FIFO** by waktu buat.

### C. SLA
- **FR-9** Setiap tiket punya target SLA berdasar prioritas (default: Low=3 hari, Medium=2 hari,
  High=1 hari, Urgent=4 jam) — nilai dapat dikonfigurasi Admin.
- **FR-10** Indikator SLA: 🟢 aman, 🟡 mendekati deadline, 🔴 lewat/segera lewat.

### D. Alur Kerja Teknisi
- **FR-11** IT "Ambil Task" → status jadi **Sedang Dikerjakan**, tiket **terkunci** ke teknisi itu,
  nama teknisi & **waktu mulai** tercatat.
- **FR-12** IT mengubah **progress**: 0/25/50/75/100%. Client melihat progress *realtime*.
- **FR-13** Penyelesaian: **Selesai** atau **Tidak Bisa Dikerjakan**. Jika ditolak, wajib isi:
  Alasan, Berita Acara, Rekomendasi, Catatan, Tanggal, Nama Teknisi.
- **FR-14** Status tiket: `Baru`, `Sedang Dikerjakan`, `Pending`, `Selesai`, `Ditolak`, `Terlambat`.

### E. Komunikasi
- **FR-15** Tiap tiket punya halaman detail berisi **Timeline Activity** + **Chat**.
- **FR-16** Chat mendukung: mention `@user`, emoji, upload (PDF, Excel, Word, ZIP, Image, Video),
  voice note (opsional/fase lanjut).
- **FR-17** Seluruh percakapan tersimpan permanen.

### F. Dashboard & Tampilan
- **FR-18** Dashboard IT menampilkan kartu ringkas: Task Baru, Sedang Dikerjakan, Pending, Selesai,
  Ditolak, Terlambat, Deadline Hari Ini, Urgent, Task Saya, Semua.
- **FR-19** Tampilan tiket: **Table View, Kanban View, Calendar View**, + Quick Filter & Search.

### G. Notifikasi
- **FR-20** Notifikasi in-app (toast + badge). Email & Push (PWA) = opsional/fase lanjut.
- **FR-21** Client dinotifikasi saat: tiket diterima, diproses, selesai, ditolak, ada balasan chat,
  ada mention.

### H. Pelaporan & Analitik
- **FR-22** Laporan: harian, mingguan, bulanan, tahunan; per divisi, per teknisi, per kategori,
  per prioritas.
- **FR-23** Export: Excel, PDF, CSV.
- **FR-24** Dashboard analitik: Total/Open/Completed/Rejected, Avg Completion Time, SLA Achievement,
  Top 10 Divisi, Top 10 Kategori, tren task per bulan/minggu/hari, grafik interaktif.

### I. Log & Audit
- **FR-25** Activity Log mencatat setiap aksi (buat/ambil/ubah status/progress/komentar/upload/
  download/delete/login/logout) beserta **tanggal, jam, user, IP address, browser**.
- **FR-26** Audit Log: Admin dapat melihat *before/after* setiap perubahan data penting.

## 4. Kebutuhan Non-Fungsional (Non-Functional Requirements)

| Kode | Aspek | Target |
|------|-------|--------|
| NFR-1 | **Keamanan** | RBAC, proteksi CSRF/XSS/SQLi, hashing password (bcrypt/argon2), rate limit, HTTPS-ready |
| NFR-2 | **Performa** | Respons API < 300ms untuk operasi umum; daftar tiket ter-*paginate* & ter-*index* |
| NFR-3 | **Skalabilitas** | Arsitektur berlapis (service + repository) agar mudah tumbuh |
| NFR-4 | **Ketersediaan** | Deploy di layanan gratis yang andal; siap dipindah tanpa ubah kode |
| NFR-5 | **Usability** | UI modern (Linear/Notion-like), responsive, dark/light mode, skeleton loading |
| NFR-6 | **Maintainability** | Clean Architecture, SOLID, kode modular, dokumentasi API (OpenAPI) |
| NFR-7 | **Auditability** | Semua perubahan data penting terekam & tak bisa diam-diam diubah |
| NFR-8 | **Realtime** | Progress & chat update tanpa refresh (polling atau WebSocket) |

## 5. Prioritas Rilis (MoSCoW) — supaya tidak *over-engineering* di awal

- **MVP (Must):** FR-1..FR-15, FR-18, FR-19, FR-20 (in-app), FR-25. Ini sudah menggantikan WhatsApp.
- **Should (rilis ke-2):** FR-16 (upload chat penuh), FR-21, FR-22, FR-23, FR-24, FR-26.
- **Could (nanti):** voice note, email & push PWA, integrasi SSO perusahaan.
- **Won't (belum):** mobile app native, integrasi WhatsApp Business API.

> **Rekomendasi mentor:** kita bangun **MVP dulu** sampai bisa dipakai harian, baru tambah
> laporan & analitik. Ini mengurangi risiko dan memberi *value* tercepat. Semua tetap dirancang
> agar fitur lanjutan tinggal "colok" tanpa bongkar arsitektur.

---
**Next:** `03-srs.md` (Software Requirement Specification) yang memformalkan use case & aturan.
