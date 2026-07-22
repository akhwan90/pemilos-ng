<template>
  <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
    
    <!-- Navbar / Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <img src="/asset/KPU.png" alt="Logo KPU" class="h-10 w-auto object-contain fallback-bg" @error="handleImageError" />
          <img src="/asset/kulonprogo.png" alt="Logo Kulon Progo" class="h-10 w-auto object-contain fallback-bg" @error="handleImageError" />
          <div>
            <h1 class="text-xl font-bold text-gray-800 leading-tight">e-PEMILOS</h1>
            <p class="text-xs text-gray-500 hidden md:block">Kabupaten Kulon Progo</p>
          </div>
        </div>
        <nav class="hidden md:flex gap-6">
          <router-link to="/" class="text-sm font-medium text-blue-600">Beranda</router-link>
          <router-link to="/sekolah" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Sekolah</router-link>
          <router-link to="/calon" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Calon</router-link>
          <router-link to="/arsip" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Arsip</router-link>
          <router-link to="/bantuan" class="text-sm font-medium text-gray-600 hover:text-blue-600 transition-colors">Bantuan</router-link>
        </nav>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="relative bg-white overflow-hidden flex items-center pt-16 lg:pt-20 pb-12 lg:pb-16 border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          
          <!-- Teks Kolom Kiri -->
          <div>
            <a href="#" class="inline-flex items-center gap-2 bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold mb-6 hover:bg-red-200 transition-colors">
              <span class="bg-red-600 text-white px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider">New</span>
              e-Pemilos {{ currentYear }}, apa yang baru?
              <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
            
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-6">
              <span class="text-blue-600">e-PEMILOS</span>
            </h2>
            
            <p class="text-base text-gray-600 leading-relaxed mb-4 text-justify">
              e-Pemilos adalah sebuah aplikasi yang diinisiasi dan didesain oleh Komisi Pemilihan Umum Kabupaten Kulon Progo sejak Tahun 2020 yang disesuaikan dengan format dan tahapan penyelenggaraan Pemilu. Aplikasi ini dibuat oleh Dinas Komunikasi dan Informatika Kabupaten Kulon Progo, dan didukung oleh Badan Kesatuan Bangsa dan Politik Kabupaten Kulon Progo, Dinas Pendidikan Kabupaten Kulon Progo, Balai Pendidikan Menengah Kabupaten Kulon Progo, dan Kantor Kementerian Agama Kabupaten Kulon Progo.
            </p>
            <p class="text-base text-gray-600 leading-relaxed mb-8 text-justify">
              Aplikasi ini digunakan untuk Pemilihan Ketua OSIS tingkat menengah se-Kabupaten Kulon Progo secara daring, sebagai wadah pendidikan pemilih sesuai asas pemilu yang dilaksanakan secara efektif, murah, aman, cepat, serta ramah lingkungan.
            </p>
            
            <div class="flex flex-col gap-3">
              <router-link to="/admin/login" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white text-base font-semibold rounded-lg shadow-sm transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                Login Admin Sekolah / Admin TPS
              </router-link>
              
              <div class="flex flex-col sm:flex-row gap-3">
                <router-link to="/tpssekolah" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-amber-400 hover:bg-amber-500 text-gray-900 text-base font-semibold rounded-lg shadow-sm transition-all">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                  Login Pemilih
                </router-link>
                
                <router-link to="/tpsluarsekolah" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-red-500 hover:bg-red-600 text-white text-base font-semibold rounded-lg shadow-sm transition-all">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                  Login Pemilih Luar Sekolah
                </router-link>
              </div>
            </div>
          </div>
          
          <!-- Gambar Ilustrasi Kanan -->
          <div class="hidden lg:flex justify-center">
            <img src="/asset/vote.png" alt="Pemilos Vote Illustration" class="w-full max-w-lg object-contain drop-shadow-xl" @error="handleImageError" />
          </div>
        </div>
      </div>
    </section>

    <!-- Callout / Statistik Section -->
    <section class="bg-white py-10 border-b border-gray-100 shadow-sm relative z-10 -mt-2 mx-4 lg:mx-auto max-w-7xl rounded-xl">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-4">
          <div class="md:w-1/2 text-center md:text-left">
            <h3 class="text-3xl font-bold text-gray-800">
              <span class="font-normal text-gray-500">sebuah</span> statistik
            </h3>
          </div>
          <div class="md:w-1/2 flex justify-around items-center w-full">
            <div class="text-center">
              <!-- Placeholder: ganti jumlah dengan prop/data dari API -->
              <div class="text-4xl font-extrabold text-gray-900 mb-1">{{ jml_sekolah }}</div>
              <div class="text-xs font-bold uppercase tracking-widest text-blue-600">Sekolah</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-extrabold text-gray-900 mb-1">{{ jml_pilihan }}</div>
              <div class="text-xs font-bold uppercase tracking-widest text-blue-600">Calon</div>
            </div>
            <div class="text-center">
              <div class="text-4xl font-extrabold text-gray-900 mb-1">{{ jml_tps }}</div>
              <div class="text-xs font-bold uppercase tracking-widest text-blue-600">TPS</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50 flex-grow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl font-bold text-gray-800">
            Mengapa menggunakan <span class="text-blue-600">e-Pemilos</span> ?
          </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 text-center">
            <div class="w-16 h-16 mx-auto bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-6">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-3">Aman</h5>
            <p class="text-gray-600 text-sm">Penerapan standar keamanan dalam setiap proses manajemen data.</p>
          </div>
          
          <!-- Feature 2 -->
          <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 text-center">
            <div class="w-16 h-16 mx-auto bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-6">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" /></svg>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-3">Modern</h5>
            <p class="text-gray-600 text-sm">Menggunakan teknologi terkini dalam pembangunan aplikasinya.</p>
          </div>
          
          <!-- Feature 3 -->
          <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 text-center">
            <div class="w-16 h-16 mx-auto bg-green-50 text-green-600 rounded-full flex items-center justify-center mb-6">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-3">Simpel</h5>
            <p class="text-gray-600 text-sm">Semua level pengguna hanya dalam satu akses aplikasi</p>
          </div>
          
          <!-- Feature 4 -->
          <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 text-center">
            <div class="w-16 h-16 mx-auto bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mb-6">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-3">Responsive Design</h5>
            <p class="text-gray-600 text-sm">Tampilan aplikasi e-Pemilos akan menyesuaikan ukuran layar</p>
          </div>
          
          <!-- Feature 5 -->
          <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 text-center">
            <div class="w-16 h-16 mx-auto bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-6">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-3">Realtime</h5>
            <p class="text-gray-600 text-sm">Laporan hasil pemilihan dapat dilihat secara langsung</p>
          </div>
          
          <!-- Feature 6 -->
          <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100 text-center">
            <div class="w-16 h-16 mx-auto bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-6">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-3">Ramah Lingkungan</h5>
            <p class="text-gray-600 text-sm">Meminimalkan penggunaan kertas</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer (Meniru layout CI3/Stisla admin lama) -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
          <div class="mb-4 md:mb-0">
            Aplikasi oleh &copy; Diskominfo KP
          </div>
          <div>
            <!-- Bisa diisi versi aplikasi atau info lain -->
            e-PEMILOS v3.0
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const currentYear = computed(() => new Date().getFullYear());

// Data dummy untuk statistik (bisa diubah agar me-load dari API Backend)
const jml_sekolah = ref(0);
const jml_pilihan = ref(0);
const jml_tps = ref(0);

// Simulasi load data statistik (opsional)
onMounted(() => {
  // Ganti dengan axios/fetch call Anda nantinya
  jml_sekolah.value = 45;
  jml_pilihan.value = 112;
  jml_tps.value = 248;
});

const handleImageError = (e) => {
  if (e.target.classList.contains('w-full')) {
    e.target.src = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_18e%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_18e%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23f3f4f6%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144.5%22%20y%3D%22158.5%22%3EIlustrasi%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';
  } else {
    e.target.style.display = 'none';
  }
};
</script>

<style scoped>
.fallback-bg {
  background-color: transparent;
}
</style>