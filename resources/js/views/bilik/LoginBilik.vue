<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center flex-col items-center">
        <img src="/asset/img/kpu_logo.png" alt="Logo KPU" class="h-16 w-auto mb-4" />
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
          Aktivasi Bilik TPS
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Silakan login menggunakan akun Admin TPS
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        
        <div v-if="errorMsg" class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700">{{ errorMsg }}</p>
            </div>
          </div>
        </div>

        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username TPS</label>
            <div class="mt-1">
              <input id="username" v-model="username" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1">
              <input id="password" v-model="password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loading ? 'Mengautentikasi...' : 'Aktivasi Bilik' }}
            </button>
          </div>
        </form>
        
        <div class="mt-6 text-center">
          <router-link to="/" class="text-sm text-gray-500 hover:text-gray-900">Kembali ke Beranda</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';

const router = useRouter();

const username = ref('');
const password = ref('');
const loading = ref(false);
const errorMsg = ref('');

async function handleLogin() {
  loading.value = true;
  errorMsg.value = '';
  
  try {
    // Kita gunakan endpoint login yang sudah ada. Backend akan mengembalikan data user beserta token.
    const res = await api.post('/login', {
      username: username.value,
      password: password.value
    });
    
    const { token, user } = res.data.data;
    
    // Pastikan hanya TPS (Level 3) yang boleh mengaktifkan bilik ini
    if (parseInt(user.level) !== 3) {
      errorMsg.value = "Hanya akun Admin TPS yang diizinkan mengaktifkan Bilik.";
      loading.value = false;
      return;
    }

    // Simpan identitas Bilik Suara ke LocalStorage (Kita bedakan key-nya dari login dashboard admin)
    localStorage.setItem('bilik_token', token);
    localStorage.setItem('bilik_info', JSON.stringify({
      id_tps: user.id_tps,
      npsn: user.npsn,
      nama_tps: user.nama_tps || 'TPS',
      is_luar_sekolah: !!user.is_tps_luar_sekolah
    }));
    
    // Arahkan langsung ke halaman input token (standby)
    router.push('/tpssekolah/token');
  } catch (error) {
    errorMsg.value = error.response?.data?.message || 'Gagal terhubung ke server.';
  } finally {
    loading.value = false;
  }
}
</script>