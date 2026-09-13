<template>
	<div class="space-y-6">
		<div class="flex items-center justify-between mb-4">
			<div>
				<h2 class="text-xl font-bold text-gray-800">Semua Data Siswa</h2>
				<p class="text-sm text-gray-500">Lihat data seluruh siswa dari berbagai sekolah (Global).</p>
			</div>
		</div>

		<BaseCard class="p-0">
		    <div class="p-2 bg-blue-200 text-sm rounded">
				Available column : tb_siswa.nisn, tb_siswa.nm_siswa, tb_siswa.jk, tb_siswa.kelas, tb_siswa.difabel, tb_siswa.npsn, tb_siswa.status, tb_siswa.tahun, tb_siswa.create_at, tb_siswa.hapus_time, tb_sekolah.nama_sekolah
			</div>
			<!-- Toolbar -->
			<div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-wrap gap-4">
				<div class="flex gap-2 items-center w-full">
					<!-- Filter Sekolah -->
						<!-- <select v-model="filterNpsn" @change="fetchSiswa(1)" class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
							<option value="">Semua Sekolah</option>
							<option v-for="sekolah in daftarSekolah" :key="sekolah.npsn" :value="sekolah.npsn">
								{{ sekolah.nama_sekolah || sekolah.nm_sekolah || sekolah.npsn }}
							</option>
						</select>
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
							<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
						</div> -->
						<form @submit.prevent="fetchSiswa(1)" class="flex gap-2 w-full">
						    <BaseInput placeholder="Query String" class="w-full" v-model="queryString"></BaseInput>
							<BaseButton type="submit">Go</BaseButton>
						</form>
				</div>

				<!-- <div class="relative w-full md:w-64">
					<input
						v-model="search"
						@keyup.enter="fetchSiswa(1)"
						type="text"
						placeholder="Cari nama atau NISN..."
						class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
					/>
					<svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</div> -->
			</div>

			<!-- Table -->
			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
						<tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
							<th class="px-6 py-4 font-medium">Aksi</th>
							<th class="px-6 py-4 font-medium">NISN</th>
							<th class="px-6 py-4 font-medium">Nama Siswa</th>
							<th class="px-6 py-4 font-medium">Sekolah</th>
							<th class="px-6 py-4 font-medium">Kelas</th>
							<th class="px-6 py-4 font-medium">Jenis Kelamin</th>
							<th class="px-6 py-4 font-medium">Difabel</th>
							<th class="px-6 py-4 font-medium">Status</th>
							<th class="px-6 py-4 font-medium">Tahun Input</th>
							<th class="px-6 py-4 font-medium">Created At</th>
							<th class="px-6 py-4 font-medium">Hapus Time</th>
							<th class="px-6 py-4 font-medium">Hapus User Id</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 text-xs">
						<tr v-if="siswa.length === 0">
							<td colspan="12" class="px-6 py-12 text-center text-gray-500">
								<div class="flex flex-col items-center justify-center">
									<svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
									</svg>
									<p>Tidak ada data siswa ditemukan.</p>
								</div>
							</td>
						</tr>
						<tr v-for="item in siswa" :key="item.id_siswa" class="hover:bg-gray-50 transition-colors">
							<td class="px-6 py-4 flex text-sm font-medium gap-2">
								<button @click.prevent="openModalSiswa(item)" class="text-blue-600 hover:text-blue-900 focus:outline-none bg-blue-50 p-1.5 rounded" title="Hapus Permanen">
									Edit
								</button>
								<button @click="deleteSiswa(item)" class="text-red-600 hover:text-red-900 focus:outline-none bg-red-50 p-1.5 rounded" title="Hapus Permanen">
									Hapus
								</button>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<div class="font-medium text-gray-900">{{ item.nisn }}</div>
							</td>
							<td class="px-6 py-4">
								<div class="font-medium text-gray-900">{{ item.nm_siswa }}</div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-500">
								{{ item.nama_sekolah || item.npsn || '-' }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap">{{ item.kelas }}</td>
							<td class="px-6 py-4">{{ item.jk }}</td>
							<td class="px-6 py-4">{{ item.difabel }}</td>
							<td class="px-6 py-4">{{ item.status }}</td>
							<td class="px-6 py-4">{{ item.tahun }}</td>
							<td class="px-6 py-4">{{ item.create_at }}</td>
							<td class="px-6 py-4">{{ item.hapus_time }}</td>
							<td class="px-6 py-4">{{ item.hapus_user_id }}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			<div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between" v-if="pagination.total > 0">
				<div class="text-sm text-gray-500">
					Menampilkan <span class="font-medium text-gray-900">{{ pagination.from }}</span> sampai <span class="font-medium text-gray-900">{{ pagination.to }}</span> dari
					<span class="font-medium text-gray-900">{{ pagination.total }}</span> data
				</div>
				<div class="flex gap-2">
					<button
						@click="fetchSiswa(pagination.current_page - 1)"
						:disabled="pagination.current_page === 1"
						class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						Sebelumnya
					</button>
					<button
						@click="fetchSiswa(pagination.current_page + 1)"
						:disabled="pagination.current_page === pagination.last_page"
						class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						Selanjutnya
					</button>
				</div>
			</div>
		</BaseCard>

		<ModalSiswa v-model="modalSiswa" :siswa="selectedSiswa" @saved="fetchSiswa(1)" />
	</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import BaseCard from '../../components/BaseCard.vue';
import BaseSelect from '../../components/BaseSelect.vue';
import BaseInput from '../../components/BaseInput.vue';
import BaseButton from '../../components/BaseButton.vue';

import ToastNotification from '../../components/ToastNotification.vue';
import ModalSiswa from './modals/ModalEditSiswa.vue';

const loading = ref(false);
const siswa = ref([]);
const daftarSekolah = ref([]);
const filterNpsn = ref('');
const search = ref('');
const queryString = ref('');
const pagination = ref({
	current_page: 1,
	last_page: 1,
	total: 0,
	from: 0,
	to: 0,
});
const modalSiswa = ref(false);
const selectedSiswa = ref({});

function openModalSiswa(data) {
	selectedSiswa.value = data;
	modalSiswa.value = true;
}

// Use the Toast Component logically assuming it's globally registered or imported
const toast = ref({ show: false, message: '', type: 'success' });
const showToast = (message, type = 'success') => {
	// Try to use a global bus or store if available, otherwise fallback to alert for simplicity here
	// Assuming useToast is not easily available, we fallback to custom event or just console.error
	console.log(message);
};

// const fetchSekolah = async () => {
// 	try {
// 		const response = await api.get('/admin/data-sekolah', { params: { no_pagination: 1 } });

// 		// Support paginated or unpaginated response
// 		if (response.data && Array.isArray(response.data)) {
// 			daftarSekolah.value = response.data;
// 		} else if (response.data && response.data.data && Array.isArray(response.data.data)) {
// 			daftarSekolah.value = response.data.data;
// 		} else {
// 			daftarSekolah.value = [];
// 		}
// 	} catch (error) {
// 		console.error('Error fetching data sekolah:', error);
// 	}
// };

const fetchSiswa = async (page = 1) => {
	loading.value = true;
	try {
		const params = {
			page,
		};

		if (search.value) params.cari = search.value;
        if (filterNpsn.value) params.npsn = filterNpsn.value;
        if (queryString.value) params.query = queryString.value;

		const response = await api.get('/admin/data-siswa-global', { params });

		siswa.value = response.data.data;
		pagination.value = {
			current_page: response.data.current_page,
			last_page: response.data.last_page,
			total: response.data.total,
			from: response.data.from,
			to: response.data.to,
		};
	} catch (error) {
		console.error('Error fetching data siswa global:', error);
		showToast('Gagal memuat data siswa', 'error');
	} finally {
		loading.value = false;
	}
};

// Debounce search
// let searchTimeout;
// watch(search, () => {
// 	clearTimeout(searchTimeout);
// 	searchTimeout = setTimeout(() => {
// 		fetchSiswa(1);
// 	}, 500);
// });

const deleteSiswa = async (item) => {
	if (confirm(`Apakah Anda yakin ingin menghapus data siswa "${item.nm_siswa}" secara permanen? Data yang telah dihapus tidak dapat dikembalikan.`)) {
		try {
			await api.delete(`/admin/data-siswa-global/${item.id}`);
			showToast('Data siswa berhasil dihapus secara permanen', 'success');
			fetchSiswa(pagination.value.current_page);
		} catch (error) {
			console.error('Error deleting data siswa:', error);
			showToast(error.response?.data?.message || 'Gagal menghapus data siswa', 'error');
		}
	}
};

onMounted(async () => {
	// await fetchSekolah();
	fetchSiswa();
});
</script>
