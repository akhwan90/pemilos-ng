<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center flex-col items-center">
        <img src="/images/kpu_logo.png" alt="Logo" class="h-20 w-auto mb-4" />
        <h2 class="text-center text-4xl font-extrabold text-gray-900 tracking-wider">
          BILIK SUARA {{ bilikInfo?.is_luar_sekolah ? '(Luar Sekolah)' : '' }}
        </h2>
        <p class="mt-2 text-center text-lg text-indigo-700 font-bold bg-indigo-100 px-4 py-1 rounded-full">
          {{ bilikInfo?.nama_tps || 'TPS Aktif' }}
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-lg">
      <div class="bg-white py-10 px-4 shadow-xl border-t-4 border-indigo-600 sm:rounded-lg sm:px-10">

        <div class="text-center mb-8">
          <h3 class="text-lg font-medium text-gray-700">Masukkan Data Pemilih Anda</h3>
          <p class="text-sm text-gray-500 mt-1">Silakan masukkan {{ bilikInfo?.is_luar_sekolah ? 'NISN dan Token' : 'Token' }} Anda</p>
        </div>

        <!-- Loading State -->
        <div v-if="isChecking" class="text-center py-10">
            <svg class="animate-spin h-10 w-10 text-indigo-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500">Memeriksa status pemilihan...</p>
        </div>

        <div v-else-if="!isPemilihanOpen" class="text-center py-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
              <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Pemilihan Belum Dibuka</h3>
            <p class="text-gray-500">{{ statusMessage }}</p>
            <button @click="checkStatus" class="mt-6 text-indigo-600 font-medium hover:text-indigo-800 flex items-center justify-center mx-auto">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
              Cek Ulang Status
            </button>
        </div>

        <form v-else @submit.prevent="verifyToken" class="space-y-6">
          <!-- Input NISN khusus Luar Sekolah -->
          <div v-if="bilikInfo?.is_luar_sekolah">
            <label class="block text-sm font-medium text-gray-700 text-center mb-2">NISN Anda</label>
            <div class="flex justify-center">
              <input
                ref="nisnInputRef"
                v-model="nisnPemilih"
                type="text"
                class="appearance-none block w-full text-center text-2xl tracking-[0.2em] font-mono px-2 py-3 border-2 border-indigo-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50"
                placeholder="Ketik NISN Anda"
                required
                autofocus
                autocomplete="off"
              />
            </div>
          </div>

          <!-- Input Token -->
          <div>
            <label v-if="bilikInfo?.is_luar_sekolah" class="block text-sm font-medium text-gray-700 text-center mb-2">Token Pemilihan</label>
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
                :autofocus="!bilikInfo?.is_luar_sekolah"
                autocomplete="off"
              />
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading || tokenPemilih.length < 5 || (bilikInfo?.is_luar_sekolah && !nisnPemilih)" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:bg-gray-400 transition-colors">
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
const nisnInputRef = ref(null);
const nisnPemilih = ref('');

const isChecking = ref(true);
const isPemilihanOpen = ref(false);
const statusMessage = ref('');

onMounted(() => {
  // Ambil info TPS dari LocalStorage saat Bilik ini dinyalakan
  const savedInfo = localStorage.getItem('bilik_info');
  if (savedInfo) {
    bilikInfo.value = JSON.parse(savedInfo);
    // Cek status saat pertama kali load
    checkStatus();
  } else {
    // Jika tidak ada data bilik (belum login admin tps), paksa kembali ke login bilik
    toast.error('Bilik TPS belum diaktifkan oleh Admin!');
    router.push('/tpssekolah/login');
  }
});

async function checkStatus() {
    isChecking.value = true;
    try {
        const res = await api.get('/bilik/status', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('bilik_token')}`
            }
        });
        isPemilihanOpen.value = res.data.is_open;
        statusMessage.value = res.data.message;

        if (isPemilihanOpen.value) {
            // Beri delay sedikit agar elemen DOM form-nya ter-render
            setTimeout(() => {
                if (bilikInfo.value?.is_luar_sekolah) {
                    nisnInputRef.value?.focus();
                } else {
                    tokenInputRef.value?.focus();
                }
            }, 100);
        }
    } catch (e) {
        toast.error(e.response?.data?.message);
        isPemilihanOpen.value = false;
        statusMessage.value = 'Gagal terhubung ke server.';
    } finally {
        isChecking.value = false;
    }
}

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

    // Jika Token Valid, simpan data sesi si Pemilih tersebut beserta log_id
    localStorage.setItem('pemilih_data', JSON.stringify({
        ...res.data.siswa,
        log_id: res.data.log_id
    }));

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
