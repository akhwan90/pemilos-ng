<template>
	<div class="space-y-6">
		<div class="flex items-center justify-between mb-4">
			<div>
				<h2 class="text-xl font-bold text-gray-800">Log Aktivitas</h2>
				<p class="text-sm text-gray-500">Lihat data seluruh aktivitas user di aplikasi.</p>
			</div>
		</div>

		<BaseCard class="p-0">
			<!-- Toolbar -->
			<div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-wrap gap-4">


				<div class="relative w-full md:w-64">
					<input
						v-model="search"
						@keyup.enter="fetchAktivitas(1)"
						type="search"
						placeholder="Cari Username..."
						class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
					/>
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
							<th class="px-6 py-4 font-medium" width="10%">No</th>
							<th class="px-6 py-4 font-medium" width="10%">Username</th>
							<th class="px-6 py-4 font-medium" width="15%">Waktu</th>
							<th class="px-6 py-4 font-medium" width="45%">Aktivitas & Keterangan</th>
							<th class="px-6 py-4 font-medium" width="10%">IP</th>
							<th class="px-6 py-4 font-medium" width="10%">OS</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 text-sm">
						<tr v-if="loading">
							<td colspan="5" class="px-6 py-12 text-center text-gray-500">
								<div class="flex justify-center items-center gap-2">
									<svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
										<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
										<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
									</svg>
									Memuat data...
								</div>
							</td>
						</tr>
						<tr v-else-if="dataAktivitas?.data?.length === 0">
							<td colspan="5" class="px-6 py-12 text-center text-gray-500">
								<div class="flex flex-col items-center justify-center">
									<svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
									</svg>
									<p>Tidak ada data siswa ditemukan.</p>
								</div>
							</td>
						</tr>
						<tr v-else v-for="(item, index) in dataAktivitas.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
						    <td class="px-6 py-4">
								{{ dataAktivitas.from + index}}
							</td>
							<td class="px-6 py-4 whitespace-nowrap">
								<div class="font-medium text-gray-900">{{ item.username }}</div>
							</td>
							<td class="px-6 py-4 text-sm">{{ item.waktu }}</td>
							<td class="px-6 py-4">
							    <div class="w-full">
							        <div class="text-sm font-semibold text-indigo-600 mb-1">
                                        {{ item.nama_aktifitas }}
                                    </div>
                                    <div class="text-sm text-gray-600 w-full" style="word-break: break-word;">
                                        <template v-if="item.keterangan && item.keterangan.length > 100">
                                            <span v-if="!expandedItems.includes(item.id)">
                                                {{ item.keterangan.substring(0, 100) }}...
                                                <button @click.prevent="toggleExpand(item.id)" class="text-indigo-500 hover:text-indigo-700 ml-1 text-xs font-medium cursor-pointer focus:outline-none">Lebih banyak</button>
                                            </span>
                                            <span v-else>
                                                {{ item.keterangan }}
                                                <button @click.prevent="toggleExpand(item.id)" class="text-indigo-500 hover:text-indigo-700 ml-1 text-xs font-medium cursor-pointer focus:outline-none">Lebih sedikit</button>
                                            </span>
                                        </template>
                                        <template v-else>
                                            {{ item.keterangan }}
                                        </template>
                                    </div>
								</div>
							</td>
							<td class="px-6 py-4">{{ item.ip }}</td>
							<td class="px-6 py-4 text-xs">
								<div>{{ item.os }}</div>
								<div class="text-gray-400 mt-1">{{ item.browser }}</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			<div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between" v-if="dataAktivitas.total > 0">
				<div class="text-sm text-gray-500">
					Menampilkan <span class="font-medium text-gray-900">{{ dataAktivitas.from }}</span> sampai <span class="font-medium text-gray-900">{{ dataAktivitas.to }}</span> dari
					<span class="font-medium text-gray-900">{{ dataAktivitas.total }}</span> data
				</div>
				<div class="flex gap-2">
					<button
						@click="fetchAktivitas(dataAktivitas.current_page - 1)"
						:disabled="dataAktivitas.current_page === 1"
						class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						Sebelumnya
					</button>
					<button
						@click="fetchAktivitas(dataAktivitas.current_page + 1)"
						:disabled="dataAktivitas.current_page === dataAktivitas.last_page"
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
import ToastNotification from '../../components/ToastNotification.vue';

const loading = ref(false);
const expandedItems = ref([]);
const dataAktivitas = ref({
    data: [],
    total: 0,
    current_page: 1,
    last_page: 1,
});
const filterNpsn = ref('');
const search = ref('');

const toggleExpand = (id) => {
    const index = expandedItems.value.indexOf(id);
    if (index === -1) {
        expandedItems.value.push(id);
    } else {
        expandedItems.value.splice(index, 1);
    }
};

// Use the Toast Component logically assuming it's globally registered or imported
const toast = ref({ show: false, message: '', type: 'success' });
const showToast = (message, type = 'success') => {
	// Try to use a global bus or store if available, otherwise fallback to alert for simplicity here
	// Assuming useToast is not easily available, we fallback to custom event or just console.error
	console.log(message);
};

const fetchAktivitas = async (page = 1) => {
	try {
    	const params = {
            page,
            search: search.value,
        };

		const response = await api.get('/admin/aktivitas', { params });

		if (response.data) {
            dataAktivitas.value = response.data;
		}
	} catch (error) {
		console.error('Error fetching data sekolah:', error);
	}
};
// Debounce search
let searchTimeout;

watch(search, () => {
	clearTimeout(searchTimeout);
	searchTimeout = setTimeout(() => {
		fetchAktivitas(1);
	}, 500);
});

onMounted(async () => {
	fetchAktivitas();
});
</script>
