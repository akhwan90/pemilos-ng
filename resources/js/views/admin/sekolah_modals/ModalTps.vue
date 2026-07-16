<template>
  <BaseModal v-model="isOpen" title="Data TPS / Kelas" max-width="4xl">
    <div v-if="npsn" class="space-y-6">
      
      <!-- Form Tambah/Edit TPS -->
      <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="flex justify-between items-center mb-3">
          <h4 class="text-sm font-semibold text-gray-700">
            {{ isEditing ? 'Edit Data TPS' : 'Tambah TPS Baru' }}
          </h4>
          <button v-if="isEditing" @click="cancelEdit" class="text-xs text-gray-500 hover:text-gray-700">Batal Edit</button>
        </div>
        
        <form @submit.prevent="submitForm" class="flex flex-col sm:flex-row gap-3 items-end">
          <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Nama TPS / Kelas</label>
            <input v-model="form.nm_kelas" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Misal: TPS 01 atau Kelas X-A">
          </div>
          <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Lokasi</label>
            <select v-model="form.is_tps_luar_sekolah" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
              <option :value="false">Di Dalam Sekolah</option>
              <option :value="true">Di Luar Sekolah</option>
            </select>
          </div>
          <div class="w-full sm:w-auto">
            <BaseButton type="submit" variant="primary" :loading="isSubmitting" class="w-full sm:w-auto h-[38px]">
              {{ isEditing ? 'Simpan Perubahan' : 'Simpan TPS' }}
            </BaseButton>
          </div>
        </form>
      </div>

      <!-- Tabel List TPS -->
      <div>
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Daftar TPS Terdaftar</h4>
        <div class="overflow-hidden border border-gray-200 rounded-lg">
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
              <tr>
                <th class="px-4 py-3 w-12 text-center">No</th>
                <th class="px-4 py-3">Nama TPS / Kelas</th>
                <th class="px-4 py-3 text-center">Lokasi</th>
                <th class="px-4 py-3 text-center">Status Generate Token</th>
                <th class="px-4 py-3 w-32 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading" class="bg-white">
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Memuat data TPS...</td>
              </tr>
              <tr v-else-if="tpsList.length === 0" class="bg-white">
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada TPS/Kelas yang terdaftar.</td>
              </tr>
              <tr v-else v-for="(tps, index) in tpsList" :key="tps.kd_kelas" class="bg-white border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-center">{{ index + 1 }}</td>
                <td class="px-4 py-3 font-medium">{{ tps.nm_kelas }}</td>
                <td class="px-4 py-3 text-center">
                  <span v-if="tps.is_tps_luar_sekolah == 1" class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800">Luar Sekolah</span>
                  <span v-else class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">Dalam Sekolah</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span v-if="tps.is_generate_token == 1" class="text-green-600 font-medium">Sudah</span>
                  <span v-else class="text-gray-400">Belum</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="flex gap-2 justify-center">
                    <button @click="editTps(tps)" class="text-xs text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded border border-indigo-200">Edit</button>
                    <button @click="deleteTps(tps.kd_kelas)" class="text-xs text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded border border-red-200">Hapus</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

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
  npsn: { type: [String, Number], default: null }
});

const emit = defineEmits(['update:modelValue']);
const toast = useToast();

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const tpsList = ref([]);
const isLoading = ref(false);

const isEditing = ref(false);
const isSubmitting = ref(false);

const form = ref({
  kd_kelas: null,
  nm_kelas: '',
  is_tps_luar_sekolah: false
});

watch(() => isOpen.value, (newVal) => {
  if (newVal && props.npsn) {
    cancelEdit();
    fetchTps();
  }
});

async function fetchTps() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin/data-sekolah/${props.npsn}/tps`);
    tpsList.value = res.data.data;
  } catch (error) {
    toast.error('Gagal mengambil data TPS');
  } finally {
    isLoading.value = false;
  }
}

function editTps(tps) {
  isEditing.value = true;
  form.value.kd_kelas = tps.kd_kelas;
  form.value.nm_kelas = tps.nm_kelas;
  // konversi integer 1/0 ke boolean true/false untuk select box
  form.value.is_tps_luar_sekolah = tps.is_tps_luar_sekolah == 1;
}

function cancelEdit() {
  isEditing.value = false;
  form.value.kd_kelas = null;
  form.value.nm_kelas = '';
  form.value.is_tps_luar_sekolah = false;
}

async function submitForm() {
  isSubmitting.value = true;
  try {
    const payload = {
      nm_kelas: form.value.nm_kelas,
      is_tps_luar_sekolah: form.value.is_tps_luar_sekolah
    };

    if (isEditing.value) {
      await api.put(`/admin/data-sekolah/${props.npsn}/tps/${form.value.kd_kelas}`, payload);
      toast.success('TPS berhasil diperbarui');
    } else {
      await api.post(`/admin/data-sekolah/${props.npsn}/tps`, payload);
      toast.success('TPS berhasil ditambahkan');
    }
    
    cancelEdit();
    fetchTps();
  } catch (error) {
    toast.error(isEditing.value ? 'Gagal memperbarui TPS' : 'Gagal menambahkan TPS');
  } finally {
    isSubmitting.value = false;
  }
}

async function deleteTps(kd_kelas) {
  if (!confirm('Apakah Anda yakin ingin menghapus TPS ini?')) return;
  
  try {
    await api.delete(`/admin/data-sekolah/${props.npsn}/tps/${kd_kelas}`);
    toast.success('TPS berhasil dihapus');
    fetchTps();
  } catch (error) {
    toast.error('Gagal menghapus TPS');
  }
}
</script>
