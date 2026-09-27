# Dokumentasi Sistem

## Profil guru dan kewenangan kata sandi

Guru dapat memperbarui nama, nama pengguna, alamat surel, foto, nomor HP, dan alamat melalui Profil Saya. Nama akun (`users.name`) dan nama guru (`gurus.nama`) diperbarui dalam transaksi yang sama. Data Guru di admin membaca langsung akun dan data guru tersebut, termasuk foto dan surel. Perubahan terlihat ketika halaman admin dibuka atau dimuat ulang, tanpa input ulang oleh admin. Pembaruan data guru oleh admin mempertahankan surel yang telah dipilih guru.

Guru tidak memiliki formulir atau rute ubah kata sandi. Permintaan kata sandi yang disisipkan ke pembaruan profil ditolak. Admin tetap mengelola kata sandi guru melalui Ubah Data Guru. NIP, status, mata pelajaran, jadwal, QR, dan peran akun tidak dapat diubah melalui profil guru.

## Desain layanan guru untuk HP dan PC

Guru menggunakan struktur header, navigasi, dan area konten bersama dengan admin, tetapi memiliki tampilan tersendiri melalui `public/assets/css/guru.css` dan kelas `guru-body`. Warna biru kehijauan lembut, kartu dengan sudut membulat, ukuran teks, dan tombol sentuh membedakannya dari administrasi. Stylesheet ini hanya dimuat untuk guru.

Pada layar hingga 980 px tersedia navigasi bawah: Beranda, Jadwal, Presensi, Pengajuan, dan Profil. Menu lengkap di atas tetap memberi akses ke riwayat dan arahan. Navigasi bawah memakai ruang tersendiri serta dukungan safe-area agar tidak menutupi konten. Tombol utama memiliki tinggi minimal 48 px, kolom formulir berukuran teks 16 px, dan pembesaran browser tidak dinonaktifkan.

Jadwal ditampilkan sebagai kartu per hari: dua kolom pada PC dan satu kolom pada HP, dengan penanda hari ini dan jam WIB. Riwayat presensi dan pengajuan memakai tabel pada PC serta kartu berlabel pada HP. Beranda menjelaskan bahwa presensi dilakukan dengan kartu QR di terminal sekolah; menu Presensi hanya menampilkan hasil pencatatan. Pengajuan menyediakan label kolom yang jelas dan mempertahankan isian ketika validasi gagal.

Pengujian portal guru lulus (3 pengujian, 85 assertions), build Vite dan kompilasi Blade berhasil. Tampilan dirancang responsif untuk Android, iPhone, dan PC, tetapi belum diuji visual pada perangkat fisik atau browser dalam sesi ini.

## Portal guru terhubung dengan administrasi sekolah

Seluruh halaman guru menggunakan kerangka layout yang sama dengan admin melalui `layouts.guru` yang mewarisi `layouts.admin`. Header, navigasi, warna, area gulir, menu HP, dan jam WIB menggunakan komponen bersama. Menu dan tautan profil dipilih sesuai peran pengguna; guru tidak diberi akses pengelolaan admin.

Menu guru: Beranda, Jadwal Saya, Presensi Saya, Pengajuan, Riwayat Presensi, Arahan Kepala Sekolah, dan Profil Saya. Beranda menampilkan jadwal hari ini, status presensi, jumlah pengajuan, dan tautan layanan.

Halaman `/guru/jadwal` membaca `gurus.jadwal` melalui `JadwalGuruService`, yaitu sumber yang sama dengan pengaturan admin dan pemeriksaan presensi. Tujuh hari ditampilkan dengan status, batas masuk, dan mulai pulang dalam WIB. Hari ini ditandai. Hari tidak aktif tidak menampilkan jam seolah-olah dapat digunakan untuk presensi. Tersedia pemberitahuan untuk jadwal kosong, data guru belum terhubung, serta akun/status guru tidak aktif.

Jadwal hanya dapat dilihat oleh guru pemilik akun. Tidak ada formulir atau endpoint pembaruan jadwal bagi guru. Perubahan admin terlihat ketika halaman guru dibuka atau dimuat ulang; tidak ada penyalinan jadwal ke penyimpanan terpisah.

Validasi: 33 pengujian lulus (342 assertions), termasuk perubahan jadwal oleh admin, isolasi data guru, penolakan perubahan jadwal oleh guru, dan seluruh halaman guru dengan navigasi sesuai peran. Build aset serta kompilasi Blade berhasil. Pemeriksaan visual langsung di browser belum tersedia.

## Perbaikan kestabilan layout dan kata sandi guru

Kerangka admin menggunakan tinggi layar yang tetap, baris header tersendiri, serta ruang konten yang menggulir. Sidebar tidak lagi menggunakan tinggi layar penuh di bawah header; menu hanya menggulir jika ruang layar tidak mencukupi. Ruang bilah gulir konten dan lebar jam tetap dicadangkan agar posisi halaman tidak bergeser. Pada HP, menu dan konten tetap mempunyai batas tinggi yang jelas.

Formulir tambah dan ubah guru memakai urutan identitas, akun, lalu jadwal. Kata sandi dan konfirmasi berada pada satu baris di desktop dan tersusun vertikal pada HP.

Admin menentukan kata sandi akun guru sesuai kesepakatan sekolah. Akun baru wajib memiliki kata sandi dan konfirmasi yang sama, tanpa batas minimal 8 karakter atau syarat kombinasi karakter. Batas panjang validasi 72 karakter mengikuti penyimpanan yang ada. Sistem tidak lagi membuat kata sandi otomatis dari NIP, termasuk saat membuat akun untuk guru lama. Pada pembaruan akun yang sudah ada, kata sandi kosong berarti mempertahankan kata sandi lama. Kata sandi tetap disimpan sebagai hash.

## Pembaruan layout portal admin — 26 September 2026

Portal admin memakai gaya akademik sederhana dengan warna putih, abu-abu, dan biru tua. Panel sambutan bergambar sudah dihapus. Beranda berisi ringkasan data, grafik, dan tautan administrasi. Data Guru disusun sebagai daftar identitas ringkas; kode QR dibuka melalui bagian yang dapat dilipat. Formulir, jadwal, presensi, laporan, arahan, dan profil mengikuti gaya yang sama.

Jam, hari, dan tanggal pada header bergerak otomatis menggunakan zona `Asia/Jakarta` (WIB) dan bahasa Indonesia. Waktu awal berasal dari server; beranda menyinkronkannya kembali bersama grafik setiap 15 detik. Angka ringkasan juga diperbarui agar presensi hari ini mengikuti pergantian tanggal.

Grafik beranda menampilkan **lima hari kerja, Senin sampai Jumat pada minggu berjalan**. Sabtu dan Minggu tetap menampilkan minggu tersebut; Senin memulai minggu baru. Hari mendatang dan hari tanpa presensi bernilai nol. Grafik menghitung guru yang mempunyai jam masuk. Grafik laporan tetap mengikuti periode laporan mingguan atau bulanan yang dipilih.

Istilah antarmuka, navigasi halaman, serta pesan validasi formulir admin menggunakan bahasa Indonesia. Nama teknis kolom database dan rute tetap dipertahankan. Tampilan HP menggunakan menu lipat, susunan vertikal, serta tabel yang dapat digeser.

CSS dan JavaScript layout admin menggunakan versi berdasarkan waktu perubahan file agar browser mengambil aset terbaru. Seluruh 29 pengujian PHP lulus (245 assertions), termasuk pergantian minggu dan pemuatan halaman admin. Pengujian JavaScript jam berhasil melewati Sabtu, Minggu, dan Senin tanpa memuat ulang. Build Vite dan kompilasi Blade berhasil. Pemeriksaan visual browser belum tersedia pada sesi ini.

## Pembaruan admin — 25 September 2026

Antarmuka admin memakai layout `resources/views/layouts/admin.blade.php`, navigasi dan header di `resources/views/admin/partials/`, stylesheet `public/assets/css/admin.css`, serta perilaku menu HP di `public/assets/js/admin-navigation.js`.

Menu aktif: Beranda, Data Guru, Jadwal Guru, Presensi, Laporan, Arahan Kepala Sekolah, dan Profil Saya. Navigasi dikelompokkan menjadi ringkasan, administrasi akademik, serta komunikasi dan akun. Pada layar hingga 980 px, menu dapat dibuka-tutup; tombol Escape menutup menu dan mengembalikan fokus. Tanpa JavaScript, menu tetap dapat digunakan melalui elemen HTML `details`. Tabel lebar dapat digeser tanpa melebarkan seluruh halaman.

### Struktur dan pembersihan

- Controller dan tampilan lama kartu guru, direktori guru, peluncur scan admin, formulir jadwal terpisah, dan formulir password admin yang tidak digunakan telah dibersihkan. Pengelolaan jadwal tetap melalui Data Guru, kartu PDF melalui Jadwal Guru, dan password melalui Profil.
- Rute kompatibilitas yang masih aktif tetap dipertahankan; endpoint pembaruan profil pengguna lama masih digunakan dan tidak dihapus.
- QR aktif tetap menggunakan `gurus.token_qr`; jadwal menggunakan kolom JSON `gurus.jadwal`.
- Migrasi `2026_09_25_000001_remove_unused_presensi_qr_codes_table` menghapus tabel QR generasi lama hanya jika kosong. Jika berisi data, migrasi berhenti agar data dapat diarsipkan dahulu. Rollback membuat kembali skema tabel kosong.
- Migrasi historis dipertahankan agar instalasi baru dan rollback tetap dapat dijalankan secara berurutan. Jangan memakai `migrate:fresh` pada database aktif.
- Query filter hari aktif diperbaiki menjadi `jadwal->{$nomor}->aktif` agar nomor hari tidak ditafsirkan sebagai objek PHP.

### Pemeriksaan database lokal

Saat audit: koneksi MySQL, 2 akun, 1 guru, 0 presensi, 0 pengajuan, 0 notifikasi, dan 0 QR pada tabel lama. Tidak ditemukan guru tanpa akun atau presensi tanpa guru. Kolom jadwal tersedia dan migrasi sebelumnya sudah dijalankan. Data akun, guru, jadwal, dan QR aktif tidak dihapus.

Migrasi pembersihan sudah dijalankan pada MySQL lokal: tabel `presensi_qr_codes` telah dihapus dan jumlah data aktif tetap sama. Seluruh 26 pengujian lulus (179 assertions); build Vite, kompilasi Blade, dan pemeriksaan sintaks JavaScript berhasil.

Pengujian menggunakan database SQLite sementara (`:memory:`), bukan database aktif. Pemeriksaan visual browser belum tersedia pada sesi ini; ukuran layar dan interaksi sentuh tetap perlu ditinjau langsung pada perangkat.

## Catatan kebutuhan sebelumnya

PROMPT UPDATE MENU ADMIN

Saya ingin melakukan perubahan hanya pada menu Admin yang sebelumnya bernama "Scan Presensi".

1. Ubah Nama Menu

Ubah nama menu:

Scan Presensi → Jadwal Guru

Menu Scan Presensi tidak lagi digunakan sebagai menu scan QR Code Admin.

2. Fungsi Menu Jadwal Guru

Menu Jadwal Guru digunakan untuk melihat seluruh data jadwal guru yang sudah ditambahkan oleh Admin melalui menu Data Guru.

Tampilkan daftar seluruh guru beserta:

- Nama guru
- Data/identitas guru yang diperlukan
- Jadwal guru
- Hari
- Jam
- QR Code guru
- Status/data lain yang memang sudah tersedia di sistem

Data harus mengambil langsung dari database yang sudah ada.

3. Kartu Guru / PDF

Pada menu Jadwal Guru, Admin dapat memilih guru dan membuat/download PDF kartu guru.

PDF harus berisi:

- Kop surat sekolah
- Nama/logo sekolah jika sudah tersedia di project
- Nama guru
- Identitas guru yang diperlukan
- Jadwal guru
- QR Code guru

PDF harus memiliki desain yang rapi, profesional, dan siap dicetak atau dibagikan kepada guru.

4. QR Code

Jangan membuat ulang sistem pembuatan QR Code.

QR Code sudah dibuat otomatis pada menu Data Guru ketika Admin menambahkan akun guru, password, dan jadwal guru.

Menu Jadwal Guru hanya menampilkan dan menggunakan QR Code yang sudah tersimpan/terhubung dengan data guru tersebut.

Jika jadwal guru berubah di Data Guru, maka informasi jadwal yang ditampilkan pada menu Jadwal Guru dan PDF harus mengikuti data terbaru.

5. Jangan Mengubah Fitur Lain

Jangan mengubah:

- Command Center
- Data Guru
- Presensi
- Laporan
- Arahan Kepsek
- Profil Admin
- Sistem login
- Sistem QR Code yang sudah ada
- Sistem presensi yang sudah berjalan

Kecuali perubahan yang benar-benar diperlukan untuk mengganti menu Scan Presensi menjadi Jadwal Guru.

Jangan membuat fitur tambahan di luar permintaan ini.

Struktur Admin Setelah Perubahan

ADMIN
│
├── Command Center
├── Data Guru
│   ├── Tambah Guru
│   ├── Tambah Jadwal
│   ├── Edit Guru
│   ├── Edit Jadwal
│   ├── Ubah Password Guru
│   ├── Hapus Guru
│   └── QR Code otomatis
│
├── Presensi
│
├── Jadwal Guru
│   ├── Lihat semua guru
│   ├── Lihat jadwal semua guru
│   ├── Lihat QR Code
│   └── Download PDF Kartu Guru
│
├── Laporan
├── Arahan Kepsek
└── Profil
    ├── Edit Profil Admin
    ├── Edit Username
    └── Edit Password

Intinya: QR Code dibuat otomatis di Data Guru. Menu "Jadwal Guru" hanya digunakan untuk melihat jadwal seluruh guru dan menyediakan kartu guru/PDF yang siap dibagikan.
