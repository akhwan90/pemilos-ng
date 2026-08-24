<template>
  <div class="min-h-screen bg-gray-50 flex flex-col font-sans">

    <!-- Navbar / Header (Reusable) -->
    <PublicHeader />

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
      <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
        <div>
          <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Daftar Sekolah</h2>
          <p class="text-gray-500 mt-2">Peserta e-Pemilos (Pemilihan Ketua OSIS) Terdaftar</p>
        </div>
        <div class="w-full md:w-1/3">
            <div class="relative">
                <input
                    type="text"
                    v-model="searchQuery"
                    @input="fetchSekolah"
                    placeholder="Cari nama sekolah..."
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <!-- Empty State -->
      <div v-else-if="sekolahList.length === 0" class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak Ada Sekolah Ditemukan</h3>
        <p class="mt-1 text-gray-500">Silakan gunakan kata kunci pencarian yang lain.</p>
      </div>

      <!-- Grid Daftar Sekolah -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="sekolah in sekolahList"
          :key="sekolah.npsn"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col group"
        >
          <div class="p-6 flex-grow flex flex-col items-center text-center">
            <div class="h-24 w-24 mb-4 rounded-full bg-gray-50 p-2 border border-gray-100 group-hover:border-blue-300 transition-colors flex justify-center items-center overflow-hidden">
              <img
                :src="sekolah.resolvedLogo"
                :alt="`Logo ${sekolah.nama_sekolah}`"
                class="h-full w-full object-contain"
                @error="(e) => { e.target.src = '/images/kpu_logo.png'; e.target.onerror = null; }"
              />
            </div>
            <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2 group-hover:text-blue-600 transition-colors">
              {{ sekolah.nama_sekolah }}
            </h3>
            <p class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-1 rounded inline-block mb-3">
              NPSN: {{ sekolah.npsn }}
            </p>
            <p class="text-sm text-gray-500 line-clamp-2">
              {{ sekolah.alamat || 'Alamat belum diatur' }}
            </p>
          </div>

          <!-- Tombol Detail / Aksi -->
          <div class="bg-gray-50 border-t border-gray-100 p-4">
            <router-link :to="`/detail-sekolah?npsn=${sekolah.npsn}`" class="w-full inline-flex justify-center items-center py-2 px-4 border border-blue-200 shadow-sm text-sm font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
              Lihat Detail & Kandidat
            </router-link>
          </div>
        </div>
      </div>

    </main>

    <!-- Footer (Reusable) -->
    <PublicFooter />

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import PublicHeader from '../../components/public/PublicHeader.vue';
import PublicFooter from '../../components/public/PublicFooter.vue';
import api from '../../services/api';

const currentYear = new Date().getFullYear();
const sekolahList = ref([]);
const searchQuery = ref('');
const loading = ref(true);

onMounted(() => {
    fetchSekolah();
});

async function fetchSekolah() {
    loading.value = true;
    try {
        const res = await api.get(`/public/sekolah?cari=${searchQuery.value}`);
        if (res.data && res.data.success) {
            // Helper function to resolve logo URL properly
            sekolahList.value = res.data.data.map(sekolah => {
                let resolvedLogo = '/asset/img/kpu_logo.png'; // Default fallback

                if (sekolah.logo) {
                    // Cek apakah string berawal dengan http atau https (seperti logo SIAP-PPDB)
                    if (sekolah.logo.startsWith('http')) {
                        resolvedLogo = sekolah.logo;
                    } else {
                        // Jika nama file biasa, arahkan ke folder lokal
                        resolvedLogo = `/uploads/logo_sekolah/${sekolah.logo}`;
                    }
                }

                return { ...sekolah, resolvedLogo };
            });
        }
    } catch (error) {
        console.error('Gagal mengambil daftar sekolah', error);
    } finally {
        loading.value = false;
    }
}
</script>
