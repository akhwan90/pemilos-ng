<template>
	<div class="space-y-6">
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-bold text-gray-800">Approval Pindah Sekolah</h2>
				<p class="text-sm text-gray-500 mt-1">Daftar siswa yang mengajukan permohonan pindah sekolah (Tahun: {{ currentYear }}).</p>
			</div>
		</div>

		<!-- Tabs Section -->
		<div class="border-b border-gray-200">
			<nav class="-mb-px flex space-x-6" aria-label="Tabs">
				<a href="#" @click.prevent="activeTab = 'from_other_schools'"
					:class="[activeTab === 'from_other_schools' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
					Dari Sekolah Lain
				</a>
				<a href="#" @click.prevent="activeTab = 'from_my_school'"
					:class="[activeTab === 'from_my_school' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
					Dari Sekolah Saya
				</a>
			</nav>
		</div>

		<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
			<!-- Table Actions/Filter -->
			<div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
				<div class="relative max-w-sm w-full">
					<input type="text" v-model="searchQuery" placeholder="Cari nama siswa atau NISN..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm" />
					<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
						<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
					</div>
				</div>
			</div>

			<!-- Loading State -->
			<div v-if="loading" class="p-8 text-center">
				<div class="inline-flex items-center justify-center space-x-2">
					<svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
					<span class="text-gray-500">Memuat data...</span>
				</div>
			</div>

			<!-- Table -->
			<div v-else class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
						<tr class="bg-gray-50 border-b border-gray-200">
							<th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
							<th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">NISN</th>
							<th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
							<th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
							<th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Pengajuan</th>
							<th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Status</th>
							<th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200">
						<tr v-if="tabFilteredData.length === 0">
							<td colspan="7" class="py-8 text-center text-gray-500">Tidak ada data permohonan pindah.</td>
						</tr>
						<tr v-for="(item, index) in tabFilteredData" :key="item.id" class="hover:bg-gray-50 transition-colors">
							<td class="py-3 px-4 text-sm text-gray-900">{{ index + 1 }}</td>
							<td class="py-3 px-4 text-sm font-medium text-gray-900">{{ item.nisn }}</td>
							<td class="py-3 px-4 text-sm text-gray-800">{{ item.nama_baru || item.nama_siswa_asal || '-' }}</td>
							<td class="py-3 px-4 text-sm text-gray-600">
								<span v-if="item.npsn === auth.user.npsn">Dimintakan pindah ke {{ item.nama_sekolah_asal }}</span>
								<span v-else>Dimintakan pindah dari {{ item.nama_sekolah_tujuan }}</span>
							</td>
							<td class="py-3 px-4 text-sm text-gray-600">{{ formatDate(item.created_at) }}</td>
							<td class="py-3 px-4 text-sm text-center">
								<span :class="getStatusBadge(item.status)">
									{{ getStatusText(item.status) }}
								</span>
							</td>
							<td class="py-3 px-4 text-sm text-center">
								<BaseButton 
									v-if="Number(item.status) === 0 && item.npsn == auth.user.npsn"
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
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';
import BaseButton from '../../components/BaseButton.vue';

const auth = useAuthStore();
const toast = useToast();
const dataApproval = ref([]);
const loading = ref(true);
const isProcessing = ref(false);
const searchQuery = ref('');
const currentYear = ref(new Date().getFullYear());

// Tab state
const activeTab = ref('from_other_schools'); // Default tab

const fetchData = async () => {
	try {
		loading.value = true;
		const response = await axios.get('/api/admin-sekolah/approval-pindah', {
			headers: {
				Authorization: `Bearer ${localStorage.getItem('admin_token')}`,
			},
		});

		if (response.data.status === 'success') {
			dataApproval.value = response.data.data;
		}
	} catch (error) {
		console.error('Error fetching data:', error);
	} finally {
		loading.value = false;
	}
};

const tabFilteredData = computed(() => {

	console.log(activeTab.value);

	if (!searchQuery.value) {
		// Jika tidak ada search query, kembalikan semua data sesuai tab
		return dataApproval.value.filter(item => {
			// Logika Anda untuk memfilter berdasarkan tab
			if (activeTab.value === 'from_other_schools') {
				return item.npsn == auth.user.npsn;
			} else if (activeTab.value === 'from_my_school') {
				return item.user_pemohon_npsn == auth.user.npsn;
			}
			return true; // Default to show all if no active tab matches (should not happen)
		});
	} else {
		// Jika ada search query, filter dulu berdasarkan search, lalu filter hasilnya berdasarkan tab
		const query = searchQuery.value.toLowerCase();
		const searchedItems = dataApproval.value.filter(item => {
			return item.nisn?.toString().toLowerCase().includes(query) ||
				item.nama_baru?.toLowerCase().includes(query) ||
				item.nama_siswa_asal?.toLowerCase().includes(query);
		});

		return searchedItems.filter(item => {
			if (activeTab.value === 'from_other_schools') {
				return item.user_pemohon_npsn !== auth.user.npsn;
			} else if (activeTab.value === 'from_my_school') {
				return item.npsn === auth.user.npsn;
			}
			return true;
		});
	}
});

watch(activeTab, () => {
	// Anda tidak perlu memanggil fetchData di sini.
	// Computed property akan otomatis mengupdate berdasarkan activeTab yang baru.
	// Jika Anda perlu memuat data baru berdasarkan tab (misal, API berbeda),
	// maka di sini Anda bisa memanggil fetchData() lagi, tapi itu berbeda dari memanggilnya di dalam computed.
});

const formatDate = (dateString) => {
	if (!dateString) return '-';
	const date = new Date(dateString);
	return new Intl.DateTimeFormat('id-ID', {
		day: '2-digit',
		month: 'short',
		year: 'numeric',
		hour: '2-digit',
		minute: '2-digit',
	}).format(date);
};

const getStatusBadge = (status) => {
	// convert to number since it might be a string from database
	const statusNum = Number(status);
	switch (statusNum) {
		case 1:
			return 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
		case 2:
			return 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
		default:
			return 'inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800';
	}
};

const getStatusText = (status) => {
	const statusNum = Number(status);
	switch (statusNum) {
		case 1:
			return 'Disetujui';
		case 2:
			return 'Ditolak';
		default:
			return 'Menunggu';
	}
};

const approvePermohonan = async (item) => {
	if (!confirm(`Apakah Anda yakin ingin menyetujui kepindahan siswa dengan NISN ${item.nisn}?`)) {
		return;
	}

	try {
		isProcessing.value = true;
		const response = await axios.post(
			`/api/admin-sekolah/approval-pindah/${item.id}/approve`,
			{},
			{
				headers: {
					Authorization: `Bearer ${localStorage.getItem('admin_token')}`,
				},
			}
		);

		if (response.data.status === 'success') {
		  // Reload data
		  fetchData();
		  toast.success('Berhasil menyetujui permohonan pindah sekolah.');
		}
	} catch (error) {
		console.error('Error approving data:', error);
		toast.error(error.response?.data?.message || 'Terjadi kesalahan saat memproses data.');
	} finally {
		isProcessing.value = false;
	}
};

onMounted(() => {
	fetchData();
});
</script>
