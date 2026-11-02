# Karhutla Lapor

Aplikasi web komunitas untuk pelaporan dan pemantauan indikasi kebakaran hutan dan lahan (Mini Project Proyek 3 — Kasus B).

Masyarakat (pelapor) mengirim laporan beserta lokasi dan bukti pendukung, relawan pemantau meninjau kelengkapan dan kewajaran informasi, lalu koordinator relawan mencatat penerusan informasi kepada pihak terkait.

> Catatan: Status "Selesai" atau "Tidak Ditindaklanjuti" menandakan proses penanganan pada aplikasi komunitas telah selesai, bukan pernyataan resmi pemadaman bencana.

---

## 1. Identitas

| Komponen             | Keterangan                                             |
| -------------------- | ------------------------------------------------------ |
| **Nama**       | Wildan M Zaki                                          |
| **NIM**        | 251511062                                              |
| **Kelas**      | 2B                                                     |
| **Judul**      | Pelaporan dan Pemantauan Kebakaran Hutan dan Lahan     |
| **Repository** | https://github.com/WildanMZaki/p3-mini-project-uts.git |

---

## 2. Kebutuhan Environment

| Komponen               | Kebutuhan Minimum                                                                                                      |
| ---------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| **PHP**          | 8.3 atau lebih baru                                                                                                    |
| **Ekstensi PHP** | `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd` *(gd digunakan untuk generasi dummy evidence seeder)* |
| **Composer**     | 2.x                                                                                                                    |
| **Database**     | MySQL 8.0+ / MariaDB 10.4+                                                                                             |
| **Framework**    | Laravel 11.x / 12.x / 13.x                                                                                             |

> Node.js tidak wajib untuk menjalankan aplikasi karena styling CSS dan interaksi asynchronous JavaScript telah disertakan langsung pada direktori `public/css/` dan `public/js/`.

---

## 3. Langkah Instalasi

1. Clone repository:

   ```bash
   git clone https://github.com/WildanMZaki/p3-mini-project-uts.git karhutla-lapor
   cd karhutla-lapor
   ```
2. Pasang dependensi PHP:

   ```bash
   composer install
   ```
3. Siapkan environment file:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

---

## 4. Konfigurasi Database

1. Buat database baru di MySQL/MariaDB:

   ```sql
   CREATE DATABASE karhutla_lapor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Sesuaikan konfigurasi koneksi pada file `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=karhutla_lapor
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

## 5. Migration dan Seeding

Jalankan migrasi seluruh tabel database beserta seeder data demo:

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan menyiapkan seluruh data pengujian:

- **6 Akun Demo Pengguna** mencakup seluruh peran teknis.
- **6 Area Wilayah** (5 aktif, 1 nonaktif).
- **5 Instansi Pihak Terkait** (BPBD, Manggala Agni, Damkar, Pemdes, Pengelola Kawasan).
- **30 Laporan Demo** (8 skenario bisnis tetap + 22 laporan acak di semua status).
- **Log Perubahan Status** di tabel `report_status_logs` via Observer.
- **Riwayat Penerusan** di tabel `report_forwardings`.
- **File Bukti Dummy** di disk privat `storage/app/private/evidence/`.

> File bukti pendukung dikelola secara aman di storage privat dan diakses melalui route terautentikasi (`reports.evidence`), sehingga **tidak memerlukan** symlink `php artisan storage:link`.

---

## 6. Akun Demo Pengujian

Semua akun demo menggunakan password default: **`password`**

| Username  | Nama Lengkap   | Peran (Role)        | Keterangan Skenario Uji                          |
| --------- | -------------- | ------------------- | ------------------------------------------------ |
| `andi`  | Andi Pratama   | Pelapor             | Akun pelapor utama                               |
| `citra` | Citra Lestari  | Pelapor             | Akun pelapor kedua (uji isolasi data / privasi)  |
| `sari`  | Sari Wulandari | Relawan Pemantau    | Akun relawan peninjau utama                      |
| `dodi`  | Dodi Saputra   | Relawan Pemantau    | Akun relawan kedua (uji *reviewer lock* BR-03) |
| `budi`  | Budi Santoso   | Koordinator Relawan | Meneruskan laporan ke instansi & menutup proses  |
| `eko`   | Eko Nonaktif   | Relawan Pemantau    | Akun nonaktif (uji verifikasi login ditolak)     |

---

## 7. Cara Menjalankan Aplikasi

1. Jalankan development server lokal:
   ```bash
   php artisan serve
   ```
2. Buka browser di [http://127.0.0.1:8000](http://127.0.0.1:8000)
3. Masuk dengan salah satu akun demo di atas.

Untuk menjalankan rangkaian Automated Feature Testing:

```bash
php artisan test
```

---

## Business Rules yang Diterapkan

| Kode            | Aturan Bisnis                                                                                                                                                                           | Mekanisme & Lokasi Penegakan                                                  |
| --------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------- |
| **BR-01** | Pelapor hanya dapat mengubah atau menghapus laporannya saat status `Baru` atau `Perlu Perbaikan`. Setelah ditinjau relawan, laporan terkunci.                                       | `ReportPolicy@update/delete` + `ReportStatus::isEditableByReporter()`     |
| **BR-02** | Perubahan status wajib mematuhi state machine transisi yang sah; status `Selesai` atau `Tidak Ditindaklanjuti` bersifat final.                                                      | `ReportWorkflowService::transition()` + `ReportStatus::canTransitionTo()` |
| **BR-03** | Laporan yang sedang ditinjau oleh seorang relawan tidak dapat diambil alih atau diputuskan oleh relawan lain (*reviewer lock*).                                                       | `ReportWorkflowService::claim()/review()` + `ReportPolicy@review`         |
| **BR-04** | Penerusan hanya dapat dicatat untuk laporan berstatus `Perlu Diteruskan` atau `Sudah Diteruskan`, dan instansi yang sama tidak boleh dicatat berulang kali untuk laporan yang sama. | `ForwardingService` + Unique database constraint `(report_id, agency_id)` |

---

## Alur Transisi Status Laporan

```
baru ──► ditinjau ──► perlu_diteruskan ──► diteruskan ──► selesai
 ▲          │
 │          ├──► perlu_perbaikan ──(pelapor memperbaiki)──┐
 │          │                                             │
 └──────────┼─────────────────────────────────────────────┘
            └──► ditolak
```
