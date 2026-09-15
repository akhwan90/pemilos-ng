<template>
	<BaseModal :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)"  :title="'History Perubahan Data Siswa'" max-width="4xl">
        <div class="overflow-x-auto">
				<table class="w-full text-left border-collapse">
					<thead>
						<tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
							<th class="px-6 py-4 font-medium">ID</th>
							<th class="px-6 py-4 font-medium">Created At</th>
							<th class="px-6 py-4 font-medium">Keterangan</th>
							<th class="px-6 py-4 font-medium">NISN</th>
							<th class="px-6 py-4 font-medium">NPSN</th>
							<th class="px-6 py-4 font-medium">Nama</th>
							<th class="px-6 py-4 font-medium">Kelas</th>
							<th class="px-6 py-4 font-medium">JK</th>
							<th class="px-6 py-4 font-medium">Difabel</th>
							<th class="px-6 py-4 font-medium">Status</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 text-xs">
						<tr v-if="history.length === 0">
							<td colspan="10" class="px-6 py-12 text-center text-gray-500">
								<div class="flex flex-col items-center justify-center">
									<svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
									</svg>
									<p>Tidak ada data siswa ditemukan.</p>
								</div>
							</td>
						</tr>
						<tr v-for="item in history" :key="item.id" class="hover:bg-gray-50 transition-colors">
							<td class="px-6 py-4">{{ item.id }}</td>
							<td class="px-6 py-4">{{ item.created_at }}</td>
							<td class="px-6 py-4">{{ item.keterangan }}</td>
							<td class="px-6 py-4">{{ item.nisn }}</td>
							<td class="px-6 py-4">{{ item.npsn }}</td>
							<td class="px-6 py-4">{{ item.nama }}</td>
							<td class="px-6 py-4">{{ item.kelas }}</td>
							<td class="px-6 py-4">{{ item.jk }}</td>
							<td class="px-6 py-4">{{ item.difabel }}</td>
							<td class="px-6 py-4">{{ item.status }}</td>
						</tr>
					</tbody>
				</table>
			</div>
	</BaseModal>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';
import BaseButton from '../../../components/BaseButton.vue';
import api from '../../../services/api';

const props = defineProps({
	modelValue: Boolean,
	history: {
		type: Array,
		default: () => ([]),
	},
});

const loading = ref(false);

onMounted(() => {
    // getHistory();
})
</script>
