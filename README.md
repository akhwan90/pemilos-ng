# Aplikasi GESIT (Buku Tamu & Aspirasi)

GESIT adalah sistem informasi berbasis web untuk manajemen Buku Tamu DPRD, Tamu Sekretariat Dewan (Setwan), Permohonan Audiensi, serta Aduan & Aspirasi Publik. Aplikasi ini dikembangkan dengan arsitektur SPA (Single Page Application) memisahkan backend API dan frontend yang interaktif.

## 🛠 Spesifikasi Aplikasi

- **Backend**: Laravel 12 (REST API)
- **Frontend**: Vue.js 3 (Composition API) + Vite
- **Routing Frontend**: Vue Router (Mode History)
- **Styling**: Tailwind CSS
- **Database**: MySQL / MariaDB
- **Autentikasi**: Laravel Sanctum
- **Library Tambahan**:
  - `barryvdh/laravel-dompdf` (Untuk *generate* dokumen PDF seperti Daftar Hadir & PPID)
  - Formulir dan Input interaktif menggunakan komponen *Vue kustom* (`BaseInput`, `BaseSelect`, `BaseModal`, dll)

## ✨ Fitur Utama

1. **Portal Publik**:
   - Pendaftaran Tamu DPRD
   - Pendaftaran Tamu Setwan
   - Pengajuan Permohonan Audiensi
   - Pengiriman Aduan & Aspirasi
   - *Upload* lampiran surat (PDF, JPG, PNG)

2. **Dashboard Admin**:
   - Autentikasi Admin (Login).
   - Manajemen data (Lihat, Terima, Tolak, Hapus data).
   - Generate & Cetak PDF Otomatis (Daftar Hadir & Dokumen PPID).
   - Preview & Unduh lampiran surat.

---

## 💻 Persyaratan Sistem (Prerequisites)

Sebelum menginstal, pastikan server atau komputer lokal Anda telah terinstal:
- PHP >= 8.2
- Composer
- Node.js (>= 18.x) & npm
- MySQL / MariaDB Server

---

## 🚀 Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di lingkungan pengembangan (local):

1. **Masuk ke direktori proyek**:
   ```bash
   cd /path/to/gesit
   ```

2. **Install dependensi Backend (PHP)**:
   ```bash
   composer install
   ```

3. **Install dependensi Frontend (Node.js)**:
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan sesuaikan kredensial database Anda:
   ```env
   APP_URL=http://localhost:8000
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_gesit
   DB_USERNAME=root
   DB_PASSWORD=password_database
   ```

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database**:
   Jalankan perintah ini untuk membuat tabel di database:
   ```bash
   php artisan migrate
   ```

7. **Buat Storage Link**:
   Langkah ini sangat penting agar file lampiran yang diunggah dan PDF yang di-*generate* dapat diakses oleh browser:
   ```bash
   php artisan storage:link
   ```
   *(Pastikan symlink `public/storage` berhasil mengarah ke `storage/app/public`)*.

8. **Build Aset Frontend**:
   Compile file Vue.js dan Tailwind CSS:
   ```bash
   npm run build
   ```
   *(Gunakan `npm run dev` jika sedang melakukan proses *development/coding* untuk fitur Hot Module Replacement / HMR)*.

---

## 📖 Cara Penggunaan

1. **Jalankan Backend server (Laravel)**:
   ```bash
   php artisan serve
   ```
   Aplikasi secara default akan berjalan di `http://127.0.0.1:8000`.

2. **Akses Halaman Publik**:
   Buka browser dan arahkan ke `http://127.0.0.1:8000`. Halaman ini berisi menu untuk mengisi form (Tamu Setwan, Tamu DPRD, Audiensi, dan Aduan).

3. **Akses Dashboard Admin**:
   Akses `http://127.0.0.1:8000/login` untuk masuk ke halaman admin.
   Setelah login, Anda bisa melihat menu kelola tamu, audiensi, dan menekan tombol **"Generate Document"** untuk membuat file Daftar Hadir & PPID (PDF) yang otomatis tersimpan dan dapat diunduh.

---

### Catatan Penting
- Saat mem-build frontend Vue (`npm run build`), pastikan `VITE_SUB_DIR` atau konfigurasi base URL di Vue Router sudah sesuai dengan *environment* deployment Anda jika tidak di-host pada *root domain*. 
- Untuk perizinan folder di Linux/Server, pastikan folder `storage/` dan `bootstrap/cache/` memiliki hak akses tulis (writeable):
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```