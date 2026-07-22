<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center flex-col items-center">
        <img src="/asset/KPU.png" alt="Logo" class="h-20 w-auto mb-4" />
        <h2 class="text-center text-4xl font-extrabold text-gray-900 tracking-wider">
          BILIK SUARA
        </h2>
        <p class="mt-2 text-center text-lg text-indigo-700 font-bold bg-indigo-100 px-4 py-1 rounded-full">
          {{ bilikInfo?.nama_tps || 'TPS Aktif' }}
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg">
      <div class="bg-white py-10 px-4 shadow-xl border-t-4 border-indigo-600 sm:rounded-lg sm:px-10">
        
        <div class="text-center mb-8">
          <h3 class="text-lg font-medium text-gray-700">Masukkan Token Pemilih Anda</h3>
          <p class="text-sm text-gray-500 mt-1">Token 5 karakter yang Anda dapatkan dari panitia</p>
        </div>

        <form @submit.prevent="verifyToken" class="space-y-6">
          <div>
            <div class="mt-1 flex justify-center">
              <!-- Kotak Input yang besar agar mudah ditekan di layar sentuh -->
              <input 
                ref="tokenInputRef"
                v-model="tokenPemilih" 
                type="text" 
                maxlength="5"
                class="appearance-none block w-full text-center text-4xl tracking-[0.5em] sm:tracking-[0.8em] font-mono uppercase px-2 py-4 border-2 border-indigo-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50" 
                placeholder="XXXXX"
                required 
                autofocus
                autocomplete="off"
              />
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading || tokenPemilih.length < 5" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:bg-gray-400 transition-colors">
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Memverifikasi...' : 'LANJUTKAN' }}
            </button>
          </div>
        </form>

      </div>
    </div>
    
    <!-- Footer Bilik Rahasia -->
    <div class="mt-8 text-center text-xs text-gray-400 flex flex-col items-center gap-2">
      <p>Sistem E-Voting Pemilos | Gunakan Hak Pilih Anda Dengan Bijak</p>
      
      <button @click="logoutBilik" class="mt-2 text-indigo-400 hover:text-indigo-600 transition-colors flex items-center gap-1 font-medium bg-transparent border-none cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        Keluar / Tutup Bilik TPS
      </button>
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

const bilikInfo = ref(null);
const tokenPemilih = ref('');
const loading = ref(false);
const tokenInputRef = ref(null);

onMounted(() => {
  // Ambil info TPS dari LocalStorage saat Bilik ini dinyalakan
  const savedInfo = localStorage.getItem('bilik_info');
  if (savedInfo) {
    bilikInfo.value = JSON.parse(savedInfo);
  } else {
    // Jika tidak ada data bilik (belum login admin tps), paksa kembali ke login bilik
    toast.error('Bilik TPS belum diaktifkan oleh Admin!');
    router.push('/tpssekolah/login');
  }
  
  // Paksa fokus ke kotak input
  if (tokenInputRef.value) {
    tokenInputRef.value.focus();
  }
});

async function verifyToken() {
  if (tokenPemilih.value.length < 5) return;
  
  loading.value = true;
  try {
    const res = await api.post('/bilik/verify-token', {
      token: tokenPemilih.value.toUpperCase()
    }, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('bilik_token')}`
      }
    });

    // Jika Token Valid, simpan data sesi si Pemilih tersebut
    localStorage.setItem('pemilih_data', JSON.stringify(res.data.siswa));
    
    toast.success(`Selamat datang, ${res.data.siswa.nm_siswa}!`);
    
    // Lempar ke halaman kertas suara / daftar calon
    router.push('/tpssekolah/vote');

  } catch (error) {
    toast.error(error.response?.data?.message || 'Token tidak valid atau sistem bermasalah.');
    tokenPemilih.value = ''; // Reset kotak agar bisa dicoba lagi
    tokenInputRef.value?.focus();
  } finally {
    loading.value = false;
  }
}

function logoutBilik() {
  if (confirm('Anda yakin ingin menutup sesi Bilik TPS di komputer ini?')) {
    // Hapus otorisasi bilik dari browser
    localStorage.removeItem('bilik_token');
    localStorage.removeItem('bilik_info');
    
    // Hapus sisa sesi pemilih jika ada yang tersangkut
    localStorage.removeItem('pemilih_data'); 
    
    toast.success('Bilik berhasil ditutup.');
    
    // Kembalikan ke halaman login bilik
    router.push('/tpssekolah/login');
  }
}
</script>