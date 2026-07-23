# Worklog Pemilos-NG

## Tanggal: 17 Juli 2026

### Pembaruan Fitur dan Perbaikan (Backend & Frontend)

1. **Manajemen Data Siswa Global (Super Admin)**
   - Membuat komponen UI baru: `DataSiswaGlobal.vue` untuk halaman Super Admin yang bisa mencari dan memfilter siswa antar sekolah.
   - Membuat controller baru `DataSiswaGlobalController.php` dengan endpoint API `GET /api/admin/data-siswa-global` beserta fitur soft-delete atau Hapus Permanen `DELETE /api/admin/data-siswa-global/{id}`.
   - Menambahkan routing Vue di `router/index.js` dan menautkan navigasinya di `AdminLayout.vue`.
   - Mengubah request murni Axios menjadi interceptor request terpusat pada file `api.js` agar token Bearer Sanctum tetap terlampir (solusi Error 401).

2. **Perbaikan Bug pada Data Sekolah**
   - Memperbaiki `TypeError: Cannot read properties of null (reading npsn)` karena kesalahan mapping nama properti objek (`nm_sekolah` menjadi `nama_sekolah`) pada saat render.
   - Memperbaiki Dropdown Data Sekolah yang opsinya terpotong menjadi 20 baris akibat efek default paginasi. Solusinya, menambahkan parameter query `no_pagination=1` pada `DataSekolahController`.

3. **Optimasi Job Impor Siswa**
   - Memperketat Rule Validation pada parameter impor data dari format Excel dalam `ImportSiswaJob.php`: Validasi panjang karakter minimum (NISN > 10, Nama > 3), casting Jenis Kelamin (hanya 1 atau 2), dan difabel (boolean / integer). 
   - Memperbarui sistem pelaporan Job supaya melompati (bypass) row yang invalid tetapi menyimpannya ke tabel Log (tidak memutus seluruh eksekusi impor).

4. **Dasbor Statistik (Admin Sekolah)**
   - Menambah fungsional perhitungan aggregate (count) `jml_siswa`, `jml_tps`, `jml_kandidat`, dan `jml_dpt` pada `DashboardController.php`.
   - Memoles antarmuka pengguna (`Dashboard.vue`) dengan tampilan kartu statistik 4 kolom, ditambah indikator visual centang hijau (check) apabila nilainya > 0, atau tanda minus abu-abu apabila nilainya 0.

5. **Manajemen TPS & Admin TPS Level 3**
   - Memisahkan logic Controller CRUD TPS menjadi dua file: `TpsSekolahController.php` (untuk Super Admin) dan `TpsController.php` (untuk Admin Sekolah di dalam namespace AdminSekolah). Hal ini dilakukan untuk menghindari error clash argument (`Too few arguments to function`) akibat variasi path parameter npsn.
   - Mengganti layout form sidebar tambah TPS di file `DataTps.vue` dengan popup `ModalFormTps.vue` untuk UI yang lebih ringkas.
   - Membuat fitur manajemen Admin TPS (User Level 3) di dalam komponen pop up khusus `ModalAdminTps.vue`.
   - Menambahkan rute dan API (GET, POST, DELETE, PUT Password) pengelolaan akun level 3 khusus untuk Admin Sekolah tersebut.
   - Memperbaiki interaksi form Admin TPS: penolakan karakter spasi pada username dan password, serta penambahan icon toggle (mata silang/buka) untuk visibilitas password.

*Telah di-commit dengan pesan: "feat: Menambahkan fitur Data Siswa Global dan perbaikan modul TPS/Admin Sekolah"*