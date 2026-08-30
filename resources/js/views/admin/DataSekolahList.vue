<template>
	<div class="space-y-6">
		<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
			<div>
				<h2 class="text-xl font-bold text-gray-800">Data Sekolah & Progres Pemilihan</h2>
				<p class="text-sm text-gray-500">Monitoring progres pemilihan di setiap sekolah yang terdaftar.</p>
			</div>
			<div v-if="auth.user?.level === 1" class="flex gap-2">
				<BaseButton @click="openModal('edit')" variant="primary">
					<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
					</svg>
					Tambah Sekolah
				</BaseButton>
			</div>
		</div>

		<!-- Filter Card -->
		<BaseCard>
			<form @submit.prevent="fetchData(1)" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
				<div>
					<BaseSelect v-model="filterTahun" :options="tahunOptions" :valueKey="'value'" :labelKey="'label'" placeholder="- Tahun -"/>
				</div>
				<div>
					<input v-model="filterSearch" type="search" placeholder="Cari nama, NPSN, dll..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"/>
				</div>
				<div>
					<BaseSelect v-model="filterTingkat" :options="tingkatOptions" :valueKey="'value'" :labelKey="'label'" placeholder="- Tingkat -"/>
				</div>
				<div>
					<BaseSelect v-model="filterOrderBy" :options="orderByOptions" :valueKey="'value'" :labelKey="'label'" placeholder="- Order By -"/>
				</div>
				<div>
					<BaseButton type="submit" variant="primary" class="w-full justify-center">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						Cari / Urutkan
					</BaseButton>
				</div>
			</form>
		</BaseCard>

		<!-- Table Card -->
		<BaseCard class="overflow-visible p-0">
			<div class="overflow-visible min-h-[300px]">
				<table class="w-full text-sm text-left">
					<thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
						<tr>
							<th class="px-4 py-3 w-16 text-center">No</th>
							<th class="px-4 py-3 w-32">Aksi</th>
							<th class="px-4 py-3">Nama Sekolah</th>
							<th class="px-4 py-3">NPSN</th>
							<th class="px-4 py-3 text-center">Kandidat</th>
							<th class="px-4 py-3 text-center">TPS</th>
							<th class="px-4 py-3 text-center">Siswa</th>
							<th class="px-4 py-3 text-center">DPT</th>
							<th class="px-4 py-3 w-64">Progress Pemilihan</th>
						</tr>
					</thead>
					<tbody>
						<!-- <tr v-if="isLoading" class="bg-white">
							<td colspan="9" class="px-4 py-8 text-center text-gray-500">
								<svg class="animate-spin h-8 w-8 mx-auto mb-2 text-indigo-500" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
									<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
								</svg>
								Memuat data...
							</td>
						</tr> -->
						<tr v-if="items.length === 0" class="bg-white">
							<td colspan="9" class="px-4 py-8 text-center text-gray-500">Tidak ada data ditemukan.</td>
						</tr>
						<tr v-else v-for="(item, index) in items" :key="item.npsn" class="bg-white border-b hover:bg-gray-50">
							<td class="px-4 py-3 text-center">
								{{ index + 1 }}
							</td>
							<td class="px-4 py-3">
								<div class="flex gap-1 relative">
									<BaseButton v-if="auth.user?.level === 1" @click="openModal('edit', item.npsn)" variant="warning" class="px-2 py-1 !min-h-0 text-xs" title="Edit"> Edit </BaseButton>

									<!-- Dropdown Component -->
									<BaseDropdown widthClass="w-56" alignClass="left-0">
										<template #trigger>
											<BaseButton variant="primary" class="px-2 py-1 !min-h-0 cursor-pointer text-xs flex items-center gap-1">
												Opsi
												<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
												</svg>
											</BaseButton>
										</template>

										<a href="#" @click.prevent="openModal('user', item.npsn)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">User Sekolah</a>
										<a href="#" @click.prevent="openModal('jadwal', item.npsn)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Jadwal Pemilihan</a>
										<div class="border-t border-gray-100 my-1"></div>
										<a href="#" @click.prevent="openModal('kandidat', item.npsn)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Kandidat</a>
										<a href="#" @click.prevent="openModal('tps', item.npsn)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">TPS</a>
										<a href="#" @click.prevent="openModal('siswa', item.npsn)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">DPT</a>
										<a href="#" @click.prevent="openModal('upload', item.npsn)" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Upload</a>
										<div class="border-t border-gray-100 my-1"></div>
										<router-link :to="'/admin/monitoring/hasil-vote/' + item.npsn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 font-medium text-emerald-600">Monitoring Hasil Vote</router-link>
										<a href="#" @click.prevent="hapus(item.npsn)" class="block px-4 py-2 text-sm text-red-700 hover:bg-gray-100">Hapus</a>
									</BaseDropdown>
								</div>
							</td>
							<td class="px-4 py-3">
								<p class="font-medium text-gray-900">{{ item.nama_sekolah }}</p>
								<p class="text-sm text-gray-600">Tingkat : {{ item.jenjang }}</p>
							</td>
							<td class="px-4 py-3">{{ item.npsn }}</td>
							<td class="px-4 py-3 text-center">
								{{ item.jml_kandidat }}
							</td>
							<td class="px-4 py-3 text-center text-xs">
								<span class="font-bold text-gray-800">{{ item.jml_tps_generate_token }}</span>
								/
								<span class="text-gray-500">{{ item.jml_tps }}</span>
							</td>
							<td class="px-4 py-3 text-center">
								{{ item.jml_siswa }}
							</td>
							<td class="px-4 py-3 text-center">
								<span
									:class="{
										'bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded font-bold': item.is_over_capacity,
									}"
								>
									{{ item.jml_dpt }} ({{ item.persentase_dpt }}%)
								</span>
							</td>
							<td class="px-4 py-3">
								<div class="flex justify-between text-xs mb-1">
									<span class="font-medium text-gray-700">{{ item.jml_memilih }} Suara Masuk</span>
									<span class="font-bold text-emerald-600">{{ item.persentase_memilih }}%</span>
								</div>
								<div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden flex">
									<!-- Progress bar modern (menggantikan linear-gradient table di CI3) -->
									<div class="bg-emerald-500 h-2.5" :style="'width: ' + item.persentase_memilih + '%'"></div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</BaseCard>

		<!-- Modals -->
		<ModalEditSekolah v-model="modals.edit" :npsn="selectedNpsn" :is-edit-mode="isEditMode" @updated="fetchData()" />
		<ModalUserSekolah v-model="modals.user" :npsn="selectedNpsn" />
		<ModalJadwalSekolah v-model="modals.jadwal" :npsn="selectedNpsn" />
		<ModalKandidat v-model="modals.kandidat" :npsn="selectedNpsn" />
		<ModalTps v-model="modals.tps" :npsn="selectedNpsn" />
		<ModalSiswaDpt v-model="modals.siswa" :npsn="selectedNpsn" />
		<ModalUpload v-model="modals.upload" :npsn="selectedNpsn"/>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseSelect from '../../components/BaseSelect.vue';
import BaseDropdown from '../../components/BaseDropdown.vue';
import api from '../../services/api';

// Import Modals
import ModalUserSekolah from './sekolah_modals/ModalUserSekolah.vue';
import ModalJadwalSekolah from './sekolah_modals/ModalJadwalSekolah.vue';
import ModalKandidat from './sekolah_modals/ModalKandidat.vue';
import ModalTps from './sekolah_modals/ModalTps.vue';
import ModalSiswaDpt from './sekolah_modals/ModalSiswaDpt.vue';
import ModalEditSekolah from './sekolah_modals/ModalEditSekolah.vue';
import ModalUpload from './sekolah_modals/ModalUpload.vue';

const auth = useAuthStore();
const items = ref([]);
const isLoading = ref(false);

// Modals State
const selectedNpsn = ref(null);
const isEditMode = ref(true);
const modals = ref({
	edit: false,
	user: false,
	jadwal: false,
	kandidat: false,
	tps: false,
	siswa: false,
});

function openModal(modalName, npsn = null) {
	selectedNpsn.value = npsn;
	if (modalName === 'edit') {
		isEditMode.value = npsn !== null;
	}
	modals.value[modalName] = true;
}

const currentYear = new Date().getFullYear();
const filterTahun = ref(currentYear.toString());
const filterSearch = ref('');
const filterOrderBy = ref('');
const filterTingkat = ref('');

const tahunOptions = [
	{ value: '2026', label: 'Pilih tahun: 2026' },
	{ value: '2025', label: 'Pilih tahun: 2025' },
	{ value: '2024', label: 'Pilih tahun: 2024' },
	{ value: '2023', label: 'Pilih tahun: 2023' },
	{ value: '2022', label: 'Pilih tahun: 2022' },
];

const tingkatOptions = [
	{ value: 'kemenag', label: 'Kemenag' },
	{ value: 'smp', label: 'SMP' },
	{ value: 'sma', label: 'SMA/SMK' },
];

const orderByOptions = [
	{
		value: 'jml_siswa_asc',
		label: 'Sort by Jumlah Siswa : Terkecil > Terbesar',
	},
	{
		value: 'jml_siswa_desc',
		label: 'Sort by Jumlah Siswa : Terbesar > Terkecil',
	},
	{ value: 'jml_dpt_asc', label: 'Sort by Jumlah DPT : Terkecil > Terbesar' },
	{
		value: 'jml_dpt_desc',
		label: 'Sort by Jumlah DPT : Terbesar > Terkecil',
	},
	{ value: 'jml_tps_asc', label: 'Sort by Jumlah TPS : Terkecil > Terbesar' },
	{
		value: 'jml_tps_desc',
		label: 'Sort by Jumlah TPS : Terbesar > Terkecil',
	},
	{
		value: 'jml_kandidat_asc',
		label: 'Sort by Jumlah Kandidat : Terkecil > Terbesar',
	},
	{
		value: 'jml_kandidat_desc',
		label: 'Sort by Jumlah Kandidat : Terbesar > Terkecil',
	},
	{ value: 'jenjang_asc', label: 'Sort by Jenjang : Terkecil > Terbesar' },
	{ value: 'jenjang_desc', label: 'Sort by Jenjang : Terbesar > Terkecil' },
	{
		value: 'nama_sekolah_asc',
		label: 'Sort by Nama Sekolah : Terkecil > Terbesar',
	},
	{
		value: 'nama_sekolah_desc',
		label: 'Sort by Nama Sekolah : Terbesar > Terkecil',
	},
	{ value: 'npsn_asc', label: 'Sort by NPSN Sekolah : Terkecil > Terbesar' },
	{ value: 'npsn_desc', label: 'Sort by NPSN Sekolah : Terbesar > Terkecil' },
	{
		value: 'persentase_dpt_desc',
		label: 'Sort by Persentase DPT : Terbesar > Terkecil',
	},
	{
		value: 'persentase_memilih_desc',
		label: 'Sort by Persentase Memilih : Terbesar > Terkecil',
	},
];


async function fetchData(page = 1) {
	isLoading.value = true;
	try {
		const response = await api.get('/admin/data-sekolah', {
			params: {
				tahun: filterTahun.value,
				cari: filterSearch.value,
				order_by: filterOrderBy.value,
                filter_by: filterTingkat.value,
				no_pagination: 1,
			},
		});

		items.value = response.data;
	} catch (error) {
		console.error('Gagal mengambil data sekolah:', error);
	} finally {
		isLoading.value = false;
	}
}

async function hapus(npsn) {
	if (!confirm('Apakah Anda yakin ingin menghapus data sekolah ini?')) return;
	try {
		await api.delete(`/admin/data-sekolah/${npsn}`);
		fetchData();
	} catch (error) {
		console.error('Gagal menghapus data sekolah:', error);
    }

}

onMounted(() => {
	fetchData();
});
</script>
