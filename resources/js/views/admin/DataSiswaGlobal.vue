<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Semua Data Siswa</h2>
        <p class="text-sm text-gray-500">Lihat data seluruh siswa dari berbagai sekolah (Global).</p>
      </div>
    </div>

    <BaseCard class="p-0">
      <!-- Toolbar -->
      <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-wrap gap-4">
        <div class="flex gap-2 items-center w-full md:w-auto">
          <!-- Filter Sekolah -->
          <div class="relative w-full md:w-64">
            <select v-model="filterNpsn" @change="fetchSiswa(1)" class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
              <option value="">Semua Sekolah</option>
              <option v-for="sekolah in daftarSekolah" :key="sekolah.npsn" :value="sekolah.npsn">
                {{ sekolah.nama_sekolah || sekolah.nm_sekolah || sekolah.npsn }}
              </option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </div>
          </div>
        </div>

        <div class="relative w-full md:w-64">
          <input
            v-model="search"
            @keyup.enter="fetchSiswa(1)"
            type="text"
            placeholder="Cari nama atau NISN..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500"
          >
          <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
              <th class="px-6 py-4 font-medium">NISN</th>
              <th class="px-6 py-4 font-medium">Nama Siswa</th>
              <th class="px-6 py-4 font-medium">Sekolah</th>
              <th class="px-6 py-4 font-medium">Kelas</th>
              <th class="px-6 py-4 font-medium">Jenis Kelamin</th>
              <th class="px-6 py-4 font-medium text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 text-sm">
            <tr v-if="loading">
              <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                <div class="flex justify-center items-center gap-2">
                  <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  Memuat data...
                </div>
              </td>
            </tr>
            <tr v-else-if="siswa.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center">
                  <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                  <p>Tidak ada data siswa ditemukan.</p>
                </div>
              </td>
            </tr>
            <tr v-else v-for="item in siswa" :key="item.id_siswa" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="font-medium text-gray-900">{{ item.nisn }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-gray-900">{{ item.nm_siswa }}</div>
                <div class="text-xs text-gray-500">{{ item.nis }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                {{ item.nama_sekolah || item.nm_sekolah || '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ item.kelas }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center text-xs font-medium" :class="item.jk === 'L' ? 'text-indigo-600' : 'text-pink-600'">
                  <svg v-if="item.jk === 'L'" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                  <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                  {{ item.jk === 'L' ? 'Laki-laki' : 'Perempuan' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="deleteSiswa(item)" class="text-red-600 hover:text-red-900 focus:outline-none bg-red-50 p-1.5 rounded" title="Hapus Permanen">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between" v-if="pagination.total > 0">
        <div class="text-sm text-gray-500">
          Menampilkan <span class="font-medium text-gray-900">{{ pagination.from }}</span> sampai <span class="font-medium text-gray-900">{{ pagination.to }}</span> dari <span class="font-medium text-gray-900">{{ pagination.total }}</span> data
        </div>
        <div class="flex gap-2">
          <button
            @click="fetchSiswa(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Sebelumnya
          </button>
          <button
            @click="fetchSiswa(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Selanjutnya
          </button>
        </div>
      </div>
    </BaseCard>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../../services/api';
import BaseCard from '../../components/BaseCard.vue';
import ToastNotification from '../../components/ToastNotification.vue';

const loading = ref(false);
const siswa = ref([]);
const daftarSekolah = ref([]);
const filterNpsn = ref('');
const search = ref('');
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
});

// Use the Toast Component logically assuming it's globally registered or imported
const toast = ref({ show: false, message: '', type: 'success' });
const showToast = (message, type = 'success') => {
  // Try to use a global bus or store if available, otherwise fallback to alert for simplicity here
  // Assuming useToast is not easily available, we fallback to custom event or just console.error
  console.log(message);
};

const fetchSekolah = async () => {
  try {
    const response = await api.get('/admin/data-sekolah', { params: { no_pagination: 1 } });

    // Support paginated or unpaginated response
    if (response.data && Array.isArray(response.data)) {
        daftarSekolah.value = response.data;
    } else if (response.data && response.data.data && Array.isArray(response.data.data)) {
        daftarSekolah.value = response.data.data;
    } else {
        daftarSekolah.value = [];
    }
  } catch (error) {
    console.error('Error fetching data sekolah:', error);
  }
};

const fetchSiswa = async (page = 1) => {
  loading.value = true;
  try {
    const params = {
      page,
    };

    if (search.value) params.cari = search.value;
    if (filterNpsn.value) params.npsn = filterNpsn.value;

    const response = await api.get('/admin/data-siswa-global', { params });

    siswa.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total,
      from: response.data.from,
      to: response.data.to
    };
  } catch (error) {
    console.error('Error fetching data siswa global:', error);
    showToast('Gagal memuat data siswa', 'error');
  } finally {
    loading.value = false;
  }
};

// Debounce search
let searchTimeout;
watch(search, () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchSiswa(1);
  }, 500);
});

const deleteSiswa = async (item) => {
  if (confirm(`Apakah Anda yakin ingin menghapus data siswa "${item.nm_siswa}" secara permanen? Data yang telah dihapus tidak dapat dikembalikan.`)) {
    try {
      await api.delete(`/admin/data-siswa-global/${item.id}`);
      showToast('Data siswa berhasil dihapus secara permanen', 'success');
      fetchSiswa(pagination.value.current_page);
    } catch (error) {
      console.error('Error deleting data siswa:', error);
      showToast(error.response?.data?.message || 'Gagal menghapus data siswa', 'error');
    }
  }
};

onMounted(async () => {
  await fetchSekolah();
  fetchSiswa();
});
</script>
