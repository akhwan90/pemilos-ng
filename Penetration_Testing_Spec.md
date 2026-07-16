# Spesifikasi Sistem "GESIT" (Untuk Keperluan Penetration Testing)

Dokumen ini berisi spesifikasi teknis dan rincian *attack surface* dari aplikasi **GESIT**, yang dirancang untuk memudahkan tim Penetration Tester (Pentester) dalam melakukan asesmen keamanan sistem.

## 1. Informasi Umum
- **Nama Aplikasi**: GESIT (Aduan Aspirasi & Layanan Tamu DPRD/Setwan)
- **Model Arsitektur**: *Headless/Decoupled* (Backend API + Frontend SPA)
- **Tujuan Sistem**: Memfasilitasi masyarakat untuk mengirimkan aduan, aspirasi, mendaftar kunjungan (Tamu DPRD/Setwan), dan permohonan audiensi secara publik, serta dashboard admin untuk mengelolanya.

## 2. Technology Stack
- **Backend Framework**: Laravel v12.62 (PHP 8.x)
- **Frontend Framework**: Vue.js v3.5.39 (SPA di-*build* dengan Vite) + TailwindCSS v4
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Sanctum (Token-based Auth untuk Admin)
- **Pustaka Pihak Ketiga Utama**:
  - `mews/captcha` v3.5 (Captcha untuk *Public Form*)
  - `barryvdh/laravel-dompdf` v3.1 (Export / Generate Dokumen PDF)
  - `maatwebsite/excel` v3.1 (Export Excel)

## 3. Security Controls (Mekanisme Keamanan Saat Ini)
Berikut adalah kontrol keamanan yang sudah diimplementasikan dan perlu diverifikasi kekuatannya:
1. **Rate Limiting (Throttling)**:
   - Form Submit Publik (`throttle:public-submission`): Dibatasi maksimal 3 *request* per 10 menit per IP Address.
   - Admin Login (`throttle:admin-login`): Pembatasan jumlah percobaan login.
2. **Anti-Bot (Captcha)**:
   - Form publik diwajibkan menyertakan input Captcha. *Endpoint* generate captcha menggunakan library Intervention Image.
3. **Persetujuan Data Pribadi (General Consent)**:
   - Diimplementasikan di sisi frontend (General Consent Modal) sebelum form dikirimkan, untuk kepatuhan Pelindungan Data Pribadi (PDP).
4. **CORS (Cross-Origin Resource Sharing)**: 
   - Diatur menggunakan konfigurasi default `fruitcake/php-cors` bawaan Laravel.
5. **Autentikasi API (Sanctum)**: 
   - Modul Admin sepenuhnya menggunakan skema token Bearer via middleware `auth:sanctum`.

## 4. Attack Surface & Input Vectors
Fokuskan pengujian pada celah-celah interaksi pengguna (input) berikut:

### A. File Uploads
Terdapat fitur unggah dokumen (Bukti Aduan, Surat Kunjungan, Bukti Menginap, SPT).
- **Validasi Frontend**: Dibatasi ekstensi `.pdf, .jpg, .jpeg, .png` dengan maksimal ukuran 5MB.
- **Titik Uji**: 
  - Validasi MIME type dan ekstensi di sisi backend (apakah bisa di-bypass mengunggah `.php`, `.sh`, `.exe`, atau file SVG yang berisi XSS).
  - Apakah file di-*store* di public disk yang dapat diakses langsung, atau di private disk (Directory Traversal / Local File Inclusion).

### B. Form Publik (Unauthenticated)
Masyarakat dapat mengirimkan data ke endpoint berikut tanpa perlu login:
- Kategori Input: NIK (16 digit), Nama, Email, Alamat, Nomor HP, Isi Aduan/Tujuan Kunjungan.
- **Titik Uji**: 
  - *Cross-Site Scripting (XSS)* pada field teks (terutama `isi_aduan` dan `alamat`).
  - *SQL Injection (SQLi)* pada input parameter API.
  - *Captcha Bypass* (apakah *session* captcha dapat di-reuse, dikosongkan, atau di-bypass).
  - *Rate Limit Evasion* (melakukan bypass IP throttling misalnya menggunakan header `X-Forwarded-For`).

### C. Admin Dashboard (Authenticated)
Fitur untuk admin mengelola data (CRUD), mengubah status, dan mengekspor dokumen.
- **Titik Uji**:
  - *Insecure Direct Object Reference (IDOR)*: Apakah pengguna dengan privilege (jika ada *multi-role*) bisa memodifikasi atau menghapus ID aduan/tamu yang bukan haknya.
  - *Privilege Escalation* / *Broken Access Control*.
  - Pembuatan Laporan PDF/Excel: Injeksi *formula CSV (CSV Injection)* atau celah *Server-Side Request Forgery (SSRF) / Remote Code Execution (RCE)* lewat *DOMPDF*.

## 5. Daftar Endpoint API (Route Map)

### 🔓 Public API (No Auth)
| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/kategori-aduan` | Mengambil list master kategori aduan |
| `GET` | `/api/captcha` | Men-generate string & gambar base64 Captcha |
| `POST` | `/api/aduan-aspirasi` | Submit Aduan & Upload Bukti (Rate-limited) |
| `POST` | `/api/tamu-setwan` | Submit Tamu Setwan & Upload Berkas (Rate-limited) |
| `POST` | `/api/tamu-dprd` | Submit Tamu DPRD & Upload Berkas (Rate-limited) |
| `POST` | `/api/permohonan-audiensi` | Submit Permohonan Audiensi & Berkas (Rate-limited) |

### 🔒 Admin API (Sanctum Auth Required)
| Method | Endpoint | Deskripsi |
|---|---|---|
| `POST` | `/api/admin/login` | Endpoint autentikasi admin (Rate-limited) |
| `POST` | `/api/admin/logout` | Revoke token Sanctum |
| `GET` | `/api/admin/dashboard/stats` | Statistik jumlah data |
| `GET` | `/api/admin/{modul}/export` | Export data (Aduan, Setwan, DPRD, Audiensi) |
| `GET/POST/PUT/DEL`| `/api/admin/{modul}/{id}` | CRUD Data tiap modul public |
| `PATCH` | `/api/admin/{modul}/{id}/status` | Mengubah status tiket/aduan |
| `POST` | `/api/admin/{modul}/{id}/upload-berkas` | Admin mengunggah berkas tambahan |
| `DELETE`| `/api/admin/{modul}/{id}/hapus-berkas` | Admin menghapus berkas |
| `POST` | `/api/admin/{modul}/{id}/generate` | Generate dokumen (PDF) dari data |
| `GET/POST/DEL`| `/api/admin/notes/{type}/{id}` | Fitur *Polymorphic Admin Notes* |
| `GET/POST` | `/api/admin/settings` | Pengaturan aplikasi (App Settings) |

*(Keterangan: `{modul}` mencakup `aduan-aspirasi`, `tamu-setwan`, `tamu-dprd`, dan `permohonan-audiensi`)*
