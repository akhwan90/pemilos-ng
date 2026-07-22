<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center flex-col items-center">
        <img src="/asset/KPU.png" alt="Logo" class="h-20 w-auto mb-4" />
        <h2 class="text-center text-3xl font-extrabold text-gray-900 tracking-wider">
          LOGIN PEMILIH<br/><span class="text-xl font-semibold text-indigo-700">(TPS Luar Sekolah)</span>
        </h2>
        <p class="mt-4 text-center text-sm text-gray-600">
          Gunakan NISN dan Token yang telah dibagikan panitia untuk menggunakan hak pilih Anda dari rumah/luar sekolah.
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-10 px-4 shadow-xl border-t-4 border-indigo-600 sm:rounded-lg sm:px-10">
        
        <form @submit.prevent="verifyToken" class="space-y-6">
          <!-- Input NISN -->
          <div>
            <label class="block text-sm font-medium text-gray-700 text-center mb-2">NISN Anda</label>
            <div class="flex justify-center">
              <input 
                ref="nisnInputRef"
                v-model="nisnPemilih" 
                type="text" 
                class="appearance-none block w-full text-center text-2xl tracking-[0.2em] font-mono px-2 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                placeholder="Ketik NISN Anda"
                required 
                autofocus
                autocomplete="off"
              />
            </div>
          </div>

          <!-- Input Token -->
          <div>
            <label class="block text-sm font-medium text-gray-700 text-center mb-2">Token Pemilihan</label>
            <div class="mt-1 flex justify-center">
              <input 
                v-model="tokenPemilih" 
                type="text" 
                maxlength="5"
                class="appearance-none block w-full text-center text-4xl tracking-[0.5em] sm:tracking-[0.8em] font-mono uppercase px-2 py-4 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" 
                placeholder="XXXXX"
                required 
                autocomplete="off"
              />
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading || tokenPemilih.length < 5 || !nisnPemilih" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:bg-gray-400 transition-colors">
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Memverifikasi...' : 'MASUK BILIK SUARA' }}
            </button>
          </div>
        </form>

      </div>
    </div>
    
    <div class="mt-6 text-center">
        <router-link to="/" class="text-sm text-gray-500 hover:text-gray-900">&larr; Kembali ke Beranda</router-link>
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

const nisnPemilih = ref('');
const tokenPemilih = ref('');
const loading = ref(false);
const nisnInputRef = ref(null);

onMounted(() => {
  // Clear any existing session to ensure a fresh login
  localStorage.removeItem('pemilih_data');
  localStorage.removeItem('bilik_token'); // Ensure they are not using regular bilik auth
  
  if (nisnInputRef.value) {
    nisnInputRef.value.focus();
  }
});

async function verifyToken() {
  if (tokenPemilih.value.length < 5 || !nisnPemilih.value) return;
  
  loading.value = true;
  try {
    const payload = { 
        token: tokenPemilih.value.toUpperCase(),
        nisn: nisnPemilih.value
    };

    const res = await api.post('/bilik-luar-sekolah/verify', payload);

    const dataSiswa = res.data.data;

    if (dataSiswa.pilihan !== null) {
      toast.error('Maaf, Anda sudah menggunakan hak pilih sebelumnya (Sudah Mencoblos)!');
      tokenPemilih.value = '';
      nisnInputRef.value?.focus();
      return;
    }

    toast.success(`Selamat datang, ${dataSiswa.nm_siswa}!`);
    
    // Store regular pemilih data
    localStorage.setItem('pemilih_data', JSON.stringify({
      id_siswa_tps: dataSiswa.id_siswa_tps,
      nisn: dataSiswa.nisn,
      nm_siswa: dataSiswa.nm_siswa,
      kelas: dataSiswa.kelas
    }));

    // For Luar Sekolah, they act as their own "Bilik" so we fake the bilik_info
    // just enough so KertasSuara.vue can function.
    localStorage.setItem('bilik_info', JSON.stringify({
        is_luar_sekolah_mode: true, // Special flag for KertasSuara
        npsn: dataSiswa.npsn,
        nama_tps: 'TPS Luar Sekolah (Mandiri)',
        token_akses: res.data.token // Temporary token provided by backend
    }));
    
    router.push('/tpssekolah/vote');

  } catch (error) {
    toast.error(error.response?.data?.message || 'NISN atau Token tidak valid.');
    tokenPemilih.value = '';
  } finally {
    loading.value = false;
  }
}
</script>