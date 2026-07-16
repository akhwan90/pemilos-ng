<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Data TPS / Kelas</h2>
        <p class="text-sm text-gray-500">Kelola daftar TPS atau kelas untuk instansi ini.</p>
      </div>
    </div>

    <!-- Layout 2 Kolom -->
    <div class="flex flex-col md:flex-row gap-6 items-start">
      
      <!-- Form Input -->
      <BaseCard class="w-full md:w-1/3 !p-0">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
          <h3 class="font-semibold text-gray-800">{{ isEditing ? 'Edit TPS' : 'Tambah TPS Baru' }}</h3>
        </div>
        <form @submit.prevent="submitForm" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama TPS / Kelas</label>
            <input v-model="form.nm_kelas" type="text" placeholder="Cth: Kelas X-A" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi TPS</label>
            <select v-model="form.is_tps_luar_sekolah" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
              <option :value="false">Di Dalam Sekolah</option>
              <option :value="true">Di Luar Sekolah</option>
            </select>
          </div>
          
          <div v-if="isEditing">
            <label class="block text-sm font-medium text-gray-700 mb-1">Status Penghapusan</label>
            <select v-model="form.is_hapus" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
              <option :value="false">0 - Aktif</option>
              <option :value="true">1 - Terhapus</option>
            </select>
          </div>
          
          <div class="pt-4 flex flex-col gap-2">
            <BaseButton type="submit" variant="primary" :loading="isSubmitting" class="w-full">
              {{ isEditing ? 'Simpan Perubahan' : 'Tambah TPS' }}
            </BaseButton>
            <BaseButton v-if="isEditing" type="button" variant="secondary" @click="resetForm" class="w-full">
              Batal Edit
            </BaseButton>
          </div>
        </form>
      </BaseCard>

      <!-- Tabel Data -->
      <BaseCard class="w-full md:w-2/3 !p-0">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
              <tr>
                <th class="px-4 py-3 w-16 text-center">No</th>
                <th class="px-4 py-3">Nama Kelas / TPS</th>
                <th class="px-4 py-3 text-center">Lokasi</th>
                <th class="px-4 py-3 text-center">Status Hapus</th>
                <th class="px-4 py-3 w-32 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading" class="bg-white border-b">
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Memuat data TPS...</td>
              </tr>
              <tr v-else-if="items.length === 0" class="bg-white border-b">
                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data TPS.</td>
              </tr>
              <tr v-else v-for="(item, index) in items" :key="item.kd_kelas" class="bg-white border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-center">{{ index + 1 }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ item.nm_kelas }}</td>
                <td class="px-4 py-3 text-center">
                  <span v-if="item.is_tps_luar_sekolah == 1" class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800">Luar Sekolah</span>
                  <span v-else class="text-gray-500 text-xs">Dalam Sekolah</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <span v-if="item.is_hapus == 1" class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Terhapus (1)</span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="flex gap-2 justify-center">
                    <button @click="editTps(item)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded text-xs">Edit</button>
                    <button v-if="item.is_hapus == 0" @click="deleteTps(item.kd_kelas)" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded text-xs">Hapus</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </BaseCard>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const items = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const isEditing = ref(false);

const form = ref({
  kd_kelas: null,
  nm_kelas: '',
  is_tps_luar_sekolah: false,
  is_hapus: false
});

onMounted(() => {
  fetchTps();
});

async function fetchTps() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin-sekolah/tps`);
    items.value = res.data.data;
  } catch (error) {
    toast.error('Gagal memuat data TPS');
  } finally {
    isLoading.value = false;
  }
}

function resetForm() {
  isEditing.value = false;
  form.value = {
    kd_kelas: null,
    nm_kelas: '',
    is_tps_luar_sekolah: false,
    is_hapus: false
  };
}

function editTps(tps) {
  isEditing.value = true;
  form.value.kd_kelas = tps.kd_kelas;
  form.value.nm_kelas = tps.nm_kelas;
  form.value.is_tps_luar_sekolah = tps.is_tps_luar_sekolah == 1;
  form.value.is_hapus = tps.is_hapus == 1;
}

async function submitForm() {
  if (!form.value.nm_kelas) return;
  
  isSubmitting.value = true;
  try {
    if (isEditing.value) {
      await api.put(`/admin-sekolah/tps/${form.value.kd_kelas}`, form.value);
      toast.success('Data TPS berhasil diperbarui');
    } else {
      await api.post(`/admin-sekolah/tps`, form.value);
      toast.success('TPS baru berhasil ditambahkan');
    }
    resetForm();
    fetchTps();
  } catch (error) {
    toast.error('Terjadi kesalahan saat menyimpan data');
  } finally {
    isSubmitting.value = false;
  }
}

async function deleteTps(kd_kelas) {
  if (!confirm('Anda yakin ingin menghapus TPS ini? Data hanya akan ditandai sebagai terhapus.')) return;
  
  try {
    await api.delete(`/admin-sekolah/tps/${kd_kelas}`);
    toast.success('TPS berhasil dihapus (soft delete)');
    fetchTps();
  } catch (error) {
    toast.error('Gagal menghapus TPS');
  }
}
</script>
