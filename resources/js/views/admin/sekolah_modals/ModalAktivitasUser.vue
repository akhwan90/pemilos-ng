<template>
	<BaseModal v-model="isOpen" title="Aktivitas User" max-width="5xl">
		<div v-if="npsn" class="space-y-4">
			<!-- Tabel List Kandidat (View Mode) -->
			<div v-if="!isEditing" class="overflow-hidden border border-gray-200 rounded-lg">
				<table class="w-full text-sm text-left">
					<thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
						<tr>
							<th class="px-4 py-3 w-16 text-center">No</th>
							<th class="px-4 py-3 w-24">Waktu</th>
							<th class="px-4 py-3 w-24">Nama</th>
							<th class="px-4 py-3 w-32 text-center">Detil</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="isLoading" class="bg-white">
							<td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat data ...</td>
						</tr>
						<tr v-else-if="aktivitass.length === 0" class="bg-white">
							<td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada aktivitas user Sekolah ini.</td>
						</tr>
						<tr v-else v-for="(k, index) in aktivitass" :key="k.id" class="bg-white border-b hover:bg-gray-50">
							<td class="px-4 py-3 text-center text-gray-700">{{ (index + 1) }}</td>
							<td class="px-4 py-3">{{ k.waktu }}</td>
							<td class="px-4 py-3">{{ k.nama_aktifitas }}</td>
							<td class="px-4 py-3">
								<div class="text-sm text-gray-600 w-full" style="word-break: break-word;">
									<template v-if="k.keterangan && k.keterangan.length > 100">
										<span v-if="!expandedItems.includes(k.id)">
											{{ k.keterangan.substring(0, 100) }}...
											<button @click.prevent="toggleExpand(k.id)" class="text-indigo-500 hover:text-indigo-700 ml-1 text-xs font-medium cursor-pointer focus:outline-none">Lebih banyak</button>
										</span>
										<span v-else>
											{{ k.keterangan }}
											<button @click.prevent="toggleExpand(k.id)" class="text-indigo-500 hover:text-indigo-700 ml-1 text-xs font-medium cursor-pointer focus:outline-none">Lebih sedikit</button>
										</span>
									</template>
									<template v-else>
										{{ k.keterangan }}
									</template>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div v-if="showDetil">
			<h3>Detil</h3>
			<ol>
				<li v-for="(item, index) in detilDatas" :key="index">{{ index+1 }}. {{ item.keterangan }}</li>
			</ol>
		</div>
	</BaseModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';
import BaseButton from '../../../components/BaseButton.vue';
import api from '../../../services/api';
import { useToast } from '../../../composables/useToast';

const props = defineProps({
	modelValue: { type: Boolean, default: false },
	npsn: { type: [String, Number], default: null },
});

const emit = defineEmits(['update:modelValue']);
const toast = useToast();
const expandedItems = ref([]);

const isOpen = computed({
	get: () => props.modelValue,
	set: (val) => emit('update:modelValue', val),
});

const kandidatList = ref([]);
const isLoading = ref(false);

const isEditing = ref(false);
const isSubmitting = ref(false);
const aktivitass = ref([]);


const showDetil = ref(false);
const detilDatas = ref([]);

watch(() => isOpen.value, () => {
    fetchData();
});


const toggleExpand = (id) => {
    const index = expandedItems.value.indexOf(id);
    if (index === -1) {
        expandedItems.value.push(id);
    } else {
        expandedItems.value.splice(index, 1);
    }
};

async function fetchData() {
	isLoading.value = true;
	try {
		const res = await api.get(`/admin/data-sekolah/${props.npsn}/aktivitas-user`);
		aktivitass.value = res.data.data;
	} catch (error) {
		toast.error('Gagal mengambil data aktivitas');
	} finally {
		isLoading.value = false;
	}
}
</script>
