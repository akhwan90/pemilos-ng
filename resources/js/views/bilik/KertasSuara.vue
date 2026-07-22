<template>
  <div class="min-h-screen bg-gray-100 flex flex-col font-sans">
    <!-- Header Standby (Bilik) -->
    <header class="bg-white shadow-sm py-4">
      <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
          <img src="/asset/KPU.png" alt="Logo KPU" class="h-10" />
          <h1 class="text-xl font-bold text-gray-800 tracking-wide">SURAT SUARA ELEKTRONIK</h1>
        </div>
        <div class="text-right">
          <p class="text-sm font-bold text-indigo-700">{{ pemilihData?.nama_tps || 'TPS' }}</p>
          <p class="text-xs text-gray-500">{{ pemilihData?.nm_siswa }} ({{ pemilihData?.kelas }})</p>
        </div>
      </div>
    </header>

    <!-- Main Voting Area -->
    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Pilih Calon Ketua & Wakil OSIS</h2>
        <p class="text-lg text-gray-600">Ketuk/klik pada foto atau tombol pilih pada kandidat pilihan Anda.</p>
      </div>

      <div v-if="loadingCalon" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-indigo-600"></div>
      </div>
      
      <div v-else-if="kandidatList.length === 0" class="text-center py-20 text-gray-500 bg-white rounded-xl shadow">
        <p class="text-xl">Tidak ada data kandidat calon pada TPS ini.</p>
      </div>

      <!-- Grid Kandidat -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="kandidat in kandidatList" 
          :key="kandidat.id"
          @click="konfirmasiPilihan(kandidat)"
          class="bg-white rounded-2xl shadow-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-2xl cursor-pointer border-2 border-transparent hover:border-indigo-400 flex flex-col"
        >
          <!-- Nomor Urut Badge -->
          <div class="bg-indigo-600 text-white text-center py-2 relative">
            <span class="absolute top-2 left-4 text-xs opacity-75">PASLON NOMOR</span>
            <h3 class="text-4xl font-black">{{ kandidat.no }}</h3>
          </div>
          
          <!-- Foto Kandidat -->
          <div class="aspect-w-3 aspect-h-4 bg-gray-200 relative overflow-hidden h-80">
            <img 
              :src="kandidat.photo ? `/uploads/kandidat/${kandidat.photo}` : '/asset/user.png'" 
              alt="Foto Kandidat" 
              class="w-full h-full object-cover object-top"
            />
          </div>

          <!-- Nama Kandidat -->
          <div class="p-6 text-center flex-grow flex flex-col justify-between">
            <div>
              <h4 class="text-xl font-bold text-gray-900 leading-tight">{{ kandidat.nama }}</h4>
            </div>
            
            <button class="mt-6 w-full bg-indigo-50 text-indigo-700 font-bold py-3 px-4 rounded-xl border border-indigo-200 hover:bg-indigo-600 hover:text-white transition-colors">
              PILIH NOMOR {{ kandidat.no }}
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Konfirmasi -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true" @click="showModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                  Konfirmasi Pilihan
                </h3>
                <div class="mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-100 text-center">
                  <p class="text-sm text-gray-500 mb-1">Anda akan memilih Paslon Nomor:</p>
                  <p class="text-4xl font-black text-indigo-700 my-2">{{ selectedKandidat?.no }}</p>
                  <p class="text-md font-bold text-gray-900">{{ selectedKandidat?.nama }}</p>
                </div>
                <p class="mt-4 text-sm text-red-600 font-semibold text-center">
                  Pilihan yang sudah disimpan tidak dapat diubah kembali!
                </p>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" @click="submitVote" :disabled="isSubmitting" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
              <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isSubmitting ? 'Menyimpan...' : 'YA, SAYA YAKIN' }}
            </button>
            <button type="button" @click="showModal = false" :disabled="isSubmitting" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              BATAL
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const router = useRouter();
const toast = useToast();

const pemilihData = ref(null);
const kandidatList = ref([]);
const loadingCalon = ref(true);

const showModal = ref(false);
const selectedKandidat = ref(null);
const isSubmitting = ref(false);

onMounted(() => {
  // Pastikan ada data pemilih dari input token
  const savedData = localStorage.getItem('pemilih_data');
  if (!savedData) {
    toast.error('Sesi pemilih tidak valid! Silakan masukkan token kembali.');
    return router.push('/tpssekolah/token');
  }
  
  pemilihData.value = JSON.parse(savedData);
  fetchKandidat();
});

async function fetchKandidat() {
  loadingCalon.value = true;
  try {
    const res = await api.get('/bilik/calon', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('bilik_token')}`
      }
    });
    kandidatList.value = res.data.data || [];
  } catch (error) {
    toast.error('Gagal memuat data kandidat.');
  } finally {
    loadingCalon.value = false;
  }
}

function konfirmasiPilihan(kandidat) {
  selectedKandidat.value = kandidat;
  showModal.value = true;
}

async function submitVote() {
  if (!selectedKandidat.value) return;
  
  isSubmitting.value = true;
  try {
    await api.post('/bilik/submit-vote', {
      id_siswa_tps: pemilihData.value.id_siswa_tps, // ID unik row siswa_tps yang ditarik saat token valid
      id_calon: selectedKandidat.value.id
    }, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('bilik_token')}`
      }
    });

    toast.success('Pilihan Anda berhasil disimpan. Terima Kasih!');
    
    // Auto logout sesi si pemilih
    localStorage.removeItem('pemilih_data');
    
    // Tutup modal dan lempar layar kembali ke form standby Input Token
    showModal.value = false;
    router.push('/tpssekolah/token');

  } catch (error) {
    toast.error(error.response?.data?.message || 'Terjadi kesalahan saat menyimpan pilihan.');
  } finally {
    isSubmitting.value = false;
  }
}
</script>