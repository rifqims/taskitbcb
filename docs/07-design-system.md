# Fase 2.1 — Design System (Gawe-Qi)

> **Tujuan tahap ini:** Menetapkan bahasa visual yang konsisten (token warna, tipografi,
> spacing, radius, komponen) supaya seluruh UI terasa satu kesatuan — gaya SaaS modern
> ala Linear/Notion, mendukung dark & light mode. Token ini nanti dipetakan langsung ke
> konfigurasi Tailwind + variabel CSS shadcn/ui.

## 1. Prinsip Desain
- **Minimalis & fungsional** — konten (tiket) adalah bintangnya; UI tidak berisik.
- **Konsisten** — jarak, radius, dan warna dari token yang sama; tidak ada nilai "ajaib".
- **Aksesibel** — kontras memadai (WCAG AA), fokus terlihat, komponen Radix (a11y bawaan).
- **Cepat terasa** — skeleton loading, transisi halus (150–200ms), optimistic update.

## 2. Token Warna (CSS variables, HSL)

Memakai pola shadcn/ui: satu set variabel untuk light, satu untuk dark. **Brand Gawe-Qi**
memakai **gradasi biru** sebagai identitas utama:
`linear-gradient(135deg, #9FD6FE → #2A96EF)` (biru muda ke biru), dengan warna solid
`#2A96EF` untuk elemen yang butuh teks putih (tombol, bubble chat) agar kontras tetap AA.

```css
:root {                        /* LIGHT */
  --grad: linear-gradient(135deg,#9fd6fe,#2a96ef);   /* identitas Gawe-Qi */
  --grad-strong: linear-gradient(135deg,#2a96ef,#1668b8); /* utk teks putih */
  --background: #ffffff;
  --foreground: #152230;
  --muted: #eef4fa;            /* netral bias-biru, bukan abu datar */
  --muted-foreground: #5b6b7d;
  --border: #e1eaf3;
  --primary: #2a96ef;          /* biru Gawe-Qi (solid) */
  --primary-strong: #1a7fd6;
  --primary-soft: #e3f2fe;
  --ring: #2a96ef;
}
:root.dark {                   /* DARK */
  --background: #0b1017;
  --foreground: #e5eef7;
  --muted: #18222e;
  --muted-foreground: #94a6ba;
  --border: #233140;
  --primary: #4ea8f5;
  --primary-strong: #2a96ef;
  --primary-soft: #0f2942;
  --ring: #4ea8f5;
}
```

**Aturan pemakaian gradasi (penting untuk kontras):** gradasi `#9FD6FE→#2A96EF` dipakai pada
permukaan dekoratif besar — logo, progress bar, ring avatar — di mana **tidak ada teks putih
kecil**. Untuk tombol primer, bubble chat "saya", dan kartu statistik aksen (yang memuat teks
putih) dipakai `--grad-strong` (nada lebih gelap) agar rasio kontras teks tetap ≥ 4.5:1.

### Warna Semantik (status & prioritas) — konsisten di seluruh app

| Makna | Token | Light | Penggunaan |
|-------|-------|-------|-----------|
| Prioritas Low | emerald | 🟢 `152 60% 45%` | badge prioritas |
| Prioritas Medium | amber | 🟡 `40 92% 50%` | badge prioritas |
| Prioritas High | orange | 🟠 `24 92% 53%` | badge prioritas |
| Prioritas Urgent | red | 🔴 `0 78% 56%` | badge prioritas |
| SLA aman | emerald | hijau | indikator SLA |
| SLA mendekati | amber | kuning | indikator SLA |
| SLA lewat | red | merah | indikator SLA |
| Status Baru | slate | abu | kolom kanban |
| Sedang Dikerjakan | blue | biru | kolom kanban |
| Pending | amber | kuning | kolom kanban |
| Selesai | emerald | hijau | kolom kanban |
| Ditolak | red | merah | kolom kanban |

## 3. Tipografi
- **Font:** `Inter` (UI) + `JetBrains Mono` (nomor tiket, kode). Keduanya open source.
- **Skala:** `xs 12 / sm 13 / base 14 / lg 16 / xl 18 / 2xl 22 / 3xl 28`. Basis 14px cocok
  untuk aplikasi padat data (dashboard/tabel), seperti Linear.
- **Berat:** 400 (teks), 500 (label), 600 (heading/aksen).

## 4. Spacing, Radius, Shadow
- **Spacing scale (px):** 2, 4, 8, 12, 16, 20, 24, 32, 40, 48 (kelipatan 4).
- **Radius:** `--radius: 0.65rem`; kartu `lg`, tombol/input `md`, badge `full`.
- **Shadow:** halus — `sm` untuk kartu, `md` untuk popover/dialog. Hindari bayangan berat.
- **Glassmorphism ringan:** hanya di top-bar & command palette (`backdrop-blur` + border tipis).

## 5. Inventaris Komponen (dibangun dari shadcn/ui)
| Kelompok | Komponen |
|----------|----------|
| Navigasi | AppShell, Sidebar (collapsible), Topbar, Breadcrumb, CommandPalette (⌘K) |
| Data | DataTable (sort/filter/paginate), KanbanBoard, CalendarView, StatCard |
| Tiket | TicketCard, PriorityBadge, StatusBadge, SlaIndicator, ProgressBar |
| Form | Input, Textarea, Select, DatePicker, FileDropzone, Combobox |
| Umpan balik | Toast, Skeleton, EmptyState, Dialog, AlertDialog, Tooltip, Badge |
| Detail | ActivityTimeline, ChatThread, MessageBubble, MentionInput, Avatar |
| Chart | LineChart, BarChart, DonutChart (ApexCharts) |

## 6. Pola Interaksi Kunci
- **Command Palette (⌘K):** cari tiket, pindah menu, aksi cepat ("Buat Tiket").
- **Floating Action Button:** "Buat Tiket" untuk Client (kanan bawah).
- **Sidebar collapse:** ikon-only pada layar sempit; responsif ke drawer di mobile.
- **Empty state:** ilustrasi + CTA di tiap daftar kosong.
- **Skeleton:** untuk semua daftar/kartu saat memuat, bukan spinner penuh layar.

---
**Next:** `08-wireframe.md` — susunan tiap layar, lalu prototipe HTML hi-fi untuk direview.
