<template>
	<BaseModal :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" title="Edit User">
		<form @submit.prevent="handleSubmit" class="space-y-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
				<input v-model="form.username" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 text-sm" readonly disabled />
				<p class="text-xs text-gray-500 mt-1">Username tidak dapat diubah.</p>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Opsional)</label>
				<input v-model="form.password" @keydown.space.prevent
					@input="form.password = form.password.replace(/\s/g, '')"
					type="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" />
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
				<select v-model="form.level" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 text-sm" disabled>
					<option value="1">Super Admin</option>
					<option value="2">Admin Sekolah</option>
					<option value="3">Admin TPS</option>
					<option value="4">Admin Pemantau</option>
				</select>
			</div>

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
import api from '../../../services/api';

const props = defineProps({
	modelValue: Boolean,
	user: {
		type: Object,
		default: () => ({}),
	},
});

const emit = defineEmits(['update:modelValue', 'saved', 'error']);

const loading = ref(false);
const form = ref({
	username: '',
	password: '',
	level: '',
});

watch(
	() => props.modelValue,
	(newVal) => {
		if (newVal) {
			form.value = {
				username: props.user.username || '',
				password: '', // always empty on open
				level: props.user.level || '',
			};
		}
	}
);

const handleSubmit = async () => {
	loading.value = true;
	try {
		const payload = {
			password: form.value.password,
		};
		// Only send if not empty
		if (!payload.password) {
			delete payload.password;
		}

		await api.put(`/admin/data-user/${props.user.id}`, payload);
		emit('saved');
		emit('update:modelValue', false);
	} catch (error) {
		console.error('Error saving user:', error);
		emit('error', error.response?.data?.message || 'Gagal menyimpan data user');
	} finally {
		loading.value = false;
	}
};
</script>
