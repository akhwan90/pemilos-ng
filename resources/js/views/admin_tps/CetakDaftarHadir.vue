<template>
	<div class="bg-gray-100 min-h-screen">
		<div class="max-w-[21cm] mx-auto bg-white p-8 shadow-sm print:p-0 print:shadow-none print:max-w-none">
			<!-- Header Cetak - Sembunyikan saat print -->
			<div class="mb-6 flex justify-between items-center print:hidden">
				<div class="flex items-center gap-4">
					<button @click="$router.push(auth.user?.level === 2 ? '/admin-sekolah/dpt' : '/admin-tps/dpt')" class="flex items-center text-gray-600 hover:text-gray-900">
						<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
						</svg>
						Kembali
					</button>
					<h1 class="text-2xl font-bold text-gray-800">Cetak Daftar Hadir</h1>
				</div>
				<button @click="printPage" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
					</svg>
					Cetak
				</button>
			</div>

			<div class="text-center mb-3">
				<h3>DAFTAR HADIR PEMILIH</h3>
				TPS : 
			</div>
			<table class="w-full">
				<thead>
					<tr>
						<th class="w-1/12 text-center border p-2">No</th>
						<th class="w-2/12 border p-2">NISN</th>
						<th	class="w-6/12 border p-2">Nama</th>
						<th class="w-3/12 border p-2">Tanda Tangan</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(item, index) in items" :key="index">
						<td class="border p-1">{{ index + 1 }}</td>
						<td class="border p-1">{{ item.nisn }}</td>
						<td class="border p-1">{{ item.nm_siswa }}</td>
						<td class="border p-1">{{ index + 1 }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';
import { useAuthStore } from '../../stores/auth';

const toast = useToast();
const auth = useAuthStore();

const items = ref([]);
const isLoading = ref(true);

onMounted(async () => {
	await fetchAllDpt();
});

async function fetchAllDpt() {
	isLoading.value = true;
	try {
        // limit 1000 untuk cetak semua tanpa pagination
		const res = await api.get(`/admin-sekolah/dpt?limit=1000`);
		items.value = res.data.data;
	} catch (error) {
		toast.error('Gagal memuat Data DPT untuk dicetak');
	} finally {
		isLoading.value = false;
	}
}

function printPage() {
	window.print();
}
</script>

<style scoped>
@media print {
	@page {
		size: A4 portrait;
		margin: 1cm;
	}
	
	body {
		-webkit-print-color-adjust: exact !important;
		print-color-adjust: exact !important;
	}

    .card-container {
        page-break-inside: avoid;
    }
}
</style>