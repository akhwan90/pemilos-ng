<template>
	<div class="min-h-screen bg-gray-50 flex flex-col font-sans">
		<!-- Navbar / Header (Reusable Component) -->
		<PublicHeader />

		<!-- Pop Up Notifikasi Pembaruan -->
		<Transition name="fade">
			<div v-if="showUpdatePopup" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
				<div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all relative">
					<div class="bg-blue-600 px-6 py-4 flex justify-between items-center">
						<h3 class="text-xl font-bold text-white flex items-center gap-2">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
							</svg>
							Informasi Pembaruan
						</h3>
					</div>
					<div class="p-6 md:p-8">
						<p class="text-gray-700 text-lg leading-relaxed mb-6">
							Aplikasi <span class="font-bold text-blue-600">e-Pemilos</span> kini hadir dengan <strong>tampilan baru yang lebih modern</strong>! <br /><br />
							Jangan khawatir, seluruh <strong>alur kerja, menu, dan akun autentikasi Anda masih tetap sama</strong> seperti sebelumnya.
						</p>
						<label class="flex items-center gap-3 mb-6 cursor-pointer select-none group"
							><div class="relative flex items-center justify-center w-6 h-6">
								<input type="checkbox" v-model="dontShowAgain" class="peer appearance-none w-5 h-5 border-2 border-gray-300 rounded focus:ring-0 checked:bg-blue-600 checked:border-blue-600 transition-colors" /><svg
									class="absolute w-3.5 h-3.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity"
									fill="none"
									viewBox="0 0 24 24"
									stroke="currentColor"
									stroke-width="3"
								>
									<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
								</svg>
							</div>
							<span class="text-gray-600 font-medium group-hover:text-gray-900 transition-colors">Jangan tampilkan pesan ini lagi</span></label
						>
						<button @click="closePopup" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-xl transition-colors shadow-md">Saya Mengerti</button>
					</div>
				</div>
			</div>
		</Transition>

		<!-- HERO SECTION (Desain Modern Split) -->
		<section class="relative bg-gradient-to-b from-blue-50 to-white overflow-hidden flex items-center pt-16 lg:pt-24 pb-20 lg:pb-32 border-b border-gray-100">
			<!-- Ornamen Latar Belakang (Blob) -->
			<div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-100 opacity-50 blur-3xl mix-blend-multiply pointer-events-none"></div>
			<div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-emerald-100 opacity-50 blur-3xl mix-blend-multiply pointer-events-none"></div>

			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
				<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
					<!-- Kolom Teks (Kiri) -->
					<div class="lg:col-span-7">
						<a href="#" class="inline-flex items-center gap-2 bg-white/80 backdrop-blur border border-blue-100 text-blue-800 px-3 py-1.5 rounded-full text-xs font-bold mb-8 hover:bg-blue-50 shadow-sm transition-colors">
							<span class="bg-blue-600 text-white px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider">Pemilos {{ currentYear }}</span>
							Pesta Demokrasi Pelajar Kulon Progo
						</a>

						<h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 tracking-tight mb-6 leading-tight">
							Membangun
							<span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-emerald-500">Demokrasi</span><br />Dari Bangku Sekolah
						</h2>

						<p class="text-lg text-gray-600 leading-relaxed mb-8 pr-0 lg:pr-12 text-justify">
							e-Pemilos adalah platform e-Voting Pemilihan Ketua OSIS yang cepat, aman, dan transparan. Diinisiasi oleh KPU Kulon Progo sejak tahun 2020 sebagai wadah edukasi politik bagi pemilih pemula yang ramah lingkungan.
						</p>

						<!-- Tombol Aksi Hero -->
						<div class="flex flex-col sm:flex-row flex-wrap gap-4 mb-8">
							<router-link
								to="/tpssekolah/login"
								class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-base font-bold rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5"
							>
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
								Masuk ke TPS
							</router-link>

							<router-link
								to="/tpsluarsekolah"
								class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white hover:bg-gray-50 border-2 border-gray-200 text-gray-700 text-base font-bold rounded-xl shadow-sm transition-all transform hover:-translate-y-0.5"
							>
								TPS Luar Sekolah
							</router-link>

							<router-link
								to="/admin/login"
								class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-800 hover:bg-slate-900 text-white text-base font-bold rounded-xl shadow-sm transition-all transform hover:-translate-y-0.5"
							>
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
								</svg>
								Login Admin
							</router-link>
						</div>

						<!-- Info Kolaborator / Pendukung -->
						<!-- <div class="flex items-center gap-4 text-sm text-gray-500 font-medium">
							<span>Didukung oleh:</span>
							<div class="flex gap-2 opacity-60">
								<span>KPU</span>
								<span>•</span>
								<span>Diskominfo KP</span>
								<span>•</span>
								<span>Kesbangpol</span>
								<span>•</span>
								<span>Kemenag</span>
							</div>
						</div> -->
					</div>

					<!-- Kolom Gambar Ilustrasi (Kanan) -->
					<div class="lg:col-span-5 relative hidden lg:block">
						<div class="absolute inset-0 bg-gradient-to-tr from-blue-100 to-emerald-50 rounded-[3rem] rotate-3 scale-105 -z-10"></div>

						<!-- Inline SVG Illustration - Modern E-Voting Concept -->
						<div class="w-full h-auto drop-shadow-2xl relative z-10 p-4 transform hover:scale-105 transition-transform duration-700">
							<svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
								<!-- Glowing Background Elements -->
								<circle cx="200" cy="200" r="160" fill="url(#grad_bg)" fill-opacity="0.2" />
								<circle cx="200" cy="200" r="120" stroke="url(#grad_bg)" stroke-width="2" stroke-dasharray="10 10" stroke-opacity="0.5" />

								<!-- Main Tablet / Device -->
								<rect x="80" y="50" width="240" height="320" rx="20" fill="white" stroke="#E5E7EB" stroke-width="4" />
								<rect x="85" y="55" width="230" height="310" rx="16" fill="#F8FAFC" />

								<!-- Screen Content - Header -->
								<rect x="100" y="80" width="120" height="12" rx="6" fill="#E2E8F0" />
								<circle cx="290" cy="86" r="12" fill="#DBEAFE" />
								<path d="M285 86l3 3 7-7" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

								<!-- E-Voting Ballot Cards -->
								<!-- Card 1 -->
								<rect x="100" y="120" width="200" height="60" rx="12" fill="white" stroke="#3B82F6" stroke-width="2" class="animate-pulse" />
								<rect x="115" y="135" width="30" height="30" rx="8" fill="#DBEAFE" />
								<rect x="160" y="135" width="80" height="8" rx="4" fill="#64748B" />
								<rect x="160" y="155" width="50" height="6" rx="3" fill="#94A3B8" />
								<circle cx="270" cy="150" r="12" fill="#3B82F6" />
								<path d="M265 150l3 3 7-7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

								<!-- Card 2 -->
								<rect x="100" y="195" width="200" height="60" rx="12" fill="white" stroke="#E2E8F0" stroke-width="1" />
								<rect x="115" y="210" width="30" height="30" rx="8" fill="#F1F5F9" />
								<rect x="160" y="210" width="80" height="8" rx="4" fill="#CBD5E1" />
								<rect x="160" y="230" width="50" height="6" rx="3" fill="#E2E8F0" />
								<circle cx="270" cy="225" r="10" stroke="#CBD5E1" stroke-width="2" />

								<!-- Card 3 -->
								<rect x="100" y="270" width="200" height="60" rx="12" fill="white" stroke="#E2E8F0" stroke-width="1" />
								<rect x="115" y="285" width="30" height="30" rx="8" fill="#F1F5F9" />
								<rect x="160" y="285" width="80" height="8" rx="4" fill="#CBD5E1" />

								<!-- Finger / Cursor Interaction -->
								<g transform="translate(250, 150)">
									<path
										d="M30 45 L35 25 Q36 20 40 20 Q44 20 45 25 L45 35 Q46 32 49 32 Q53 32 54 36 L54 38 Q55 36 58 36 Q62 36 63 40 L63 42 Q64 40 68 40 Q72 40 73 45 L75 60 Q75 70 65 75 L45 80 Q35 80 30 70 Z"
										fill="#FDE68A"
										stroke="#F59E0B"
										stroke-width="2"
									/>
									<path d="M40 20 Q40 10 30 15" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" />
								</g>

								<!-- Abstract Floating Elements (Data/Security) -->
								<rect x="35" y="140" width="40" height="40" rx="10" fill="#10B981" opacity="0.9" transform="rotate(-15 35 140)" />
								<path d="M45 155 L52 162 L65 147" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" transform="rotate(-15 35 140)" />

								<circle cx="340" cy="110" r="25" fill="#F59E0B" opacity="0.9" />
								<path d="M330 110 H350 M340 100 V120" stroke="white" stroke-width="3" stroke-linecap="round" />

								<defs>
									<linearGradient id="grad_bg" x1="0" y1="0" x2="400" y2="400" gradientUnits="userSpaceOnUse">
										<stop stop-color="#3B82F6" />
										<stop offset="1" stop-color="#10B981" />
									</linearGradient>
								</defs>
							</svg>
						</div>

						<!-- Floating Badge Card -->
						<div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-xl shadow-xl border border-gray-100 animate-bounce-slow">
							<div class="flex items-center gap-3">
								<div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
									</svg>
								</div>
								<div>
									<div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Status Server</div>
									<div class="text-sm font-bold text-gray-900">Online & Stabil</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- STATISTIK SECTION (Modern Cards) -->
		<section class="bg-white py-12 relative z-20 -mt-16 mx-4 lg:mx-auto max-w-5xl rounded-2xl shadow-xl shadow-gray-200/50 border border-gray-100">
			<div class="px-6 lg:px-12 grid grid-cols-1 md:grid-cols-3 gap-8 divide-y md:divide-y-0 md:divide-x divide-gray-100">
				<div class="text-center pt-4 md:pt-0">
					<div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-800 mb-2">
						{{ jml_sekolah }}
					</div>
					<div class="text-sm font-bold uppercase tracking-widest text-gray-500">Sekolah Peserta</div>
				</div>
				<div class="text-center pt-8 md:pt-0">
					<div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-emerald-700 mb-2">
						{{ jml_pilihan }}
					</div>
					<div class="text-sm font-bold uppercase tracking-widest text-gray-500">Kandidat Paslon</div>
				</div>
				<div class="text-center pt-8 md:pt-0">
					<div class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-amber-600 mb-2">
						{{ jml_tps }}
					</div>
					<div class="text-sm font-bold uppercase tracking-widest text-gray-500">Bilik Suara (TPS)</div>
				</div>
			</div>
		</section>

		<!-- FEATURES SECTION -->
		<section class="py-24 bg-gray-50">
			<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
				<div class="text-center mb-16 max-w-2xl mx-auto">
					<h2 class="text-3xl font-extrabold text-gray-900 mb-4">
						Mengapa menggunakan
						<span class="text-blue-600">e-Pemilos?</span>
					</h2>
					<p class="text-gray-500 text-lg">Platform modern yang dirancang khusus untuk memastikan integritas dan kemudahan dalam berdemokrasi.</p>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
					<!-- Feature 1 -->
					<div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 group">
						<div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
							<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
							</svg>
						</div>
						<h5 class="text-xl font-bold text-gray-900 mb-2">Keamanan Tinggi</h5>
						<p class="text-gray-500 leading-relaxed">Dilengkapi dengan Double-Vote Protection dan manajemen sesi token yang ketat.</p>
					</div>

					<!-- Feature 2 -->
					<div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 group">
						<div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
							<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
							</svg>
						</div>
						<h5 class="text-xl font-bold text-gray-900 mb-2">Cepat & Realtime</h5>
						<p class="text-gray-500 leading-relaxed">Perhitungan hasil pencoblosan langsung terlihat secara realtime tanpa perlu rekapitulasi manual.</p>
					</div>

					<!-- Feature 3 -->
					<div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 group">
						<div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
							<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
							</svg>
						</div>
						<h5 class="text-xl font-bold text-gray-900 mb-2">Ramah Lingkungan</h5>
						<p class="text-gray-500 leading-relaxed">Mendukung gerakan Paperless (Zero Kertas Suara) yang memangkas beban biaya logistik pemilihan.</p>
					</div>

					<!-- Feature 4 -->
					<div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 group">
						<div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
							<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
							</svg>
						</div>
						<h5 class="text-xl font-bold text-gray-900 mb-2">Desain Responsif</h5>
						<p class="text-gray-500 leading-relaxed">Tampilan bilik suara beradaptasi mulus di PC, Tablet TPS, maupun Layar Ponsel Luar Sekolah.</p>
					</div>

					<!-- Feature 5 -->
					<div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 group">
						<div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
							<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path
									stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
								/>
							</svg>
						</div>
						<h5 class="text-xl font-bold text-gray-900 mb-2">Akses Multi-Level</h5>
						<p class="text-gray-500 leading-relaxed">Satu aplikasi terintegrasi untuk Panitia Kabupaten, Admin Sekolah, dan Petugas TPS.</p>
					</div>

					<!-- Feature 6 -->
					<div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow border border-gray-100 group">
						<div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
							<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
							</svg>
						</div>
						<h5 class="text-xl font-bold text-gray-900 mb-2">Tertib & Transparan</h5>
						<p class="text-gray-500 leading-relaxed">Dilengkapi dengan arsip sejarah kepemiluan dan laporan cetak C1/C2 yang patuh aturan.</p>
					</div>
				</div>
			</div>
		</section>

		<!-- CALL TO ACTION (CTA) PANITIA -->
		<section class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-16">
			<div class="max-w-4xl mx-auto px-4 text-center">
				<h2 class="text-3xl font-bold mb-4">Anda Panitia Penyelenggara Pemilos?</h2>
				<p class="text-slate-300 mb-8 max-w-2xl mx-auto text-lg">Silakan akses Dashboard Admin untuk mendaftarkan Kandidat, mengelola Daftar Pemilih Tetap (DPT), atau mencetak Token Pemilihan.</p>
				<router-link to="/admin/login" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-slate-900 hover:bg-gray-100 text-base font-bold rounded-xl shadow-lg transition-all">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
					</svg>
					Masuk ke Dashboard Admin
				</router-link>
			</div>
		</section>

		<!-- Footer (Reusable) -->
		<PublicFooter />
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import PublicHeader from '../../components/public/PublicHeader.vue';
import PublicFooter from '../../components/public/PublicFooter.vue';

const currentYear = computed(() => new Date().getFullYear());

// State untuk menampilkan Pop Up Pembaruan
const showUpdatePopup = ref(true);
const dontShowAgain = ref(false);

onMounted(() => {
	// Cek localStorage agar popup tidak muncul terus setiap refresh
	const hasSeenPopup = localStorage.getItem('pemilos_update_seen');
	if (hasSeenPopup) dontShowAgain.value = true;
	if (hasSeenPopup) {
		showUpdatePopup.value = false;
	}
});

const closePopup = () => {
	showUpdatePopup.value = false;
	if (dontShowAgain.value) {
		localStorage.setItem('pemilos_update_seen', 'true');
	} else {
		localStorage.removeItem('pemilos_update_seen');
	}
};

// Data dummy untuk statistik (bisa diubah agar me-load dari API Backend)
const jml_sekolah = ref(0);
const jml_pilihan = ref(0);
const jml_tps = ref(0);

// Simulasi load data statistik (opsional)
onMounted(() => {
	// Ganti dengan axios/fetch call Anda nantinya
	jml_sekolah.value = 45;
	jml_pilihan.value = 112;
	jml_tps.value = 248;
});

const handleImageError = (e) => {
	e.target.style.display = 'none';
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
	transition:
		opacity 0.3s ease,
		transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
	opacity: 0;
	transform: scale(0.95);
}
.fallback-bg {
	background-color: transparent;
}
.animate-bounce-slow {
	animation: bounce 3s infinite;
}
@keyframes bounce {
	0%,
	100% {
		transform: translateY(-5%);
		animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
	}
	50% {
		transform: translateY(0);
		animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
	}
}
</style>
