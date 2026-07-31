<template>
	<div class="space-y-6">
		<div class="flex items-center justify-between mb-4">
			<div>
				<h2 class="text-xl font-bold text-gray-800">Log Approval Pindah Sekolah</h2>
				<p class="text-sm text-gray-500">Melihat riwayat mutasi siswa antar sekolah di tahun berjalan</p>
			</div>
		</div>

		<BaseCard class="p-0">
			<!-- Toolbar -->
			<div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-wrap gap-4">
				<div class="relative w-full md:w-64">
					<input v-model="search" @keyup.enter="fetchLogs(1)" type="text" placeholder="Cari NISN / Nama Siswa..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" />
					<svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</div>
			</div>

			<!-- Table -->
			<div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
						<tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
							<th class="px-6 py-4 font-medium">No</th>
							<th class="px-6 py-4 font-medium">NPSN Pemohon</th>
							<th class="px-6 py-4 font-medium">NISN</th>
							<th class="px-6 py-4 font-medium">Terdaftar Di</th>
							<th class="px-6 py-4 font-medium">Nama Siswa Baru</th>
							<th class="px-6 py-4 font-medium">Gender</th>
							<th class="px-6 py-4 font-medium">Kelas Baru</th>
							<th class="px-6 py-4 font-medium">Status</th>
							<th class="px-6 py-4 font-medium">Tanggal Dibuat</th>
							<th class="px-6 py-4 font-medium">Tanggal Approve</th>
							<th class="px-6 py-4 font-medium">User Approve</th>
							<th class="px-6 py-4 font-medium">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 text-sm">
						<tr v-if="loading">
							<td colspan="12" class="px-6 py-12 text-center text-gray-500">
								<div class="flex justify-center items-center gap-2">
									<svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
										<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
										<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
									</svg>
									Memuat data...
								</div>
							</td>
						</tr>
						<tr v-else-if="logs.length === 0">
							<td colspan="12" class="px-6 py-12 text-center text-gray-500">
								<div class="flex flex-col items-center justify-center">
									<svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
									</svg>
									<p>Belum ada riwayat approval pindah sekolah tahun ini.</p>
								</div>
							</td>
						</tr>
						<tr v-else v-for="(item, index) in logs" :key="item.id" class="hover:bg-gray-50 transition-colors">
							<td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">
								{{ pagination.from + index }}
							</td>
							<td class="px-6 py-4">
								<div class="font-medium text-gray-900">{{ item.user_pemohon_npsn }}</div>
								<div class="text-xs text-gray-500">{{ item.nama_sekolah_pemohon || '-' }}</div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-900">
								{{ item.nisn }}
							</td>
							<td class="px-6 py-4">
								<div class="font-medium text-gray-900">{{ item.npsn }}</div>
								<div class="text-xs text-gray-500">{{ item.nama_sekolah_tujuan || '-' }}</div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-900">
								{{ item.nama_baru }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-500">
								{{ item.jk_baru === 'L' ? 'Laki-laki' : (item.jk_baru === 'P' ? 'Perempuan' : item.jk_baru) }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-900">
								{{ item.kelas_baru || '-' }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<StatusBadge 
									:status="Number(item.status) === 1 ? 'disetujui' : (Number(item.status) === 2 ? 'ditolak' : 'baru')" 
									:label="Number(item.status) === 1 ? 'Sudah Diapprove' : (Number(item.status) === 2 ? 'Ditolak' : 'Belum Diapprove')" 
								/>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-500">
								{{ formatDate(item.created_at) }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-500">
								{{ Number(item.status) === 1 ? formatDate(item.disetujui_at) : '-' }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-700">
								{{ Number(item.status) === 1 ? (item.user_pengapprove || '-') : '-' }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-center">
								<BaseButton 
									v-if="Number(item.status) === 0"
									variant="success"
									@click="approvePermohonan(item)" 
									:disabled="isProcessing"
									:loading="isProcessing"
									class="inline-flex items-center gap-1.5 !px-3 !py-1.5 !text-xs"
								>
									<svg v-if="!isProcessing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
									Approve
								</BaseButton>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			<div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between" v-if="pagination.total > 0">
				<div class="text-sm text-gray-500">
					Menampilkan
					<span class="font-medium text-gray-900">{{ pagination.from }}</span>
					sampai
					<span class="font-medium text-gray-900">{{ pagination.to }}</span>
					dari
					<span class="font-medium text-gray-900">{{ pagination.total }}</span>
					data
				</div>
				<div class="flex gap-2">
					<button
						@click="fetchLogs(pagination.current_page - 1)"
						:disabled="pagination.current_page === 1"
						class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						Sebelumnya
					</button>
					<button
						@click="fetchLogs(pagination.current_page + 1)"
						:disabled="pagination.current_page === pagination.last_page"
						class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						Selanjutnya
					</button>
				</div>
			</div>
		</BaseCard>
	</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import StatusBadge from '../../components/StatusBadge.vue';
import { useToast } from '../../composables/useToast';
import moment from 'moment';
import 'moment/dist/locale/id';

moment.locale('id');

const toast = useToast();
const loading = ref(false);
const isProcessing = ref(false);
const logs = ref([]);
const search = ref('');
const pagination = ref({
	current_page: 1,
	last_page: 1,
	total: 0,
	from: 0,
	to: 0,
});

const formatDate = (date) => {
	if (!date) return '-';
	return moment(date).format('DD MMM YYYY, HH:mm');
};

const fetchLogs = async (page = 1) => {
	loading.value = true;
	try {
		const params = { page };
		if (search.value) params.cari = search.value;

		const response = await api.get('/admin/log-approval', { params });

		logs.value = response.data.data;
		pagination.value = {
			current_page: response.data.current_page,
			last_page: response.data.last_page,
			total: response.data.total,
			from: response.data.from,
			to: response.data.to,
		};
	} catch (error) {
		console.error('Error fetching logs:', error);
	} finally {
		loading.value = false;
	}
};

const approvePermohonan = async (item) => {
	if (!confirm(`Apakah Anda yakin ingin menyetujui kepindahan siswa dengan NISN ${item.nisn}?`)) {
		return;
	}

	try {
		isProcessing.value = true;
		const response = await api.post(`/admin/log-approval/${item.id}/approve`);

		if (response.data.status === 'success') {
			fetchLogs(pagination.value.current_page);
			toast.success('Berhasil menyetujui permohonan pindah sekolah.');
		}
	} catch (error) {
		console.error('Error approving data:', error);
		toast.error(error.response?.data?.message || 'Terjadi kesalahan saat memproses data.');
	} finally {
		isProcessing.value = false;
	}
};

// Debounce search
let searchTimeout;
watch(search, () => {
	clearTimeout(searchTimeout);
	searchTimeout = setTimeout(() => {
		fetchLogs(1);
	}, 500);
});

onMounted(() => {
	fetchLogs();
});
</script>
