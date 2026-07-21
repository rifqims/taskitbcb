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
memakai *indigo/violet* sebagai warna utama (energik, modern, netral gender).

```css
:root {                        /* LIGHT */
  --background: 0 0% 100%;
  --foreground: 224 12% 12%;
  --card: 0 0% 100%;
  --muted: 220 14% 96%;
  --muted-foreground: 220 9% 46%;
  --border: 220 13% 91%;
  --primary: 245 75% 59%;      /* indigo Gawe-Qi */
  --primary-foreground: 0 0% 100%;
  --ring: 245 75% 59%;
}
.dark {                        /* DARK */
  --background: 224 20% 8%;
  --foreground: 220 14% 92%;
  --card: 224 18% 11%;
  --muted: 223 16% 16%;
  --muted-foreground: 220 10% 60%;
  --border: 223 14% 20%;
  --primary: 245 80% 66%;
  --primary-foreground: 224 20% 8%;
  --ring: 245 80% 66%;
}
```

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
