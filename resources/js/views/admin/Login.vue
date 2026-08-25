<template>
	<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
		<div class="max-w-md w-full">
			<div class="text-center mb-8">
				<div class="w-16 h-16 mx-auto mb-4">
					<img src="/images/kpu_logo.png" alt="Logo KPU" class="w-full h-full object-contain" />
				</div>
				<h1 class="text-2xl font-bold text-gray-800">PEMILOS Admin</h1>
				<p class="text-sm text-gray-500 mt-1">Pemilihan Ketua OSIS Berbasis Elektronik</p>
			</div>
			<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
				<h2 class="text-lg font-semibold text-gray-800 mb-6">Login Admin Sekolah (PPO) dan Admin TPS</h2>
				<div v-if="errorMsg" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
					{{ errorMsg }}
				</div>
				<form @submit.prevent="handleLogin">
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
						<input v-model="username" type="text" required autocomplete="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
					</div>
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
						<div class="relative">
							<input
								v-model="password"
								:type="showPassword ? 'text' : 'password'"
								required
								autocomplete="current-password"
								class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm pr-10"
							/>
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

					<button type="submit" :disabled="loading" class="w-full py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors flex items-center justify-center gap-2">
						<svg v-if="loading" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
							<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
							<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
						</svg>
						{{ loading ? 'Memproses...' : 'Masuk' }}
					</button>
				</form>
				<div class="mt-4 text-center">
					<router-link to="/" class="text-sm text-gray-500 hover:text-indigo-600">Kembali ke Beranda</router-link>
				</div>
			</div>

			<div class="p-4 bg-blue-400 text-white rounded-lg mt-4 text-sm text-center">
				Anda dapat menggunakan username dan password yang telah terdaftar sebelumnya. Jika Anda mengalami kendala, hubungi di grup WhatsApp Pemilos.
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../services/api';
import axios from 'axios';

const router = useRouter();
const auth = useAuthStore();
const username = ref('');
const password = ref('');
const showPassword = ref(false);
const captchaValue = ref('');
const captchaKey = ref('');
const captchaImage = ref('');
const loading = ref(false);
const errorMsg = ref('');

async function refreshCaptcha() {
	try {
		// Gunakan axios langsung agar tidak terkena prefix baseURL '/api'
		const res = await axios.get('/captcha/api/math');
		if (res.data && res.data.img) {
			// Periksa apakah res.data.img berupa object atau string (base64)
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

onMounted(() => {
	refreshCaptcha();
});

async function handleLogin() {
	loading.value = true;
	errorMsg.value = '';
	try {
		// Memodifikasi pemanggilan auth.login
		const res = await api.post('/login', {
			username: username.value,
			password: password.value,
			captcha: captchaValue.value,
			captcha_key: captchaKey.value,
		});

		// Set data auth secara manual jika sukses login
		auth.user = res.data.data.user;
		auth.token = res.data.data.token;
		localStorage.setItem('admin_token', auth.token);
		localStorage.setItem('admin_user', JSON.stringify(auth.user));

		// Gunakan window.location untuk force redirect sekaligus me-refresh state router
		// jika Vue Router (navigation guard) terkadang telat membaca perubahan localStorage
		if (auth.user && parseInt(auth.user.level) === 2) {
			window.location.href = '/admin-sekolah/dashboard';
		} else if (auth.user && parseInt(auth.user.level) === 3) {
			window.location.href = '/admin-tps/dashboard';
		} else {
			window.location.href = '/admin/dashboard';
		}
	} catch (e) {
		if (e.response?.status === 422 && e.response?.data?.errors?.captcha) {
			errorMsg.value = e.response.data.errors.captcha[0];
		} else {
			errorMsg.value = e.response?.data?.message || 'Login gagal. Periksa username dan password.';
		}
		// Refresh captcha jika login gagal (agar keamanan tetap terjaga)
		refreshCaptcha();
	} finally {
		loading.value = false;
	}
}
</script>
