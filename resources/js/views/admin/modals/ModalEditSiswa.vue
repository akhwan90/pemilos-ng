<template>
	<BaseModal :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" title="Edit User" maxWidth="xl">
		<form @submit.prevent="handleSubmit" class="space-y-4">
			<BaseInput v-model="form.nisn" label="NISN"/>
			<BaseInput v-model="form.nm_siswa" label="Nama Siswa"/>
			<BaseInput v-model="form.jk" label="Jenis Kelamin"/>
			<BaseInput v-model="form.kelas" label="Kelas"/>
			<BaseInput v-model="form.difabel" label="Status Difabilitas"/>
			<BaseInput v-model="form.npsn" label="NPSN"/>
			<BaseInput v-model="form.status" label="Status"/>
			<BaseInput v-model="form.tahun" label="Tahun"/>
			<BaseInput v-model="form.create_at" label="Create At"/>
			<BaseInput v-model="form.hapus_time" label="Hapus Time"/>
			

			<div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
				<BaseButton type="button" variant="secondary" @click="$emit('update:modelValue', false)">Batal</BaseButton>
				<BaseButton type="submit" variant="primary" :disabled="loading">
					<template v-if="loading">Menyimpan...</template>
					<template v-else>Simpan Perubahan</template>
				</BaseButton>
			</div>
		</form>
	</BaseModal>
</template>

<script setup>
import { ref, watch } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';
import BaseButton from '../../../components/BaseButton.vue';
import BaseInput from '../../../components/BaseInput.vue';
import api from '../../../services/api';
import { useToast } from '../../../composables/useToast';


const toast = useToast();

const form = ref({
	id: null,
	nisn: '',
	nm_siswa: '',
	jk: '',
	kelas: '',
	difabel: '',
	npsn: '',
	status: '',
	tahun: '',
	create_at: '',
	hapus_time: '',
});

const props = defineProps({
	modelValue: Boolean,
	siswa: {
		type: Object,
		default: () => ({}),
	},
});

const emit = defineEmits(['update:modelValue', 'saved', 'error']);

const loading = ref(false);


watch(
	() => props.modelValue,
	(newVal) => {
		if (newVal) {
			form.value = {
				id: props.siswa.id,
				nisn: props.siswa.nisn || '',
				nm_siswa: props.siswa.nm_siswa || '',
				jk: props.siswa.jk || '',
				kelas: props.siswa.kelas || '',
				difabel: props.siswa.difabel || '',
				npsn: props.siswa.npsn || '',
				status: props.siswa.status || '',
				tahun: props.siswa.tahun || '',
				create_at: props.siswa.create_at || '',
				hapus_time: props.siswa.hapus_time || '',
			};
		}
	}
);

const handleSubmit = async () => {
	loading.value = true;
	try {
		await api.put(`/admin/data-siswa-global/${props.siswa.id}`, form.value);
		emit('saved');
		emit('update:modelValue', false);
	} catch (error) {
		console.error('Error saving user:', error);
        toast.error(error.response?.data?.message || 'Gagal menyimpan');
		// emit('error', error.response?.data?.message || 'Gagal menyimpan data user');
	} finally {
		loading.value = false;
	}
};
</script>
