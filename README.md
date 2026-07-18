# SIMIM — Sistem Informasi Profil Indikator Mutu

Sistem informasi untuk memantau **profil indikator mutu** tiap unit di
RS Jiwa Tampan. Dibangun dengan **Yii 2 (basic template)** dan **MySQL**.

Terdapat 3 jenis indikator mutu yang dipantau:

| Kode | Jenis |
|------|-------|
| INM | Indikator Nasional Mutu |
| IMP-RS | Indikator Mutu Prioritas Rumah Sakit |
| IMU | Indikator Mutu Unit |

## Peran Pengguna

| Peran | Hak akses |
|-------|-----------|
| `super_admin` | Semua fitur tanpa kecuali, termasuk membuat akun admin |
| `admin` (Komite Mutu) | Kelola indikator + pantau hasil seluruh unit; **tidak bisa** melihat akun super admin / admin lain |
| `kepala_unit` | Dashboard unitnya sendiri + mengisi logbook unitnya |

## Aturan Bisnis Utama

- Kepala unit mengisi **numerator (N)** dan **denominator (D)** per tanggal
  pada logbook (tampilan seperti Excel: tanggal menjadi kolom).
- Persentase dihitung otomatis: `N / D × 100%`.
  Capaian bulanan = `SUM(N) / SUM(D) × 100%`.
- **Batas pengisian logbook 7 hari** — tanggal yang sudah lewat lebih dari
  7 hari tidak bisa diisi lagi.
- Dashboard admin: grafik per unit — **hijau** jika capaian memenuhi target,
  **merah** jika belum.
- Dashboard kepala unit: status tiap indikator — **hijau** (sudah diisi),
  **kuning** (mendekati batas pengisian), **merah** (melewati batas).

## Kebutuhan Sistem

- PHP >= 8.1 (dikembangkan dengan PHP 8.3)
- MySQL / MariaDB
- [Composer](https://getcomposer.org/)

## Cara Replikasi (Instalasi)

```bash
# 1. Clone / salin proyek, lalu masuk ke foldernya
cd SIMIM

# 2. Pasang dependensi
composer install
# (jika gagal karena versi PHP berbeda dengan lock file, jalankan: composer update)

# 3. Buat database kosong
mysql -u root -p -e "CREATE DATABASE db_simim CHARACTER SET utf8mb4"

# 4. Sesuaikan koneksi database di config/db.php
#    (host, dbname, username, password)

# 5. Jalankan migrasi — membuat semua tabel + data awal
php yii migrate --interactive=0

# 6. Jalankan server development
php yii serve
# buka http://localhost:8080
```

Untuk mengulang database dari nol: `php yii migrate/fresh`.

## Akun Bawaan (hasil seed)

| Username | Password | Peran |
|----------|----------|-------|
| `superadmin` | `password123` | Super Admin |
| `komitemutu` | `password123` | Admin (Komite Mutu) |
| `igd` | `password123` | Kepala Unit IGD |

> Ganti password bawaan ini sebelum dipakai sungguhan. Akun kepala unit
> lainnya dibuat melalui menu Kelola Pengguna.

## Struktur Database

Skema dibuat lewat file migrasi di folder `migrations/`:

| Tabel | Isi |
|-------|-----|
| `unit` | 24 unit kerja RS (IGD, FARMASI, RANAP, ...) — dari sheet Excel logbook |
| `user` | Pengguna + kolom `role`; `unit_id` diisi hanya untuk kepala unit |
| `indikator` | Master indikator: nama, jenis (INM/IMP-RS/IMU), target %, arah target (`>=` makin tinggi makin baik, `<=` sebaliknya) |
| `indikator_unit` | Penugasan indikator ke unit (many-to-many) |
| `logbook` | Isian harian N & D per penugasan per tanggal (unik per indikator-unit + tanggal) |

Diagram relasi singkat:

```
unit ──< indikator_unit >── indikator
              │
              └──< logbook >── user (diisi_oleh)
user >── unit (kepala unit)
```

Data awal (seed) diekstrak dari `LOGBOOK VALIDASI 2026.xlsx`:
24 unit, 53 indikator unik, 96 penugasan indikator-unit.

## Referensi Desain UI

Desain antarmuka (SIMUTURS) tersimpan di folder `design-reference/`:
- `DESIGN.md` — spesifikasi warna, tipografi, layout, dan tiap halaman
- `src/App.tsx` — prototipe Figma Make (React) sebagai acuan tampilan
