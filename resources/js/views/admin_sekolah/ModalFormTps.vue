<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <h3 class="text-lg font-bold text-gray-800">{{ isEditing ? 'Edit TPS' : 'Tambah TPS Baru' }}</h3>
        <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <!-- Body Form -->
      <form @submit.prevent="submitForm" class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama TPS / Kelas</label>
          <input v-model="form.nm_kelas" type="text" placeholder="Cth: Kelas X-A" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi TPS</label>
          <select v-model="form.is_tps_luar_sekolah" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500 bg-white">
            <option :value="false">Di Dalam Sekolah</option>
            <option :value="true">Di Luar Sekolah</option>
          </select>
        </div>

        <div v-if="isEditing">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status Penghapusan</label>
          <select v-model="form.is_hapus" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500 bg-white">
            <option :value="false">0 - Aktif</option>
            <option :value="true">1 - Hapus</option>
          </select>
        </div>

        <div class="pt-4 flex gap-3 justify-end border-t border-gray-100 mt-6">
          <BaseButton type="button" variant="secondary" @click="close">
            Batal
          </BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">
            {{ isEditing ? 'Simpan Perubahan' : 'Tambah TPS' }}
          </BaseButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import BaseButton from '../../components/BaseButton.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  show: Boolean,
  isEditing: Boolean,
  tpsData: Object
});

const emit = defineEmits(['close', 'saved']);
const toast = useToast();

const isSubmitting = ref(false);
const form = ref({
  kd_kelas: null,
  nm_kelas: '',
  is_tps_luar_sekolah: false,
  is_hapus: false
});

watch(() => props.show, (newVal) => {
  if (newVal) {
    if (props.isEditing && props.tpsData) {
      form.value = {
        kd_kelas: props.tpsData.kd_kelas,
        nm_kelas: props.tpsData.nm_kelas,
        is_tps_luar_sekolah: props.tpsData.is_tps_luar_sekolah == 1,
        is_hapus: props.tpsData.is_hapus == 1
      };
    } else {
      form.value = {
        kd_kelas: null,
        nm_kelas: '',
        is_tps_luar_sekolah: false,
        is_hapus: false
      };
    }
  }
});

function close() {
  emit('close');
}

async function submitForm() {
  if (!form.value.nm_kelas) return;

  isSubmitting.value = true;
  try {
    if (props.isEditing) {
      await api.put(`/admin-sekolah/tps/${form.value.kd_kelas}`, form.value);
      toast.success('Data TPS berhasil diperbarui');
    } else {
      await api.post(`/admin-sekolah/tps`, form.value);
      toast.success('TPS baru berhasil ditambahkan');
    }
    emit('saved');
    close();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Terjadi kesalahan saat menyimpan data');
  } finally {
    isSubmitting.value = false;
  }
}
</script>
