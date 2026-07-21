# Roadmap Pengembangan — Peta Tahapan & Status Persetujuan

Dokumen ini adalah "papan kendali" kita. Sesuai permintaan Anda, kita **tidak melompati
tahapan** dan **menunggu persetujuan** sebelum lanjut. Centang ✅ = selesai, ⏳ = menunggu
persetujuan Anda, ⬜ = belum mulai.

## Fase 1 — Analisis & Fondasi Desain  ✅ (menunggu review Anda)
| Tahap | Dokumen | Status |
|-------|---------|--------|
| Rekomendasi nama | `01-penamaan-produk.md` | ✅ |
| Requirement Analysis | `02-analisis-kebutuhan.md` | ✅ |
| SRS + Use Case + Activity/State | `03-srs.md` | ✅ |
| Keputusan Teknologi (perbandingan) | `04-teknologi-stack.md` | ✅ |
| Arsitektur (Clean/SOLID/layer) | `05-arsitektur.md` | ✅ |
| ERD & Desain Database | `06-database-erd.md` | ✅ |

## Fase 2 — Desain UI/UX  ⏳ (butuh persetujuan untuk mulai)
- Wireframe low-fidelity (dashboard, list/kanban/calendar, detail tiket, form)
- Design System (token warna, tipografi, spacing, komponen shadcn)
- High-fidelity mock utama + dark/light mode

## Fase 3 — Backend Development  ⬜
- Scaffold Laravel 12, konfigurasi env & Postgres
- Migration + Seeder + Factory (dari ERD)
- Auth (Sanctum) + RBAC + Policy
- Modul Tiket (buat, ambil, status, progress, resolusi) + Service Layer
- Chat, Activity Log, Audit Log
- API + OpenAPI docs + test

## Fase 4 — Frontend Development  ⬜
- Scaffold React + Vite + Tailwind + shadcn
- App shell (sidebar, command palette, theme, breadcrumb)
- Auth flow + guard RBAC
- Dashboard + Table/Kanban/Calendar + Detail tiket + Chat realtime

## Fase 5 — Integrasi Lanjutan  ⬜
- Notifikasi (in-app → email/PWA), Report & Export (Excel/PDF/CSV), Analytics

## Fase 6 — Testing, Deployment, Maintenance  ⬜
- Testing (unit/feature/e2e), CI
- Deploy: Cloudflare Pages + Railway/Render + Neon/Supabase (langkah demi langkah)
- Panduan maintenance & backup

---

## 🚦 Keputusan yang saya butuhkan dari Anda sebelum lanjut

1. **Nama brand** — setujui `HelixDesk` atau pilih dari daftar / usulkan sendiri.
   (Tidak memblokir; kode pakai codename `taskit`.)
2. **Stack** — konfirmasi **Laravel 12 + React/Vite + PostgreSQL**, atau minta saya
   ubah (mis. NestJS full-TypeScript).
3. **Ruang lingkup rilis pertama** — setuju fokus **MVP dulu** (lihat MoSCoW di
   `02-analisis-kebutuhan.md`) sebelum modul laporan/analitik?
4. **Tahap berikutnya** — mulai dari **Fase 2 (Wireframe/UI)** atau langsung **Fase 3
   (scaffold Backend)**?

Setelah Anda menjawab, saya lanjut ke tahap yang disetujui — satu fase per langkah.
