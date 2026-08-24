<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center flex-col items-center">
        <img src="/images/kpu_logo.png" alt="Logo KPU" class="h-16 w-auto mb-4" />
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

        <form class="space-y-6" @submit.prevent="handleLogin" v-if="isJadwalAktif">
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username TPS</label>
            <div class="mt-1">
              <input id="username" v-model="username" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1 relative">
              <input id="password" v-model="password" :type="showPassword ? 'text' : 'password'" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-10" />
              <button type="button" @click="showPassword = !showPassword" tabindex="-1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.71-1.581c4.478 0 8.268 2.943 9.543 7a9.97 9.97 0 01-1.563 3.029m-5.858-.908a3 3 0 00-4.243-4.243"
                  />
                </svg>
              </button>
            </div>
          </div>

          <div class="mb-6">
						<label class="block text-sm font-medium text-gray-700 mb-1">Captcha</label>
						<div class="flex items-center gap-3">
							<div class="bg-gray-100 p-1 rounded border min-h-[40px] min-w-[120px] flex items-center justify-center cursor-pointer flex-shrink-0" @click="refreshCaptcha" title="Klik untuk refresh captcha">
								<img v-if="captchaImage" :src="captchaImage" alt="captcha" class="max-h-full" />
								<span v-else class="text-xs text-gray-400">Loading...</span>
							</div>
							<button type="button" @click="refreshCaptcha" class="text-sm text-indigo-600 hover:text-indigo-800 p-2 flex-shrink-0">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
								</svg>
							</button>
							<input v-model="captchaValue" type="text" required placeholder="Hasil" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase" />
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
        <div v-else>
          <p class="text-sm text-white bg-red-400 border-red-800 p-4 rounded">Belum ada jadwal pemilihan yang aktif</p>
        </div>

        <div class="mt-6 text-center">
          <router-link to="/" class="text-sm text-gray-500 hover:text-gray-900">Kembali ke Beranda</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import axios from 'axios';

const router = useRouter();

const username = ref('');
const password = ref('');
const showPassword = ref(false);
const captchaValue = ref('');
const captchaKey = ref('');
const captchaImage = ref('');
const loading = ref(false);
const errorMsg = ref('');
const isJadwalAktif = ref(false);

async function refreshCaptcha() {
	try {
		const res = await axios.get('/captcha/api/math');
		if (res.data && res.data.img) {
			if (typeof res.data.img === 'string') {
				captchaImage.value = res.data.img.startsWith('data:image')
					? res.data.img
					: 'data:image/jpeg;base64,' + res.data.img;
			} else {
				captchaImage.value = '';
			}
		}
		captchaKey.value = res.data.key;
		captchaValue.value = '';
	} catch (e) {
		console.error('Gagal memuat captcha:', e);
	}
}

async function cekJadwal() {
  try {
    const res = await api.get('/cek-jadwal-public');
    console.log(res.data);
    isJadwalAktif.value = res.data.status;
  } catch (error) {
    console.error('Gagal memuat jadwal:', error);
  }
}

onMounted(() => {
  cekJadwal();
	refreshCaptcha();
});

async function handleLogin() {
  loading.value = true;
  errorMsg.value = '';

  try {
    const res = await api.post('/login', {
      username: username.value,
      password: password.value,
      captcha: captchaValue.value,
      captcha_key: captchaKey.value
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
    if (error.response?.status === 422 && error.response?.data?.errors?.captcha) {
        errorMsg.value = error.response.data.errors.captcha[0];
    } else {
        errorMsg.value = error.response?.data?.message || 'Gagal terhubung ke server.';
    }
    refreshCaptcha();
  } finally {
    loading.value = false;
  }
}
</script>
