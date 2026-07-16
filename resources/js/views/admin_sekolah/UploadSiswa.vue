<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Upload Data Siswa</h2>
        <p class="text-sm text-gray-500">Import data siswa secara massal menggunakan file Excel (.xlsx).</p>
      </div>
      <BaseButton variant="primary" @click="isUploadModalOpen = true">
        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Upload File Excel
      </BaseButton>
    </div>

    <!-- Peringatan -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-blue-700">
            Pastikan format file Excel Anda mengikuti template standar. Baris pertama (header) akan diabaikan. Urutan kolom wajib: 
            <strong>NISN | NIK | Nama Siswa | JK (L/P) | Kelas | Difabel (Ya/Tidak) | No. WA | Email</strong>.
          </p>
        </div>
      </div>
    </div>

    <!-- History Antrean -->
    <BaseCard class="p-0">
      <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <h3 class="font-semibold text-gray-700">Riwayat Antrean Upload</h3>
        <BaseButton variant="secondary" @click="fetchHistory" class="!py-1 !px-3 text-xs" :loading="isLoading">
          Refresh Status
        </BaseButton>
      </div>

      <div class="overflow-x-auto min-h-[300px]">
        <table class="w-full text-sm text-left">
          <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
            <tr>
              <th class="px-4 py-3 text-center">No</th>
              <th class="px-4 py-3">Nama File</th>
              <th class="px-4 py-3">Waktu Mulai</th>
              <th class="px-4 py-3">Waktu Selesai</th>
              <th class="px-4 py-3 text-center">Status</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading && history.length === 0" class="bg-white border-b">
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">Memuat riwayat...</td>
            </tr>
            <tr v-else-if="history.length === 0" class="bg-white border-b">
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat upload.</td>
            </tr>
            <tr v-else v-for="(item, index) in history" :key="item.id" class="bg-white border-b hover:bg-gray-50">
              <td class="px-4 py-3 text-center">{{ index + 1 }}</td>
              <td class="px-4 py-3 font-medium">{{ item.file_excel }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ item.create_at }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ item.finish_at || '-' }}</td>
              <td class="px-4 py-3 text-center">
                <span v-if="item.is_selesai == 1" class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Selesai</span>
                <span v-else class="px-2 py-1 text-xs font-semibold rounded bg-amber-100 text-amber-800 flex inline-flex items-center gap-1">
                  <svg class="animate-spin h-3 w-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  Diproses
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="openLogs(item.id)" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded text-xs font-medium">Lihat Log</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </BaseCard>

    <!-- Modal Form Upload -->
    <BaseModal v-model="isUploadModalOpen" title="Upload File Excel" max-width="md">
      <form @submit.prevent="submitUpload" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File (.xlsx)</label>
          <input type="file" ref="fileInput" accept=".xlsx" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
          <p class="mt-2 text-xs text-gray-500">Maksimal ukuran file: 2 MB</p>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
          <BaseButton type="button" variant="secondary" @click="isUploadModalOpen = false">Batal</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">Mulai Upload</BaseButton>
        </div>
      </form>
    </BaseModal>

    <!-- Modal Log Detail -->
    <BaseModal v-model="isLogModalOpen" title="Detail Log Import" max-width="4xl">
      <div v-if="isLoadingLogs" class="py-8 text-center text-gray-500">Memuat log...</div>
      <div v-else>
        <div class="mb-4 flex gap-4 text-sm font-semibold">
          <span class="text-green-600">Sukses: {{ logs.filter(l => l.is_success == 1).length }}</span>
          <span class="text-red-600">Gagal Pindah: {{ logs.filter(l => l.is_success == 0).length }}</span>
          <span class="text-gray-600">Total Baris: {{ logs.length }}</span>
        </div>
        <div class="overflow-y-auto max-h-[60vh] bg-gray-50 rounded border text-xs">
          <table class="w-full text-left">
            <thead class="bg-gray-200 sticky top-0">
              <tr>
                <th class="px-3 py-2 w-12 text-center">#</th>
                <th class="px-3 py-2 w-32">Waktu</th>
                <th class="px-3 py-2 w-24 text-center">Status</th>
                <th class="px-3 py-2">Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(log, idx) in logs" :key="log.id" class="border-b" :class="log.is_success == 1 ? '' : 'bg-red-50'">
                <td class="px-3 py-2 text-center text-gray-500">{{ idx + 1 }}</td>
                <td class="px-3 py-2 text-gray-500 whitespace-nowrap">{{ log.waktu }}</td>
                <td class="px-3 py-2 text-center">
                  <span v-if="log.is_success == 1" class="text-green-600 font-bold">OK</span>
                  <span v-else class="text-red-600 font-bold">FAIL</span>
                </td>
                <td class="px-3 py-2 font-mono" :class="log.is_success == 1 ? 'text-gray-700' : 'text-red-700'">{{ log.keterangan }}</td>
              </tr>
              <tr v-if="logs.length === 0">
                <td colspan="4" class="px-3 py-6 text-center text-gray-500">Log kosong belum ada aktivitas</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </BaseModal>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseModal from '../../components/BaseModal.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const history = ref([]);
const isLoading = ref(false);

const isUploadModalOpen = ref(false);
const isSubmitting = ref(false);
const fileInput = ref(null);

const isLogModalOpen = ref(false);
const isLoadingLogs = ref(false);
const logs = ref([]);

onMounted(() => {
  fetchHistory();
});

async function fetchHistory() {
  isLoading.value = true;
  try {
    const res = await api.get('/admin-sekolah/upload-siswa');
    history.value = res.data.data;
  } catch (error) {
    toast.error('Gagal mengambil riwayat upload');
  } finally {
    isLoading.value = false;
  }
}

async function submitUpload() {
  const file = fileInput.value.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('file_excel', file);

  isSubmitting.value = true;
  try {
    await api.post('/admin-sekolah/upload-siswa', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    toast.success('File berhasil diupload dan masuk antrean proses');
    isUploadModalOpen.value = false;
    fileInput.value.value = '';
    fetchHistory();
  } catch (error) {
    if (error.response?.status === 403) {
      toast.error(error.response.data.message);
    } else {
      toast.error('Gagal mengupload file excel');
    }
  } finally {
    isSubmitting.value = false;
  }
}

async function openLogs(jobId) {
  isLogModalOpen.value = true;
  isLoadingLogs.value = true;
  logs.value = [];
  
  try {
    const res = await api.get(`/admin-sekolah/upload-siswa/${jobId}/logs`);
    logs.value = res.data.data;
  } catch (error) {
    toast.error('Gagal mengambil data log');
  } finally {
    isLoadingLogs.value = false;
  }
}
</script>
