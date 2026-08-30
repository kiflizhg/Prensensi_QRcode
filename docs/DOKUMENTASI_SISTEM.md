# Dokumentasi Sistem Presensi QR Code

## 1. Ringkasan Sistem
Sistem Presensi QR Code adalah aplikasi berbasis web yang bertujuan untuk mengelola kehadiran guru secara digital menggunakan teknologi pemindaian QR Code. Permasalahan utama yang diselesaikan adalah pencatatan kehadiran yang lebih akurat, cepat, serta meminimalisir manipulasi absen. Pengguna sistem ini meliputi Administrator, Guru, dan Kepala Sekolah. Data utama yang dikelola meliputi data pengguna, data guru, kartu/token QR, data kehadiran (presensi), serta pengajuan izin/sakit/cuti. Keluaran utama sistem adalah rekapitulasi kehadiran harian, monitoring kehadiran real-time, dan riwayat presensi guru.

## 2. Teknologi yang Digunakan
Berdasarkan analisis file konfigurasi (`composer.json`, `package.json`, `.env.example`, dll), teknologi yang digunakan adalah:
- **Backend Framework**: Laravel v12.0
- **PHP Requirement**: PHP >= 8.2
- **Database**: SQLite (dilihat dari skrip `post-create-project-cmd` pada composer yang membuat `database.sqlite`)
- **Frontend / Styling**: Tailwind CSS v4, dikompilasi menggunakan Vite
- **Autentikasi**: Laravel Auth bawaan dengan pembatasan single-session khusus Admin dan Kepala Sekolah.
- **QR Code Library (Backend)**: `simplesoftwareio/simple-qrcode` v4.2 (digunakan untuk mencetak SVG QR Code).
- **QR Code Library (Frontend)**: `html5-qrcode` v2.3.8 (digunakan untuk fitur pemindaian di web).
- **PDF Library**: `barryvdh/laravel-dompdf` v3.1 (tersedia di composer, kemungkinan untuk cetak laporan).
- **Testing**: PHPUnit v11.5.

## 3. Pengguna dan Hak Akses

| Role | Dashboard | Menu/Fitur Utama | Hak Akses | Pembatasan | Bukti File |
|------|-----------|------------------|-----------|-------------|------------|
| Admin | `/admin/dashboard` | Kelola Guru, Presensi, Notifikasi, Laporan | Read, Create, Update, Delete data guru; Akses halaman scan terminal | Tidak memiliki wewenang untuk menyetujui pengajuan izin | `routes/web.php` (admin), `App\Http\Controllers\Admin\*` |
| Guru | `/guru/dashboard` | Riwayat Presensi, Pengajuan Izin/Sakit | Read data sendiri, Create pengajuan izin | Hanya bisa melihat data milik sendiri, tidak bisa menambah guru | `routes/web.php` (guru), `App\Http\Controllers\Guru\*` |
| Kepala Sekolah | `/kepsek/dashboard` | Monitoring Presensi, Persetujuan Pengajuan | Read rekap, Approve/Reject pengajuan izin guru | Tidak bisa menambah/mengubah master data guru | `routes/web.php` (kepsek), `App\Http\Controllers\KepalaSekolah\*` |

**Mekanisme Otorisasi (Middleware):**
Aplikasi menggunakan middleware bawaan Laravel (`auth`) yang digabungkan dengan middleware spesifik role (`admin`, `guru`, `kepsek`) di setiap route file.

## 4. Gambaran Alur Sistem
1. Admin mendaftarkan data guru baru.
2. Sistem otomatis membuatkan akun login dan menghasilkan Token QR unik.
3. Guru mengunduh Kartu QR mereka dari sistem.
4. Guru melakukan pemindaian QR Code di perangkat scanner yang disediakan (halaman `/scan`).
5. Sistem memvalidasi QR, mendeteksi jenis scan (masuk/pulang), dan mencatat waktu presensi ke dalam database.
6. Admin dan Kepala Sekolah dapat memantau data kehadiran secara langsung di dashboard/monitoring.
7. Jika Guru berhalangan hadir, mereka dapat membuat Pengajuan (Izin/Sakit/Cuti).
8. Kepala Sekolah menyetujui pengajuan tersebut, dan sistem akan otomatis mengisi status kehadiran Guru pada tanggal yang diajukan.

## 5. Alur Autentikasi
Alur berdasarkan `App\Http\Controllers\Auth\LoginController.php`:
- **Login Page**: Pengguna membuka halaman `/login`.
- **Validasi Input**: Memasukkan *login* (bisa berupa email atau username) dan *password*.
- **Authentication**: `Auth::validate()` mengecek kredensial. Terdapat pengecekan khusus apakah `is_active = true`.
- **Single-Session (Khusus Admin/Kepsek)**: Jika pengguna adalah Admin atau Kepsek, sistem akan mengecek field `active_session_id`. Jika sesi lain sedang aktif, login ditolak dengan pesan error.
- **Sukses Login**: Jika berhasil, sesi diregenerasi, `active_session_id` disimpan (untuk admin/kepsek), dan pengguna diarahkan ke rute `/dashboard`. Rute ini (`routes/web.php`) akan melakukan deteksi role dan *redirect* ke dashboard masing-masing (`/admin/dashboard`, `/guru/dashboard`, atau `/kepsek/dashboard`).
- **Logout**: Memutus sesi dan mengosongkan `active_session_id` pada tabel *users*.

## 6. Pengelolaan Guru
Fungsionalitas ini berada di `App\Http\Controllers\Admin\GuruController.php` dan `App\Services\QRCodeService.php`.
- **Tambah Guru**: Admin mengisi formulir (NIP, nama, dll). Sistem akan menyimpan data ke tabel `gurus` dan secara otomatis membuatkan akun pada tabel `users`.
- **Kredensial Default**: Akun `users` dibuat menggunakan field `username` (input form), `email` (hasil generate format `username@sma-cipasung.local`), dan `password` default (menggunakan NIP guru).
- **Pembuatan QR**: Setelah data guru tersimpan, `QRCodeService->ensureGuruToken()` berjalan. Ia membuat token string acak sepanjang 48 karakter dengan prefix `GURU-` dan menyimpannya di kolom `token_qr` pada tabel `gurus`.
- **Ubah/Hapus Guru**: Pengubahan data guru akan mengubah data profil dan user terkait. Penghapusan akan menghapus data di `gurus` dan `users` secara berjenjang (melalui *database transaction*).
- **Cetak Kartu QR**: `Admin\GuruCardController.php` menangani pencetakan kartu. Menggunakan `html-download` berformat HTML dengan SVG QR code (tidak menggunakan PDF library).

## 7. QR Code dan QR Card
- **Siapa yang menghasilkan QR**: Sistem di sisi backend (`QRCodeService`).
- **Library**: `simplesoftwareio/simple-qrcode`.
- **Payload QR**: Merupakan *raw token* (misal: `GURU-ABC123XYZ...`). Token ini unik untuk tiap guru dan disimpan di database `gurus`.
- **Pembuatan QR Card**: Dibuat dalam format file `.html` (fitur unduh di `GuruCardController`), bukan `.pdf`.

## 8. Pemindaian dan Pencatatan Presensi
- **Halaman Pemindaian**: Berada pada rute `/scan` (publik, `ScanController@index`). Kemungkinan juga menggunakan `html5-qrcode` untuk integrasi kamera.
- **Request ke Backend**: Memukul rute `POST /scan` (dibatasi 60 request/menit). Input berupa `qr_code` dan `jenis_scan` (otomatis, masuk, pulang).
- **Validasi Token**: Sistem mengambil *basename* dari URL jika QR memuat path URL penuh, kemudian mengecek token di tabel `gurus`.
- **Pengecekan Izin**: Sebelum mencatat hadir, `PresensiService` mengecek apakah guru memiliki izin disetujui hari tersebut. Jika ada, scan ditolak (terjadi bentrok).
- **Proses Presensi**: Jika valid, sistem memanggil fungsi `absenMasuk` atau `absenPulang`. Sistem juga melakukan pengecekan `tentukanJenisScanOtomatis` apabila input dari scanner adalah *otomatis*.
- **Pencegahan Duplikat**: Berjalan dengan validasi kondisi, dimana guru tidak bisa `absenPulang` jika belum `absenMasuk`. Data unik dijaga oleh constraints `unique(['guru_id', 'tanggal'])` di skema `presensis`.
- **Output**: JSON balasan berupa status kehadiran, sapaan suara (`speech`), nama guru, dan jam masuk/pulang. Terdapat penanganan *Error Logging* jika gagal.

## 9. Status Kehadiran
Status kehadiran dikontrol dalam tabel `presensis` pada migrasi `2026_06_01_184729_create_presensis_table.php`.
- **Allowed Statuses**: `belum_presensi`, `hadir`, `izin`, `sakit`, `cuti`, `dinas_luar`, `alpa`, `alfa`.
- **Check-In**: Menyimpan `jam_masuk` pada jam saat scan.
- **Check-Out**: Menyimpan `jam_pulang` pada jam saat scan.
- **Keterlambatan (Lateness)**: `PresensiService` menggunakan konstanta `BATAS_MASUK_NORMAL` ('07:30:00'). Jika guru scan masuk di atas jam tersebut, *tidak ada status 'terlambat' secara enum*, melainkan field `keterangan` akan diisi kalimat: "Terlambat scan masuk pukul {jam}.".
- **Status Alfa/Alpa**: Terdapat pada enum migrasi, kemungkinan dipakai pada saat penutupan hari atau *cron job* untuk guru yang tidak presensi (Tidak dapat dipastikan tanpa menjalankan aplikasi/mengecek schedule commands lengkap).

## 10. Izin, Sakit, Cuti, dan Persetujuan
- **Pihak yang Mengajukan**: Guru, melalui menu Pengajuan (`App\Http\Controllers\Guru\PengajuanController`).
- **Formulir**: Memasukkan `jenis` (izin, sakit, cuti, dll), `tanggal_mulai`, `tanggal_selesai`, `alasan`, dan `lampiran` file jika ada.
- **Pihak yang Menyetujui**: Kepala Sekolah, melalui `App\Http\Controllers\KepalaSekolah\PersetujuanController`.
- **Siklus Persetujuan**: Menunggu $\rightarrow$ Disetujui/Ditolak.
- **Sinkronisasi Otomatis**: Jika disetujui, `PresensiService->sinkronkanPengajuanDisetujui()` berjalan. Metode ini akan melakukan perulangan berdasarkan selang hari `tanggal_mulai` hingga `tanggal_selesai` (menggunakan `CarbonPeriod`) dan memasukkan/mengganti rekor `presensis` guru dengan status sesuai (izin/sakit/cuti) dan *metode input* = "Form Web". Ini menggunakan `upsert` massal ke database.
- **Lampiran Dokumen**: Terdapat pengunduhan lampiran di route `kepsek.persetujuan.lampiran`.

## 11. Dashboard Admin
- Berdasarkan `App\Http\Controllers\Admin\DashboardController.php`, Admin memiliki akses tampilan awal pasca-login. (Fitur widget spesifik hanya terdapat di View).
- **Tersedia**: Halaman profil, manajemen guru, manajemen presensi, notifikasi.

## 12. Dashboard Guru
- Berdasarkan `App\Http\Controllers\Guru\DashboardController.php`.
- **Tersedia**: Rekapitulasi riwayat presensi, pengajuan absen, notifikasi profil.

## 13. Dashboard Kepala Sekolah
- Berdasarkan `App\Http\Controllers\KepalaSekolah\DashboardController.php`.
- **Tersedia**: Fitur monitoring presensi, persetujuan pengajuan absen guru, laporan.

*(Rincian visual/grafik/filter dashboard di atas tidak dapat dipastikan 100% tanpa membuka browser/menjalankan aplikasi, karena dikontrol penuh oleh blade view).*

## 14. Monitoring dan Laporan
- **Monitoring Harian**: Ditemukan pada `App\Http\Controllers\KepalaSekolah\MonitoringController`. Mengambil daftar semua guru aktif dan mengecek status presensinya pada hari ini secara *real-time*. Jika belum scan, sistem mencetak status bayangan `belum_presensi` untuk view. *(Tersedia)*.
- **Laporan/Ekspor File**: Ditemukan pada rute `kepsek.laporan.download` dan `admin.laporan.kirim`. Berhubungan dengan `LaporanController`. (PDF/Cetak: *Tersedia, mengacu pada laravel-dompdf di composer*).

## 15. Pemetaan Route
Tabel rute utama (berdasarkan keluaran `php artisan route:list`):

| No | Method | URL | Route Name | Controller | Method | Middleware | Role | Output |
|----|--------|-----|------------|------------|--------|------------|------|--------|
| 1 | GET | `/login` | `login` | `Auth\LoginController` | `create` | `guest` | Guest | Tampilan Login |
| 2 | POST | `/login` | `login.store` | `Auth\LoginController` | `store` | `guest` | Guest | Redirect Dashboard |
| 3 | POST | `/logout` | `logout` | `Auth\LoginController` | `destroy` | `auth` | All | Redirect Homepage |
| 4 | GET | `/scan` | `scan.index` | `ScanController` | `index` | `web` | Guest | Tampilan Scanner |
| 5 | POST | `/scan` | `scan.store` | `ScanController` | `store` | `throttle:60,1` | Guest | JSON (Validasi Absen) |
| 6 | GET | `/dashboard` | `dashboard` | `Closure` | - | `auth` | All | Redirect Role |
| 7 | GET | `/admin/guru` | `admin.guru.index` | `Admin\GuruController` | `index` | `admin` | Admin | Tabel Guru |
| 8 | POST | `/admin/guru` | `admin.guru.store` | `Admin\GuruController` | `store` | `admin` | Admin | Simpan Guru Baru |
| 9 | GET | `/guru/pengajuan` | `guru.pengajuan.index`| `Guru\PengajuanController`| `index` | `guru` | Guru | Form & Tabel Pengajuan |
| 10 | POST | `/kepsek/persetujuan/{id}/setujui` | `kepsek.persetujuan.setujui` | `KepalaSekolah\PersetujuanController` | `setujui` | `kepsek` | Kepsek | Setujui & Update Presensi |
| 11 | GET | `/kepsek/monitoring`| `kepsek.monitoring.index`| `KepalaSekolah\MonitoringController` | `index` | `kepsek` | Kepsek | Tabel Monitoring Real-time|

## 16. Pemetaan Controller

| Controller | Fungsi Utama | Route/Fitur | Model/Service yang Digunakan |
|------------|--------------|-------------|------------------------------|
| `LoginController` | Autentikasi Pengguna & Sesi Tunggal | `/login`, `/logout` | `User`, `Auth` |
| `ScanController` | Endpoint Publik Pemindaian QR | `/scan` (POST & GET) | `PresensiService`, `Guru` |
| `GuruController` | CRUD Data Guru (Admin) | `/admin/guru/*` | `Guru`, `User`, `QRCodeService` |
| `GuruCardController` | Melihat dan Mengunduh ID Card HTML | `/admin/guru/*/kartu` | `Guru`, `QRCodeService` |
| `PengajuanController` | Proses input pengajuan (Izin/Sakit) oleh Guru | `/guru/pengajuan/*` | `Pengajuan`, `Guru` |
| `PersetujuanController` | Persetujuan Cuti/Sakit oleh Kepsek | `/kepsek/persetujuan/*` | `Pengajuan`, `PresensiService` |
| `MonitoringController` | Memonitor status absen harian | `/kepsek/monitoring` | `Guru`, `Presensi` |

## 17. Model dan Database

| Tabel | Fungsi | Kolom Penting | Primary Key | Foreign Key | Relasi | Digunakan Pada |
|-------|--------|---------------|-------------|-------------|--------|----------------|
| `users` | Akun Autentikasi | `name`, `username`, `email`, `role`, `is_active`, `active_session_id` | `id` | - | 1 to 1 (`Guru`) | `LoginController`, `GuruController` |
| `gurus` | Master Data Guru | `nip`, `nama`, `mata_pelajaran`, `token_qr`, `status` | `id` | `user_id` | 1 to M (`Presensi`, `Pengajuan`) | `GuruController`, Manajemen Master |
| `presensis` | Catatan Kehadiran Harian | `tanggal`, `jam_masuk`, `jam_pulang`, `status`, `metode_input`, `keterangan` | `id` | `guru_id` | BelongsTo (`Guru`) | `ScanController`, `MonitoringController` |
| `pengajuans` | Record Cuti/Izin/Sakit | `jenis`, `tanggal_mulai`, `tanggal_selesai`, `alasan`, `status`, `disetujui_oleh` | `id` | `guru_id`, `disetujui_oleh` | BelongsTo (`Guru`, `User` as approver) | `PengajuanController`, `PersetujuanController` |
| `notifikasis` | Pesan / Pemberitahuan | `judul`, `pesan`, `is_read` | `id` | `user_id` | BelongsTo (`User`) | Modul Notifikasi |

*Catatan: Tabel `presensi_qr_codes` ditemukan dalam migrasi, namun tidak dipakai dominan pada alur `PresensiService`.*

## 18. Pemetaan Fitur: Input, Proses, Output

| No | Fitur | Input | Proses | Output | Role | Route | Database | Status |
|----|-------|-------|--------|--------|------|-------|----------|--------|
| 1 | Login Sistem | Email/Username, Password | Validasi Auth, cek `is_active` dan `active_session_id` | Redirect ke Dashboard | Semua | `/login` | `users` | Berfungsi berdasarkan kode |
| 2 | Tambah Guru Baru | Form Data Diri Guru | Menyimpan record `gurus`, `users`, generate `token_qr` acak | Pesan Sukses | Admin | `/admin/guru` | `gurus`, `users` | Berfungsi berdasarkan kode |
| 3 | Download Kartu QR | ID Guru | Ambil token QR, SVG Render | HTML File Download | Admin | `admin.guru.kartu.download`| `gurus` | Berfungsi berdasarkan kode |
| 4 | Scan Kehadiran | Teks QR Code dari kamera | Validasi UUID, Lookup Token, Cek status Izin, Tulis Waktu Hadir | JSON Respons (Speech text, jam) | Tamu/Semua | `/scan` | `gurus`, `presensis` | Berfungsi berdasarkan kode |
| 5 | Pengajuan Izin | Rentang waktu, Alasan, Lampiran | Buat record `pengajuans` berstatus menunggu | Pesan Sukses | Guru | `/guru/pengajuan`| `pengajuans` | Berfungsi berdasarkan kode |
| 6 | Approval Izin | ID Pengajuan, Keputusan | Ubah status `disetujui`, loop `CarbonPeriod` insert ke `presensis` | Pesan Sukses | Kepsek | `/kepsek/persetujuan/{id}/setujui` | `pengajuans`, `presensis`| Berfungsi berdasarkan kode |
| 7 | Monitoring Harian | - | Loop semua guru `aktif`, periksa status record `presensis` harian | Tabel UI View | Kepsek | `/kepsek/monitoring` | `gurus`, `presensis` | Berfungsi berdasarkan kode |

## 19. Use Case
Kode seperti *UC19*, *UC24* tidak ditemukan secara tertulis dalam source code. Identifier use case berikut adalah identifier *dokumentasi (rekonstruksi)* untuk memetakan arsitektur yang sudah berjalan:

| Kode | Nama Use Case | Aktor | Input | Proses | Output | Route | Bukti |
|------|---------------|-------|-------|--------|--------|-------|-------|
| UC-AUTH-01 | Single Session Login | Admin, Kepsek | Kredensial | Mengecek `active_session_id`, membatasi sesi ganda | Redirect Dashboard / Tolak login | `/login` | `LoginController.php` |
| UC-ADMIN-01| Pembuatan Akun Guru & QR | Admin | NIP, Nama, Jabatan | Save `users`, Save `gurus`, Trigger `QRCodeService` | Akun Terbuat + Token GURU-xxx | `admin.guru.store` | `GuruController.php` |
| UC-GURU-01 | Permohonan Cuti/Izin | Guru | Tanggal, Alasan | Create `pengajuans` default `menunggu` | Form tersimpan | `guru.pengajuan.store`| `PengajuanController.php` |
| UC-SCAN-01 | Absen Masuk Otomatis | Sistem Publik | QR Scanner | Pengecekan Token, Waktu Jam Masuk (cek terlambat jam 07:30) | Record kehadiran baru / Terlambat | `/scan` | `ScanController.php`, `PresensiService.php` |
| UC-KEP-01 | Persetujuan Izin (Sinkronisasi) | Kepsek | ID Pengajuan | Status dirubah, *Bulk Upsert* ke tabel `presensis` | Izin masuk pada absen harian | `kepsek.persetujuan.setujui`| `PersetujuanController.php` |

## 20. Fitur yang Tersedia
Fitur-fitur ini dapat dipastikan benar-benar ada dan berjalan di *backend* (berdasarkan file Controllers dan Services):
- Autentikasi dan Otorisasi Multi-Level (Admin, Guru, Kepala Sekolah).
- Single-Session Validation untuk Admin dan Kepsek.
- Master Data Manajemen Guru (Otomatisasi Akun Login).
- Pembuatan QR Token Unik `(GURU-{string})` secara otomatis.
- Cetak ID Card Guru beserta SVG QR.
- Public Scan Endpoint berbasis JSON Response (mengakomodasi hardware scan maupun web-cam).
- Manajemen Logika Keterlambatan Otomatis (pukul 07:30 batas masuk).
- Pengajuan Izin/Sakit/Cuti beserta Fitur Lampiran.
- Mekanisme Persetujuan/Tolak oleh Kepala Sekolah beserta Injeksi Data Presensi Otomatis.
- Monitoring Kehadiran "Hari Ini" bagi Kepala Sekolah.

## 21. Fitur yang Belum Lengkap atau Tidak Ditemukan
- **Hardware Integrasi**: Tidak ditemukan perintah khusus untuk berinteraksi dengan API Mesin IoT/Fingerprint dalam source code; Scanner murni menggunakan parameter URL POST form biasa (kemungkinan memakai web cam di view frontend).
- **Notifikasi Email/WA**: Meski ada tabel `notifikasis`, tidak ditemukan sinkronisasi pengiriman eksternal (menggunakan `Mail` atau `Twilio/Wablas`) saat presensi disetujui. Notifikasi hanya berupa data di database (In-app notifications).
- **Status Alfa Otomatis**: Tidak ditemukan *Cron Job* (`app/Console/Commands/` atau `routes/console.php` yang terisi) yang secara berkala memasukkan guru berstatus "Alfa" jika tidak scan sampai waktu pulang selesai. Status Alfa murni tersedia di Enum database.

## 22. Bagian yang Memerlukan Verifikasi
- **Tampilan Visual / Filter Laporan**: Grafik chart pada Dashboard, rentang periode filter, dan hasil output cetak tabel PDF memerlukan eksekusi *runtime* (dibuka melalui browser) karena berada di dalam file `.blade.php`.
- **Scan Kamera Frontend**: Mekanisme `html5-qrcode` memanipulasi *DOM browser*, ini perlu diuji langsung di perangkat untuk memastikan pemanggilan `POST /scan` berjalan mulus pasca-pembacaan bingkai kamera.

## 23. Kesimpulan Cara Kerja Sistem
Sistem presensi ini menggunakan pendekatan sentralisasi data guru dengan sistem *Self-Service QR*. Saat Admin mendaftarkan guru, sistem merangkai entitas `User`, `Guru`, dan `QR Token`. Pemindaian kehadiran dilakukan dari halaman publik (dibatasi *throttle* rate-limit) menggunakan API berbasis JSON, sehingga cocok dipakai via tablet stand atau perangkat kamera web sekolah. Sistem menaruh logika absensi berat pada layer Service (`PresensiService`) untuk menangani konflik izin-absensi, pengecekan keterlambatan otomatis, dan validasi jam pulang. Hak persetujuan mutlak dipegang oleh Kepala Sekolah, dimana persetujuan izin otomatis memodifikasi (upsert) kalender absensi harian guru.
