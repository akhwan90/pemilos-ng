<template>
  <BaseModal v-model="isOpen" title="Data DPT" max-width="4xl">
    <div v-if="npsn" class="space-y-4">
			<!-- Tabel List Kandidat (View Mode) -->
			<div class="overflow-hidden border border-gray-200 rounded-lg">
				<table class="w-full text-sm text-left">
					<thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
						<tr>
							<th class="px-4 py-3 w-16 text-center">No</th>
							<th class="px-4 py-3 w-24">NISN</th>
							<th class="px-4 py-3 w-24">Nama</th>
							<th class="px-4 py-3 w-32 text-center">TPS</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="dptdatas.length === 0" class="bg-white">
							<td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada aktivitas user Sekolah ini.</td>
						</tr>
						<tr v-else v-for="(k, index) in dptdatas" :key="k.id" class="bg-white border-b hover:bg-gray-50">
							<td class="px-4 py-3 text-center text-gray-700">{{ (index + 1) }}</td>
							<td class="px-4 py-3 text-center">{{ k.nisn }}</td>
							<td class="px-4 py-3">{{ k.nm_siswa }}</td>
							<td class="px-4 py-3 text-center">{{ k.nama_tps }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
  </BaseModal>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';
import { useToast } from '../../../composables/useToast';
import api from '../../../services/api';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  npsn: { type: [String, Number], default: null }
});
const toast = useToast();

const emit = defineEmits(['update:modelValue']);

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

watch(() => isOpen.value, () => {
    fetchData();
});
const dptdatas = ref([]);


async function fetchData() {
  console.log('npsn', props.npsn);
	try {
		const res = await api.get(`/admin/data-sekolah/${props.npsn}/dpt`);
		dptdatas.value = res.data.data;
	} catch (error) {
    console.log(error);
		toast.error('Gagal mengambil data aktivitas');
	} finally {
	}
}
</script>
