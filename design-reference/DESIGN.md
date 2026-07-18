# SIMUTURS — Design Reference

Source: Figma Make "Hospital Quality Management UI (Better)"
https://www.figma.com/make/IUNG0fbmWbMFs580uHJPLs/

Extracted prototype code: `src/App.tsx` (all screens) + `src/index.css` (theme).

## Color palette

| Token        | Hex       | Usage |
|--------------|-----------|-------|
| Navy         | `#293681` | Sidebar bg, headings, INM badge, target line, superAdmin badge |
| Primary blue | `#4274D9` | Primary buttons, active states, IMP-RS badge, komiteMutu badge |
| Teal border  | `#95CCDD` | Card/table borders, scrollbar |
| Teal card    | `#D0E7E6` | Table header bg, hover fills, unit chips |
| Green        | `#22c55e` | Target met / filled |
| Amber        | `#f59e0b` | Warning / approaching deadline |
| Red          | `#ef4444` | Below target / overdue |
| Page bg      | `#f8fafc` | Body background |
| Teal (role)  | `#0d9488` | kepalaUnit badge |

Status badges: green `#dcfce7`/`#15803d`, yellow `#fef9c3`/`#854d0e`, red `#fee2e2`/`#991b1b` (bg/text + matching border).

## Typography

- **Inter** — body text
- **Plus Jakarta Sans** — headings, table headers (11px uppercase, letter-spacing 0.06em)
- **JetBrains Mono** — numbers, table cells, clock

Cards: white, rounded-xl (12px), border `#95CCDD`, subtle shadow.

## Layout shell

- **Sidebar** 224px navy: brand "SIMUTURS — Sistem Indikator Mutu RS", menu
  (role-filtered), user avatar+name+position at bottom. Active item: blue left
  border 3px + `rgba(66,116,217,0.25)` bg.
- **Navbar** 48px white: page title + live clock (id-ID locale), search box,
  role badge, notification bell with dropdown (kepalaUnit sees filtered list).
- **Footer** 32px: app name left, © year + credits right.

## Roles & menu access

| Menu                    | superAdmin | komiteMutu | kepalaUnit |
|-------------------------|:---:|:---:|:---:|
| Dashboard               | ✓ | ✓ | ✓ (own unit) |
| Logbook                 | ✓ (all units) | ✓ (all units) | ✓ (own unit, input) |
| Kelola Pengguna         | ✓ (all users) | ✓ (cannot see superAdmin) | – |
| Kelola Profil Indikator | ✓ | ✓ | – |

## Screens

### Dashboard (superAdmin / komiteMutu)
- 4 KPI cards: Total Indikator Aktif, Unit Memenuhi Target, Unit Dalam Proses,
  Unit Kritis (di bawah 60%).
- Horizontal bar chart, all units sorted worst→best. Bar color by
  achieved/target ratio: ≥1 green, ≥0.75 amber, else red. Dashed navy
  reference line at 85% target. Tooltip: achievement %.
- Right panel: per-unit progress bars with "filled/total terisi" + %.

### Dashboard (kepalaUnit)
- 3 big-number cards: Belum Diisi (red), Sudah Diisi (green), Pencapaian Unit
  (navy on teal bg).
- Deadline status list per indicator: badge ✓/!/✗, type badge, achieved %,
  countdown ("HARI INI" red, "+N hari"). Logbook window is ≤7 days.
- Monthly achievement bar chart (blue bars) with 85% reference line.

### Logbook (kepalaUnit)
Excel-like dense table, 12 columns:
No | Nama Indikator | Tipe | Target (%) | Tgl Otomatis | Numerator |
Denominator | Hasil Harian (%) | Capaian (%) | Sisa Deadline | Status | Aksi

- Inline edit per row (Edit ↔ Simpan): numerator & denominator become inputs;
  result auto-computed = numerator/denominator × 100 (1 decimal).
- Buttons: "+ Tambah Baris" (outline), "Cetak & Kirim Laporan" (`#4274D9`).
- Status: green=Selesai, yellow=Mendekati, red=Terlambat.

### Logbook (superAdmin / komiteMutu)
- Left sub-sidebar (208px, `#f6fbfd`): unit list, active = teal bg + blue right border.
- 12-month grid selector: past ✓, current month solid blue "Aktif", future disabled.
- Report preview table (No, Nama Indikator, Tipe, Target, Capaian, Status).
- Buttons: Unduh PDF (outline blue), Unduh Excel (green).

### Kelola Pengguna
Table: No | Nama Lengkap | Unit/Departemen | Peran (badge) | Login Terakhir |
Status | Log Aktivitas (Lihat Log) | Aksi (Edit / Reset PW / Nonaktif).
"Nonaktif" button visible to superAdmin only. komiteMutu view excludes
superAdmin accounts. "+ Tambah Pengguna" button top-right.

### Kelola Profil Indikator
- Left sub-sidebar: pillars INM / IMP-RS / IMU with full names.
- Table: No. Indikator (mono, blue) | Nama Indikator | Daftar Unit Pelaksana
  (teal chips) | Aksi (Edit / Hapus). "+ Tambah Indikator" button.

## Seed data in prototype (reference for DB schema)

- 10 units: Farmasi, IGD, ICU, Laboratorium, Radiologi, Rawat Inap A,
  Rawat Inap B, Bedah Sentral, Gizi, CSSD.
- Indicators numbered per pillar: INM-01…, IMP-RS-01…, IMU-F-01 (unit-coded)…
  each mapped to multiple executing units.
- Users have: name+title, unit, role, last login, status Aktif/Nonaktif.
- Logbook rows: numerator, denominator, computed %, deadline countdown, status.
