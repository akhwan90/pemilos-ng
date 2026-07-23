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
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8 flex flex-col md:flex-row items-center p-8 gap-8">
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

        <!-- Menu Tabs Navigation -->
        <div class="border-b border-gray-200 mb-8 overflow-x-auto">
            <nav class="-mb-px flex space-x-8 min-w-max" aria-label="Tabs">
                <!-- Tab Calon -->
                <button 
                    @click="changeTab('calon')"
                    :class="[
                        currentTab === 'calon' 
                            ? 'border-indigo-500 text-indigo-600 font-bold' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium',
                        'whitespace-nowrap pb-4 px-1 border-b-2 text-sm transition-colors cursor-pointer'
                    ]"
                >
                    Kandidat Paslon
                </button>
                
                <!-- Tab DPS -->
                <button 
                    @click="changeTab('dps')"
                    :class="[
                        currentTab === 'dps' 
                            ? 'border-indigo-500 text-indigo-600 font-bold' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium',
                        'whitespace-nowrap pb-4 px-1 border-b-2 text-sm transition-colors cursor-pointer'
                    ]"
                >
                    Data Pemilih Sementara (DPS)
                </button>

                <!-- Tab DPT -->
                <button 
                    @click="changeTab('dpt')"
                    :class="[
                        currentTab === 'dpt' 
                            ? 'border-indigo-500 text-indigo-600 font-bold' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium',
                        'whitespace-nowrap pb-4 px-1 border-b-2 text-sm transition-colors cursor-pointer'
                    ]"
                >
                    Data Pemilih Tetap (DPT)
                </button>
            </nav>
        </div>

        <!-- Section: Kandidat -->
        <div v-if="currentTab === 'calon'">
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

        <!-- Section: DPS -->
        <div v-if="currentTab === 'dps'">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Data Pemilih Sementara (DPS)</h3>
            </div>
            
            <div v-if="loadingDps" class="flex justify-center py-10">
                <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8a8 8 0 01-8-8z"></path></svg>
            </div>
            <div v-else-if="!isDpsBuka" class="text-center py-16 bg-white border border-gray-100 rounded-xl">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <p class="text-gray-500 font-medium">{{ dpsMessage || 'Data Pemilih Sementara belum dibuka.' }}</p>
            </div>
            <div v-else-if="dpsData.length === 0" class="text-center py-16 bg-white border border-gray-100 rounded-xl">
                <p class="text-gray-500">Belum ada Data Pemilih Sementara yang terdaftar.</p>
            </div>
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">L/P</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(siswa, idx) in dpsData" :key="siswa.nisn" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ idx + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ siswa.nisn }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ siswa.nm_siswa }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ siswa.kelas }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ siswa.jk == 1 ? 'Laki-laki' : 'Perempuan' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section: DPT -->
        <div v-if="currentTab === 'dpt'">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Data Pemilih Tetap (DPT)</h3>
            </div>

            <div v-if="loadingDpt" class="flex justify-center py-10">
                <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8a8 8 0 01-8-8z"></path></svg>
            </div>
            <div v-else-if="!isDptBuka" class="text-center py-16 bg-white border border-gray-100 rounded-xl">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <p class="text-gray-500 font-medium">{{ dptMessage || 'Data Pemilih Tetap belum diumumkan.' }}</p>
            </div>
            <div v-else-if="dptData.length === 0" class="text-center py-16 bg-white border border-gray-100 rounded-xl">
                <p class="text-gray-500">Belum ada Data Pemilih Tetap yang terdaftar di TPS ini.</p>
            </div>
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NISN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas Asal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TPS (Bilik)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(siswa, idx) in dptData" :key="siswa.nisn" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ idx + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ siswa.nisn }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ siswa.nm_siswa }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ siswa.nama_kelas_asal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-indigo-600">{{ siswa.nama_tps }}</td>
                            </tr>
                        </tbody>
                    </table>
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
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import PublicHeader from '../../components/public/PublicHeader.vue';
import PublicFooter from '../../components/public/PublicFooter.vue';
import api from '../../services/api';

const route = useRoute();
const router = useRouter();
const currentYear = new Date().getFullYear();
const loading = ref(true);
const sekolahData = ref(null);
const kandidatList = ref([]);
const dpsData = ref([]);
const dptData = ref([]);
const loadingDps = ref(false);
const loadingDpt = ref(false);
const isDpsBuka = ref(true);
const isDptBuka = ref(true);
const dpsMessage = ref('');
const dptMessage = ref('');
const currentTab = ref(route.query.tab || 'calon');

// Fungsi untuk mengganti tab sekaligus update parameter URL
function changeTab(tabName) {
    currentTab.value = tabName;
    router.replace({
        query: {
            ...route.query,
            tab: tabName
        }
    });
}

// Watcher untuk mendeteksi apabila parameter URL "tab" diganti (misal back button)
watch(() => route.query.tab, (newTab) => {
    if (newTab && ['calon', 'dps', 'dpt'].includes(newTab)) {
        currentTab.value = newTab;
    }
});

watch(currentTab, (newTab) => {
    if (newTab === 'dps' && dpsData.value.length === 0 && isDpsBuka.value) {
        fetchDataDps(route.query.npsn);
    } else if (newTab === 'dpt' && dptData.value.length === 0 && isDptBuka.value) {
        fetchDataDpt(route.query.npsn);
    }
});

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
      
      // Auto fetch if tab is selected on mount
      if (currentTab.value === 'dps') {
          fetchDataDps(npsn);
      } else if (currentTab.value === 'dpt') {
          fetchDataDpt(npsn);
      }
    }
  } catch (error) {
    console.error('Gagal mengambil detail sekolah:', error);
  } finally {
    loading.value = false;
  }
}

async function fetchDataDps(npsn, page = 1) {
    loadingDps.value = true;
    try {
        const res = await api.get(`/public/sekolah/${npsn}/dps?page=${page}`);
        if (res.data && res.data.success) {
            dpsData.value = res.data.data.data; // Using laravel paginator format
            isDpsBuka.value = true;
        }
    } catch (error) {
        if (error.response && error.response.status === 403) {
            isDpsBuka.value = false;
            dpsMessage.value = error.response.data.message;
        }
        console.error('Gagal mengambil data DPS:', error);
    } finally {
        loadingDps.value = false;
    }
}

async function fetchDataDpt(npsn, page = 1) {
    loadingDpt.value = true;
    try {
        const res = await api.get(`/public/sekolah/${npsn}/dpt?page=${page}`);
        if (res.data && res.data.success) {
            dptData.value = res.data.data.data;
            isDptBuka.value = true;
        }
    } catch (error) {
        if (error.response && error.response.status === 403) {
            isDptBuka.value = false;
            dptMessage.value = error.response.data.message;
        }
        console.error('Gagal mengambil data DPT:', error);
    } finally {
        loadingDpt.value = false;
    }
}
</script>