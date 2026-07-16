<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Data Siswa</h2>
        <p class="text-sm text-gray-500">Kelola data pemilih (siswa) di sekolah Anda.</p>
      </div>
      <div class="flex gap-2">
        <BaseButton variant="primary" @click="openModal('add')">
          Tambah Siswa
        </BaseButton>
      </div>
    </div>
    
    <BaseCard class="p-0">
      <!-- Toolbar -->
      <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <div class="flex gap-2 items-center">
          <div class="relative w-64">
            <input v-model="searchQuery" @keyup.enter="fetchSiswa(1)" type="text" placeholder="Cari NISN, Nama, atau Kelas..." 
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
          </div>
          
          <!-- Bulk Delete Trigger Button -->
          <BaseButton v-if="selectedIds.length > 0" variant="danger" @click="isBulkDeleteModalOpen = true" class="!py-2 !h-[38px] text-xs">
            Hapus {{ selectedIds.length }} Terpilih
          </BaseButton>
        </div>

        <div class="flex gap-2 text-sm text-gray-500">
          Total: <span class="font-bold text-gray-700">{{ pagination.total }}</span> siswa
        </div>
      </div>

      <!-- Tabel -->
      <div class="overflow-x-auto min-h-[300px]">
        <table class="w-full text-sm text-left">
          <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
            <tr>
              <th class="px-4 py-3 w-10 text-center">
                <input type="checkbox" @change="toggleAll" :checked="isAllSelected" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
              </th>
              <th class="px-4 py-3 w-16 text-center">No</th>
              <th class="px-4 py-3">NISN</th>
              <th class="px-4 py-3">Nama Siswa</th>
              <th class="px-4 py-3 text-center">Kelas</th>
              <th class="px-4 py-3 text-center">Jenis Kelamin</th>
              <th class="px-4 py-3 text-center">Status Difabel</th>
              <th class="px-4 py-3 w-32 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading" class="bg-white border-b">
              <td colspan="8" class="px-4 py-8 text-center text-gray-500">Memuat data siswa...</td>
            </tr>
            <tr v-else-if="items.length === 0" class="bg-white border-b">
              <td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada data siswa ditemukan.</td>
            </tr>
            <tr v-else v-for="(item, index) in items" :key="item.id" class="bg-white border-b hover:bg-gray-50" :class="{ 'bg-indigo-50/50': isSelected(item.id) }">
              <td class="px-4 py-3 text-center">
                <input type="checkbox" :value="item.id" v-model="selectedIds" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
              </td>
              <td class="px-4 py-3 text-center">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
              <td class="px-4 py-3 font-mono text-gray-600">{{ item.nisn }}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{{ item.nm_siswa }}</td>
              <td class="px-4 py-3 text-center">{{ item.kelas }}</td>
              <td class="px-4 py-3 text-center">
                <span v-if="item.jk == 1" class="text-blue-600">Laki-laki</span>
                <span v-else-if="item.jk == 2" class="text-pink-600">Perempuan</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span v-if="item.difabel == 1" class="px-2 py-1 text-xs font-semibold rounded bg-amber-100 text-amber-800">Difabel</span>
                <span v-else class="text-gray-400 text-xs">-</span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex gap-2 justify-center">
                  <button @click="openModal('edit', item)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded text-xs">Edit</button>
                  <button @click="deleteSiswa(item.id)" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded text-xs">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > 0" class="px-4 py-3 border-t bg-gray-50 flex items-center justify-between">
        <span class="text-sm text-gray-700">
          Menampilkan <span class="font-semibold">{{ (pagination.current_page - 1) * pagination.per_page + 1 }}</span>
          sampai <span class="font-semibold">{{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}</span>
          dari <span class="font-semibold">{{ pagination.total }}</span>
        </span>
        <div class="flex gap-1">
          <button @click="fetchSiswa(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 py-1 border rounded bg-white disabled:opacity-50 text-sm">Prev</button>
          <button @click="fetchSiswa(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 py-1 border rounded bg-white disabled:opacity-50 text-sm">Next</button>
        </div>
      </div>
    </BaseCard>

    <!-- Modal Form Siswa -->
    <BaseModal v-model="isModalOpen" :title="isEditMode ? 'Edit Siswa' : 'Tambah Siswa'" max-width="xl">
      <form @submit.prevent="submitForm" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
          <input v-model="form.nisn" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
          <input v-model="form.nm_siswa" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <input v-model="form.kelas" type="text" required placeholder="Cth: X-A" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
            <select v-model="form.jk" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
              <option value="1">Laki-laki</option>
              <option value="2">Perempuan</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status Difabel</label>
          <select v-model="form.difabel" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
            <option value="0">Tidak Difabel</option>
            <option value="1">Penyandang Disabilitas</option>
          </select>
        </div>
        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
          <BaseButton type="button" variant="secondary" @click="isModalOpen = false">Batal</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">{{ isEditMode ? 'Simpan Perubahan' : 'Tambah Siswa' }}</BaseButton>
        </div>
      </form>
    </BaseModal>

    <!-- Modal Konfirmasi Hapus Bulk -->
    <BaseModal v-model="isBulkDeleteModalOpen" title="Hapus Siswa Terpilih" max-width="md">
      <form @submit.prevent="submitBulkDelete" class="space-y-4">
        <p class="text-sm text-gray-600 mb-4">
          Anda akan menghapus <strong>{{ selectedIds.length }}</strong> siswa. Silakan pilih alasan penghapusan (akan dicatat di database).
        </p>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Hapus</label>
          <div class="flex flex-col gap-2">
            <label class="flex items-center">
              <input type="radio" v-model="bulkDeleteReason" value="Lulus" required class="text-indigo-600 focus:ring-indigo-500 mr-2">
              <span class="text-sm">Lulus</span>
            </label>
            <label class="flex items-center">
              <input type="radio" v-model="bulkDeleteReason" value="Pindah" required class="text-indigo-600 focus:ring-indigo-500 mr-2">
              <span class="text-sm">Pindah / Mutasi Keluar</span>
            </label>
            <label class="flex items-center">
              <input type="radio" v-model="bulkDeleteReason" value="Lainnya" required class="text-indigo-600 focus:ring-indigo-500 mr-2">
              <span class="text-sm">Lainnya</span>
            </label>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
          <BaseButton type="button" variant="secondary" @click="isBulkDeleteModalOpen = false">Batal</BaseButton>
          <BaseButton type="submit" variant="danger" :loading="isSubmittingBulkDelete">Konfirmasi Hapus</BaseButton>
        </div>
      </form>
    </BaseModal>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseModal from '../../components/BaseModal.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const items = ref([]);
const isLoading = ref(false);
const searchQuery = ref('');
const pagination = ref({ current_page: 1, last_page: 1, total: 0, per_page: 20 });

// Modal Form State
const isModalOpen = ref(false);
const isEditMode = ref(false);
const isSubmitting = ref(false);
const formId = ref(null);
const form = ref({
  nisn: '',
  nm_siswa: '',
  kelas: '',
  jk: '1',
  difabel: '0'
});

// State for Bulk Delete
const selectedIds = ref([]);
const isBulkDeleteModalOpen = ref(false);
const bulkDeleteReason = ref('');
const isSubmittingBulkDelete = ref(false);

const isAllSelected = computed(() => {
  return items.value.length > 0 && selectedIds.value.length === items.value.length;
});

function toggleAll(event) {
  if (event.target.checked) {
    selectedIds.value = items.value.map(item => item.id);
  } else {
    selectedIds.value = [];
  }
}

function isSelected(id) {
  return selectedIds.value.includes(id);
}

onMounted(() => {
  fetchSiswa(1);
});

async function fetchSiswa(page = 1) {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin-sekolah/siswa?page=${page}&cari=${searchQuery.value}`);
    items.value = res.data.data.data;
    // Reset selection on fetch
    selectedIds.value = [];
    
    pagination.value = {
      current_page: res.data.data.current_page,
      last_page: res.data.data.last_page,
      total: res.data.data.total,
      per_page: res.data.data.per_page
    };
  } catch (error) {
    toast.error('Gagal mengambil data siswa');
  } finally {
    isLoading.value = false;
  }
}

function openModal(mode, data = null) {
  isModalOpen.value = true;
  if (mode === 'edit' && data) {
    isEditMode.value = true;
    formId.value = data.id;
    form.value = {
      nisn: data.nisn,
      nm_siswa: data.nm_siswa,
      kelas: data.kelas,
      jk: data.jk,
      difabel: data.difabel || 0
    };
  } else {
    isEditMode.value = false;
    formId.value = null;
    form.value = { nisn: '', nm_siswa: '', kelas: '', jk: '1', difabel: '0' };
  }
}

async function submitForm() {
  isSubmitting.value = true;
  try {
    if (isEditMode.value) {
      await api.put(`/admin-sekolah/siswa/${formId.value}`, form.value);
      toast.success('Data siswa berhasil diperbarui');
    } else {
      await api.post(`/admin-sekolah/siswa`, form.value);
      toast.success('Siswa baru berhasil ditambahkan');
    }
    isModalOpen.value = false;
    fetchSiswa(pagination.value.current_page);
  } catch (error) {
    if (error.response?.data?.errors?.nisn) {
      toast.error('Gagal: NISN sudah terdaftar di database.');
    } else {
      toast.error('Terjadi kesalahan saat menyimpan data.');
    }
  } finally {
    isSubmitting.value = false;
  }
}

async function deleteSiswa(id) {
  if (!confirm('Anda yakin ingin menghapus data siswa ini?')) return;
  try {
    await api.delete(`/admin-sekolah/siswa/${id}`);
    toast.success('Siswa berhasil dihapus (soft delete)');
    fetchSiswa(pagination.value.current_page);
  } catch (error) {
    toast.error('Gagal menghapus siswa');
  }
}
async function submitBulkDelete() {
  if (selectedIds.value.length === 0) return;
  
  isSubmittingBulkDelete.value = true;
  try {
    const payload = {
      ids: selectedIds.value,
      alasan_hapus: bulkDeleteReason.value
    };
    
    await api.post(`/admin-sekolah/siswa/bulk-delete`, payload);
    
    toast.success(`${selectedIds.value.length} siswa berhasil dihapus`);
    isBulkDeleteModalOpen.value = false;
    bulkDeleteReason.value = '';
    
    // Refresh table
    fetchSiswa(pagination.value.current_page);
  } catch (error) {
    toast.error('Gagal melakukan hapus massal');
  } finally {
    isSubmittingBulkDelete.value = false;
  }
}
</script>
