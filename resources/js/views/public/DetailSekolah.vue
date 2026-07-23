<template>
  <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
    <!-- Navbar / Header (Reusable) -->
    <PublicHeader />

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
      <div class="mb-6 flex items-center">
        <router-link to="/sekolah" class="text-blue-600 hover:text-blue-800 flex items-center text-sm font-medium transition-colors">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
          Kembali ke Daftar Sekolah
        </router-link>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <svg class="animate-spin h-10 w-10 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <!-- Empty / Error State -->
      <div v-else-if="!sekolahData" class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Data Tidak Ditemukan</h3>
        <p class="mt-1 text-gray-500">Sekolah dengan NPSN tersebut tidak terdaftar di sistem.</p>
      </div>

      <!-- Konten Normal -->
      <div v-else>
        <!-- Card Info Sekolah -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10 flex flex-col md:flex-row items-center p-8 gap-8">
          <div class="h-32 w-32 flex-shrink-0 rounded-full bg-gray-50 border border-gray-200 p-3 overflow-hidden flex justify-center items-center">
            <img 
              :src="resolvedLogo" 
              :alt="`Logo ${sekolahData.nama_sekolah}`" 
              class="h-full w-full object-contain"
              @error="(e) => { e.target.src = '/asset/img/kpu_logo.png'; e.target.onerror = null; }"
            />
          </div>
          <div class="text-center md:text-left flex-grow">
            <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold mb-3">
              <span>NPSN: {{ sekolahData.npsn }}</span>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 leading-tight mb-2">
              {{ sekolahData.nama_sekolah }}
            </h2>
            <p class="text-gray-500 flex items-center justify-center md:justify-start gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              {{ sekolahData.alamat_sekolah || 'Alamat tidak tersedia' }}
            </p>
          </div>
        </div>

        <!-- Section Kandidat -->
        <div>
          <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-900">Kandidat Terdaftar ({{ currentYear }})</h3>
            <span class="bg-emerald-100 text-emerald-800 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wide">
              {{ kandidatList.length }} Paslon
            </span>
          </div>

          <div v-if="kandidatList.length === 0" class="text-center py-16 bg-white border border-gray-100 rounded-xl">
            <p class="text-gray-500">Belum ada kandidat yang didaftarkan untuk sekolah ini pada periode {{ currentYear }}.</p>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
              v-for="kandidat in kandidatList" 
              :key="kandidat.id"
              class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all border border-gray-100 overflow-hidden flex flex-col group"
            >
              <!-- Nomor Urut Badge -->
              <div class="bg-gray-900 text-white text-center py-3 font-black text-xl tracking-widest relative">
                NO. {{ kandidat.no }}
              </div>
              
              <!-- Foto Paslon -->
              <div class="aspect-w-3 aspect-h-4 bg-gray-200 relative overflow-hidden h-64">
                <img 
                  :src="kandidat.photo ? `/uploads/kandidat/${kandidat.photo}` : '/asset/img/kpu_logo.png'" 
                  :alt="kandidat.nama" 
                  class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500"
                  @error="(e) => { e.target.src = '/asset/img/kpu_logo.png'; e.target.onerror = null; }"
                />
              </div>

              <!-- Info Calon -->
              <div class="p-5 flex-grow text-center flex flex-col justify-center">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mb-1">Calon Ketua & Wakil</p>
                <h4 class="text-lg font-bold text-gray-900">{{ kandidat.nama }}</h4>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>

    <!-- Footer (Reusable) -->
    <PublicFooter />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import PublicHeader from '../../components/public/PublicHeader.vue';
import PublicFooter from '../../components/public/PublicFooter.vue';
import api from '../../services/api';

const route = useRoute();
const currentYear = new Date().getFullYear();
const loading = ref(true);
const sekolahData = ref(null);
const kandidatList = ref([]);

// Komputasi pemecah logo SIAP-PPDB (Eksternal) / Lokal
const resolvedLogo = computed(() => {
  if (!sekolahData.value || !sekolahData.value.logo) {
    return '/asset/img/kpu_logo.png';
  }
  const logo = sekolahData.value.logo;
  if (logo.startsWith('http')) {
    return logo;
  }
  return `/uploads/logo_sekolah/${logo}`;
});

onMounted(() => {
  const npsnQuery = route.query.npsn;
  if (npsnQuery) {
    fetchDetailSekolah(npsnQuery);
  } else {
    loading.value = false;
  }
});

async function fetchDetailSekolah(npsn) {
  loading.value = true;
  try {
    const res = await api.get(`/public/sekolah/${npsn}`);
    if (res.data && res.data.success) {
      sekolahData.value = res.data.data.sekolah;
      kandidatList.value = res.data.data.kandidat || [];
    }
  } catch (error) {
    console.error('Gagal mengambil detail sekolah:', error);
  } finally {
    loading.value = false;
  }
}
</script>