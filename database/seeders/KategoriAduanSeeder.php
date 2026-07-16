<?php

namespace Database\Seeders;

use App\Models\KategoriAduan;
use Illuminate\Database\Seeder;

class KategoriAduanSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Infrastruktur', 'slug' => 'infrastruktur', 'deskripsi' => 'Jalan, jembatan, irigasi, dan fasilitas umum'],
            ['nama' => 'Pelayanan Publik', 'slug' => 'pelayanan-publik', 'deskripsi' => 'Pelayanan administrasi dan birokrasi pemerintahan'],
            ['nama' => 'Kesehatan', 'slug' => 'kesehatan', 'deskripsi' => 'Layanan kesehatan, puskesmas, dan rumah sakit'],
            ['nama' => 'Pendidikan', 'slug' => 'pendidikan', 'deskripsi' => 'Sarana dan mutu pendidikan di daerah'],
            ['nama' => 'Lingkungan Hidup', 'slug' => 'lingkungan-hidup', 'deskripsi' => 'Kebersihan, penghijauan, dan pelestarian lingkungan'],
            ['nama' => 'Sosial dan Kesejahteraan', 'slug' => 'sosial-dan-kesejahteraan', 'deskripsi' => 'Bantuan sosial, pemberdayaan masyarakat, dan kesejahteraan'],
            ['nama' => 'Perekonomian', 'slug' => 'perekonomian', 'deskripsi' => 'UMKM, pasar, dan perekonomian daerah'],
            ['nama' => 'Keamanan dan Ketertiban', 'slug' => 'keamanan-dan-ketertiban', 'deskripsi' => 'Keamanan lingkungan dan ketertiban umum'],
            ['nama' => 'Lainnya', 'slug' => 'lainnya', 'deskripsi' => 'Kategori lainnya yang tidak tercantum'],
        ];

        foreach ($kategori as $item) {
            KategoriAduan::create($item);
        }
    }
}
