<template>
	<BaseModal v-model="isOpen" title="Data Upload File Siswa" max-width="5xl">
		<div v-if="npsn" class="space-y-4">
			<!-- Tabel List Kandidat (View Mode) -->
			<div v-if="!isEditing" class="overflow-hidden border border-gray-200 rounded-lg">
				<table class="w-full text-sm text-left">
					<thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
						<tr>
							<th class="px-4 py-3 w-16 text-center">No</th>
							<th class="px-4 py-3 w-24">Nama File</th>
							<th class="px-4 py-3 w-24">Diupload pada</th>
							<th class="px-4 py-3 w-32 text-center">Detil</th>
						</tr>
					</thead>
					<tbody>
						<tr v-if="isLoading" class="bg-white">
							<td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat data upload...</td>
						</tr>
						<tr v-else-if="kandidatList.length === 0" class="bg-white">
							<td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada file diupload Admin Sekolah ini.</td>
						</tr>
						<tr v-else v-for="(k, index) in kandidatList" :key="k.id" class="bg-white border-b hover:bg-gray-50">
							<td class="px-4 py-3 text-center text-gray-700">{{ (index + 1) }}</td>
							<td class="px-4 py-3">{{ k.file_excel }}</td>
							<td class="px-4 py-3">{{ k.create_at }}</td>
							<td class="px-4 py-3 text-center">
								<div class="flex gap-2 justify-center">
									<button @click="detil(k)" class="text-xs text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded border border-blue-200">Detil</button>
									<button @click="download(k)" class="text-xs text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded border border-blue-200">Download</button>

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

const isOpen = computed({
	get: () => props.modelValue,
	set: (val) => emit('update:modelValue', val),
});

const kandidatList = ref([]);
const isLoading = ref(false);

const isEditing = ref(false);
const isSubmitting = ref(false);


const showDetil = ref(false);
const detilDatas = ref([]);

watch(() => isOpen.value, () => {
    fetchData();
});

async function detil(k) {
	try {
		const res = await api.get(`/admin/data-sekolah/${props.npsn}/upload/${k.id}`);
		detilDatas.value = res.data.data.data;
	} catch (error) {
		toast.error(error);
	} finally {
		showDetil.value = true;
	}
}

async function download(k) {
  try {
    const res = await api.get(`/admin/data-sekolah/${props.npsn}/upload/${k.id}/download`, {
    	responseType: 'blob' // Wajib agar respon dibaca sebagai file binary
    });

    // 1. Buat Blob URL dari data respon
    const blob = new Blob([res.data]);
    const url = window.URL.createObjectURL(blob);

    // 2. Buat elemen <a> sementara di DOM
    const link = document.createElement('a');
    link.href = url;

    // 3. Tentukan nama file (opsional: ambil dari header atau set manual)
    let fileName = `data-sekolah-${props.npsn}.xlsx`; // Sesuaikan ekstensi (cth: .pdf/.xlsx/.zip)
    
    // Jika backend mengirim header Content-Disposition:
    const disposition = res.headers['content-disposition'];
    if (disposition && disposition.includes('filename=')) {
      const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
      if (match && match[1]) {
        fileName = match[1].replace(/['"]/g, '');
      }
    }

    link.setAttribute('download', fileName);

    // 4. Trigger klik dan bersihkan URL objek
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);

    toast.success('Download berhasil!');
  } catch (error) {
    console.error(error);
    toast.error('Gagal download');
  } finally {
  }
}

async function fetchData() {
	isLoading.value = true;
	try {
		const res = await api.get(`/admin/data-sekolah/${props.npsn}/upload`);
		kandidatList.value = res.data.data;
	} catch (error) {
		toast.error('Gagal mengambil data kandidat');
	} finally {
		isLoading.value = false;
	}
}
</script>
