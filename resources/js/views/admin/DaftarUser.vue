<template>
	<div class="space-y-6">
		<div class="flex items-center justify-between mb-4">
			<div>
				<h2 class="text-xl font-bold text-gray-800">Daftar User</h2>
				<p class="text-sm text-gray-500">Lihat data seluruh user aplikasi</p>
			</div>
		</div>

		<BaseCard class="p-0">
			<!-- Toolbar -->
			<div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-wrap gap-4">
				<div class="flex gap-2 items-center w-full md:w-auto">
					<!-- Filter Sekolah -->
					<div class="relative w-full md:w-64">
						<select v-model="filterLevel" @change="fetchUsers(1)" class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
							<option value="">Semua Level</option>
							<option v-for="level in daftarLevel" :key="level.id" :value="level.id">
								{{ level.label || level.id }}
							</option>
						</select>
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
							<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
							</svg>
						</div>
					</div>
				</div>

				<div class="relative w-full md:w-64">
					<input v-model="search" @keyup.enter="fetchUsers(1)" type="text" placeholder="Cari username..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" />
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
							<th class="px-6 py-4 font-medium">Aksi</th>
							<th class="px-6 py-4 font-medium">No</th>
							<th class="px-6 py-4 font-medium">Username</th>
							<th class="px-6 py-4 font-medium">Level</th>
							<th class="px-6 py-4 font-medium">Sekolah</th>
							<th class="px-6 py-4 font-medium">TPS</th>
							<th class="px-6 py-4 font-medium">Kewenangan</th>
							<th class="px-6 py-4 font-medium">Sudah Password Versi Baru</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 text-sm">
						<tr v-if="loading">
							<td colspan="8" class="px-6 py-12 text-center text-gray-500">
								<div class="flex justify-center items-center gap-2">
									<svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
										<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
										<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
									</svg>
									Memuat data...
								</div>
							</td>
						</tr>
						<tr v-else-if="users.length === 0">
							<td colspan="8" class="px-6 py-12 text-center text-gray-500">
								<div class="flex flex-col items-center justify-center">
									<svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
									</svg>
									<p>Tidak ada data user ditemukan.</p>
								</div>
							</td>
						</tr>
						<tr v-else v-for="(item, index) in users" :key="item.id" class="hover:bg-gray-50 transition-colors">
							<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
							    <div class="flex gap-2">
									<BaseButton @click="openModal('user', item)" variant="primary">Edit</BaseButton>
									<BaseButton @click="deleteUser(item.id)" variant="danger">Hapus</BaseButton>
							    </div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<div class="font-medium text-gray-900">
									{{ pagination.from + index }}
								</div>
							</td>
							<td class="px-6 py-4">
								<div class="font-medium text-gray-900">
									{{ item.username }}
								</div>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-gray-500">
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" v-if="item.level == 1"> Super Admin </span>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" v-else-if="item.level == 2"> Admin Sekolah </span>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" v-if="item.level == 3"> Admin TPS </span>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" v-if="item.level == 4"> Admin Pemantau </span>
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								{{ item.nama_sekolah }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								{{ item.nm_kelas }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								{{ item.level_4_kewenangan || '-' }}
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								{{ item.status_password }}
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
						@click="fetchUsers(pagination.current_page - 1)"
						:disabled="pagination.current_page === 1"
						class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						Sebelumnya
					</button>
					<button
						@click="fetchUsers(pagination.current_page + 1)"
						:disabled="pagination.current_page === pagination.last_page"
						class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						Selanjutnya
					</button>
				</div>
			</div>
		</BaseCard>

		<ModalEditUser v-model="modals.user" :user="selectedUser" @saved="onUserSaved" @error="onUserError" />
	</div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import ModalEditUser from './modals/ModalEditUser.vue';
import { useToast } from '../../composables/useToast';

const toast = useToast();
const loading = ref(false);
const users = ref([]);
const daftarLevel = ref([
	{ id: 1, label: 'Admin Utama' },
	{ id: 2, label: 'Admin Sekolah' },
	{ id: 3, label: 'Admin TPS' },
	{ id: 4, label: 'Admin Pemantau' },
]);
const filterLevel = ref('');
const search = ref('');
const pagination = ref({
	current_page: 1,
	last_page: 1,
	total: 0,
	from: 0,
	to: 0,
});
const modals = ref({
	user: false,
});
const selectedUser = ref({});

function openModal(modalName, user) {
	selectedUser.value = user;
	modals.value[modalName] = true;
}

// Use the Toast Component logically assuming it's globally registered or imported
const onUserSaved = () => {
	toast.success('Data user berhasil diperbarui');
	fetchUsers(pagination.value.current_page);
};

const onUserError = (errorMsg) => {
	toast.error(errorMsg);
};

const fetchUsers = async (page = 1) => {
	loading.value = true;
	try {
		const params = {
			page,
		};

		if (search.value) params.cari = search.value;
		if (filterLevel.value) params.level = filterLevel.value;

		const response = await api.get('/admin/data-user', { params });

		users.value = response.data.data;
		pagination.value = {
			current_page: response.data.current_page,
			last_page: response.data.last_page,
			total: response.data.total,
			from: response.data.from,
			to: response.data.to,
		};
	} catch (error) {
		console.error('Error fetching data user:', error);
		toast.error('Gagal memuat data user');
	} finally {
		loading.value = false;
	}
};

// Debounce search
let searchTimeout;
watch(search, () => {
	clearTimeout(searchTimeout);
	searchTimeout = setTimeout(() => {
		fetchUsers(1);
	}, 500);
});

const deleteUser = async (id) => {
	if (confirm(`Apakah Anda yakin ingin menghapus data user ini secara permanen? Data yang telah dihapus tidak dapat dikembalikan.`)) {
		try {
			await api.delete(`/admin/data-user/${id}`);
			toast.success('Data user berhasil dihapus secara permanen');
			fetchUsers(pagination.value.current_page);
		} catch (error) {
			console.error('Error deleting user:', error);
			toast.error(error.response?.data?.message || 'Gagal menghapus data user');
		}
	}
};

onMounted(async () => {
	fetchUsers();
});
</script>
