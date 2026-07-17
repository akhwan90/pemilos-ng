<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Data TPS / Kelas</h2>
        <p class="text-sm text-gray-500">Kelola daftar TPS atau kelas untuk instansi ini.</p>
      </div>
      <div>
        <BaseButton variant="primary" @click="openAddModal">
          <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
          Tambah TPS
        </BaseButton>
      </div>
    </div>

    <!-- Tabel Data -->
    <BaseCard class="w-full !p-0">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
            <tr>
              <th class="px-4 py-3 w-16 text-center">No</th>
              <th class="px-4 py-3">Nama Kelas / TPS</th>
              <th class="px-4 py-3 text-center">Lokasi</th>
              <th class="px-4 py-3 text-center">Status Hapus</th>
              <th class="px-4 py-3 text-center">Aksi</th>
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
                  <button @click="openAdminModal(item)" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 px-2 py-1 rounded text-xs" title="Kelola Admin TPS">Admin TPS</button>
                  <button @click="openEditModal(item)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded text-xs">Edit</button>
                  <button v-if="item.is_hapus == 0" @click="deleteTps(item.kd_kelas)" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded text-xs">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </BaseCard>

    <ModalFormTps 
      :show="showFormModal" 
      :is-editing="isEditing" 
      :tps-data="selectedTps" 
      @close="showFormModal = false" 
      @saved="fetchTps" 
    />

    <ModalAdminTps 
      :show="showAdminModal" 
      :tps="selectedTps" 
      @close="showAdminModal = false" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';
import ModalFormTps from './ModalFormTps.vue';
import ModalAdminTps from './ModalAdminTps.vue';

const toast = useToast();

const items = ref([]);
const isLoading = ref(false);

const showFormModal = ref(false);
const showAdminModal = ref(false);
const isEditing = ref(false);
const selectedTps = ref(null);

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

function openAddModal() {
  isEditing.value = false;
  selectedTps.value = null;
  showFormModal.value = true;
}

function openEditModal(tps) {
  isEditing.value = true;
  selectedTps.value = tps;
  showFormModal.value = true;
}

function openAdminModal(tps) {
  selectedTps.value = tps;
  showAdminModal.value = true;
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
