<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Daftar Pemilih Tetap (DPT)</h2>
        <p class="text-sm text-gray-500">Kelola pemetaan siswa yang berhak memilih beserta lokasi TPS-nya.</p>
      </div>
    </div>

    <!-- Toolbar Atas -->
    <BaseCard class="p-0">
      <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 flex-wrap gap-4">
        
        <!-- Kolom Kiri: Filter -->
        <div class="flex gap-2 items-center flex-wrap">
          <div class="relative w-48">
            <select v-model="filterTps" @change="fetchDpt(1)" class="w-full pl-3 pr-8 py-2 border rounded-lg text-sm focus:ring-indigo-500 bg-white shadow-sm">
              <option value="">-- Semua TPS --</option>
              <option v-for="tps in listTps" :key="tps.kd_kelas" :value="tps.kd_kelas">{{ tps.nm_kelas }}</option>
            </select>
          </div>
          <div class="relative w-64">
            <input v-model="searchQuery" @keyup.enter="fetchDpt(1)" type="text" placeholder="Cari NISN atau Nama Siswa..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-indigo-500 shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
          </div>
          <BaseButton variant="secondary" @click="fetchDpt(1)" class="!py-2 text-sm shadow-sm">Cari</BaseButton>
          
          <!-- Tombol Hapus Bulk Terpilih -->
          <BaseButton v-if="selectedIds.length > 0" variant="danger" @click="submitBulkDelete" class="!py-2 text-sm shadow-sm flex gap-1 items-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Hapus {{ selectedIds.length }} Terpilih
          </BaseButton>
        </div>

        <!-- Kolom Kanan: Tombol Tambah DPT -->
        <BaseButton variant="primary" @click="openAddDptModal" class="!py-2 shadow-sm flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Tambah DPT Baru
        </BaseButton>
      </div>

      <!-- Tabel DPT -->
      <div class="overflow-x-auto min-h-[400px]">
        <table class="w-full text-sm text-left">
          <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
            <tr>
              <th class="px-4 py-3 w-10 text-center">
                <input type="checkbox" @change="toggleAll" :checked="isAllSelected" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
              </th>
              <th class="px-4 py-3">NISN</th>
              <th class="px-4 py-3">Nama Lengkap</th>
              <th class="px-4 py-3 text-center">L/P</th>
              <th class="px-4 py-3">Kelas Asal</th>
              <th class="px-4 py-3 text-center bg-indigo-50 border-l border-r border-indigo-100 text-indigo-800">TPS Saat Ini</th>
              <th class="px-4 py-3 text-center">Status Pilih</th>
              <th class="px-4 py-3 w-20 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading" class="bg-white border-b">
              <td colspan="8" class="px-4 py-8 text-center text-gray-500">Memuat data DPT...</td>
            </tr>
            <tr v-else-if="items.length === 0" class="bg-white border-b">
              <td colspan="8" class="px-4 py-8 text-center text-gray-500">Data DPT kosong. Silakan tambah DPT baru.</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id" class="bg-white border-b hover:bg-gray-50" :class="{ 'bg-indigo-50': selectedIds.includes(item.id) }">
              <td class="px-4 py-3 text-center">
                <input type="checkbox" :value="item.id" v-model="selectedIds" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
              </td>
              <td class="px-4 py-3 font-mono text-gray-600">{{ item.nisn }}</td>
              <td class="px-4 py-3 font-bold text-gray-800">{{ item.nm_siswa }}</td>
              <td class="px-4 py-3 text-center font-bold" :class="item.jk == 1 ? 'text-blue-600' : 'text-pink-500'">{{ item.jk == 1 ? 'L' : 'P' }}</td>
              <td class="px-4 py-3">{{ item.nama_kelas_asal }}</td>
              <td class="px-4 py-3 text-center font-bold bg-indigo-50 border-l border-r border-indigo-100 text-indigo-700">{{ item.nama_tps }}</td>
              <td class="px-4 py-3 text-center">
                <span v-if="item.pilihan" class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800 flex items-center justify-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                  Sudah
                </span>
                <span v-else class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-600">Belum</span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="submitSingleDelete(item.id, item.pilihan)" class="text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded text-xs">Keluarkan</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="p-4 border-t flex justify-between items-center text-sm text-gray-600 bg-white">
        <div>
          Menampilkan {{ items.length }} dari {{ pagination.total }} data
        </div>
        <div class="flex gap-2">
          <button @click="fetchDpt(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Prev</button>
          <span class="px-3 py-1 font-semibold text-gray-800 border rounded bg-gray-50">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
          <button @click="fetchDpt(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50">Next</button>
        </div>
      </div>
    </BaseCard>

    <!-- Modal Tambah DPT -->
    <BaseModal v-model="isAddModalOpen" title="Tambah Siswa ke DPT" max-width="5xl">
      <div class="flex gap-4">
        
        <!-- Panel Kiri: List Siswa Belum DPT -->
        <div class="w-3/5 border rounded-lg overflow-hidden flex flex-col h-[600px]">
          <!-- Filter Internal Modal -->
          <div class="bg-gray-100 p-3 border-b flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-sm">Daftar Siswa Belum DPT ({{ filteredSiswaBelumDpt.length }})</h4>
            <input v-model="searchSiswaBelumDpt" type="text" placeholder="Cari nama nisn kelas..." class="px-3 py-1 text-sm border rounded w-48 focus:ring-indigo-500">
          </div>
          
          <div class="overflow-y-auto flex-1 bg-white">
            <table class="w-full text-sm text-left relative">
              <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b sticky top-0 z-10 shadow-sm">
                <tr>
                  <th class="px-3 py-2 w-10 text-center">
                    <input type="checkbox" @change="toggleAllSiswaModal" :checked="isAllSiswaModalSelected" class="rounded border-gray-300">
                  </th>
                  <th class="px-3 py-2">NISN</th>
                  <th class="px-3 py-2">Nama</th>
                  <th class="px-3 py-2">Kelas</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoadingSiswa" class="border-b">
                  <td colspan="4" class="px-3 py-6 text-center text-gray-500">Memuat data siswa...</td>
                </tr>
                <tr v-else-if="filteredSiswaBelumDpt.length === 0" class="border-b">
                  <td colspan="4" class="px-3 py-6 text-center text-gray-500 bg-yellow-50">Semua siswa aktif sudah masuk ke DPT.</td>
                </tr>
                <tr v-else v-for="siswa in filteredSiswaBelumDpt" :key="siswa.nisn" class="border-b hover:bg-indigo-50 cursor-pointer" @click="toggleRowSelection(siswa.nisn)">
                  <td class="px-3 py-2 text-center" @click.stop>
                    <input type="checkbox" :value="siswa.nisn" v-model="selectedNisnToDpt" class="rounded border-gray-300">
                  </td>
                  <td class="px-3 py-2 text-xs font-mono">{{ siswa.nisn }}</td>
                  <td class="px-3 py-2 font-bold">{{ siswa.nm_siswa }}</td>
                  <td class="px-3 py-2 text-xs">{{ siswa.kelas }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Panel Kanan: Setting Target TPS Submit -->
        <div class="w-2/5 flex flex-col gap-4">
          <BaseCard class="bg-indigo-50 border-indigo-200">
            <h4 class="font-bold text-indigo-800 mb-2 border-b border-indigo-200 pb-2">Target Alokasi TPS</h4>
            <p class="text-xs text-indigo-600 mb-4">Pilih ke TPS / Kelas mana siswa yang dicentang akan didaftarkan sebagai pemilih:</p>
            
            <div class="mb-4">
              <label class="block text-sm font-bold text-gray-700 mb-1">Pilih TPS</label>
              <select v-model="targetTpsId" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" required>
                <option value="" disabled>-- Wajib Pilih TPS --</option>
                <option v-for="tps in listTps" :key="tps.kd_kelas" :value="tps.kd_kelas">{{ tps.nm_kelas }}</option>
              </select>
            </div>

            <div class="bg-white p-4 rounded border text-center">
              <span class="block text-3xl font-black text-indigo-600 mb-1">{{ selectedNisnToDpt.length }}</span>
              <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Siswa Terpilih</span>
            </div>

            <div class="mt-6 flex justify-end gap-2">
              <BaseButton type="button" variant="secondary" @click="isAddModalOpen = false" class="w-1/3">Batal</BaseButton>
              <BaseButton type="button" variant="primary" @click="submitBulkInsert" :loading="isSubmitting" :disabled="selectedNisnToDpt.length === 0 || !targetTpsId" class="w-2/3">Simpan ke DPT</BaseButton>
            </div>
          </BaseCard>
        </div>

      </div>
    </BaseModal>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseModal from '../../components/BaseModal.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const items = ref([]);
const isLoading = ref(false);
const searchQuery = ref('');
const filterTps = ref('');
const listTps = ref([]);
const pagination = ref({ current_page: 1, last_page: 1, total: 0, per_page: 30 });
const selectedIds = ref([]); 

const isAddModalOpen = ref(false);
const isLoadingSiswa = ref(false);
const siswaBelumDpt = ref([]);
const searchSiswaBelumDpt = ref('');
const selectedNisnToDpt = ref([]);
const targetTpsId = ref('');
const isSubmitting = ref(false);

const isAllSelected = computed(() => {
  return items.value.length > 0 && selectedIds.value.length === items.value.length;
});

const filteredSiswaBelumDpt = computed(() => {
  if (!searchSiswaBelumDpt.value) return siswaBelumDpt.value;
  const s = searchSiswaBelumDpt.value.toLowerCase();
  return siswaBelumDpt.value.filter(item => 
    item.nm_siswa.toLowerCase().includes(s) || 
    item.nisn.toLowerCase().includes(s) || 
    item.kelas.toLowerCase().includes(s)
  );
});

const isAllSiswaModalSelected = computed(() => {
  const visible = filteredSiswaBelumDpt.value;
  return visible.length > 0 && visible.every(s => selectedNisnToDpt.value.includes(s.nisn));
});

onMounted(() => {
  fetchListTps();
  fetchDpt(1);
});

async function fetchListTps() {
  try {
    const res = await api.get('/admin-sekolah/dpt/tps-aktif');
    listTps.value = res.data.data;
  } catch (e) {
    toast.error('Gagal mengambil daftar TPS');
  }
}

async function fetchDpt(page = 1) {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin-sekolah/dpt?page=${page}&cari=${searchQuery.value}&tps_id=${filterTps.value}`);
    items.value = res.data.data.data;
    pagination.value = {
      current_page: res.data.data.current_page,
      last_page: res.data.data.last_page,
      total: res.data.data.total,
      per_page: res.data.data.per_page
    };
    selectedIds.value = []; 
  } catch (error) {
    toast.error('Gagal memuat Data DPT');
  } finally {
    isLoading.value = false;
  }
}

function toggleAll(event) {
  if (event.target.checked) {
    selectedIds.value = items.value.map(item => item.id);
  } else {
    selectedIds.value = [];
  }
}

function toggleAllSiswaModal(event) {
  const visibleNisn = filteredSiswaBelumDpt.value.map(s => s.nisn);
  if (event.target.checked) {
    selectedNisnToDpt.value = [...new Set([...selectedNisnToDpt.value, ...visibleNisn])];
  } else {
    selectedNisnToDpt.value = selectedNisnToDpt.value.filter(nisn => !visibleNisn.includes(nisn));
  }
}

function toggleRowSelection(nisn) {
  const index = selectedNisnToDpt.value.indexOf(nisn);
  if (index === -1) {
    selectedNisnToDpt.value.push(nisn);
  } else {
    selectedNisnToDpt.value.splice(index, 1);
  }
}

async function openAddDptModal() {
  targetTpsId.value = filterTps.value || ''; 
  selectedNisnToDpt.value = [];
  searchSiswaBelumDpt.value = '';
  isAddModalOpen.value = true;
  isLoadingSiswa.value = true;
  
  try {
    const res = await api.get('/admin-sekolah/dpt/siswa-belum-dpt');
    siswaBelumDpt.value = res.data.data;
  } catch (e) {
    toast.error('Gagal memuat daftar siswa belum DPT');
  } finally {
    isLoadingSiswa.value = false;
  }
}

async function submitBulkInsert() {
  if (selectedNisnToDpt.value.length === 0) {
    return toast.error('Pilih minimal 1 siswa');
  }
  if (!targetTpsId.value) {
    return toast.error('Pilih TPS tujuan');
  }

  isSubmitting.value = true;
  try {
    const res = await api.post('/admin-sekolah/dpt/bulk-insert', {
      id_tps: targetTpsId.value,
      siswa_nisn: selectedNisnToDpt.value
    });
    
    toast.success(res.data.message);
    isAddModalOpen.value = false;
    fetchDpt(1); 
  } catch (e) {
    toast.error('Terjadi kesalahan saat memasukkan data ke DPT');
  } finally {
    isSubmitting.value = false;
  }
}

async function submitBulkDelete() {
  if (selectedIds.value.length === 0) return;
  if (!confirm(`Yakin ingin mengeluarkan ${selectedIds.value.length} siswa ini dari DPT?\n(Mereka akan kembali ke daftar "Belum DPT")`)) return;

  try {
    const res = await api.post('/admin-sekolah/dpt/bulk-delete', {
      ids: selectedIds.value
    });
    toast.success(res.data.message);
    fetchDpt(pagination.value.current_page);
  } catch (e) {
    toast.error('Gagal menghapus data DPT');
  }
}

async function submitSingleDelete(id, isSudahMemilih) {
  if (isSudahMemilih) {
    return toast.error('Siswa ini sudah menggunakan hak pilihnya (Sudah Mencoblos). Tidak bisa dikeluarkan dari DPT!');
  }
  if (!confirm('Keluarkan siswa ini dari TPS?')) return;

  try {
    const res = await api.post('/admin-sekolah/dpt/bulk-delete', {
      ids: [id]
    });
    toast.success('Berhasil dikeluarkan dari DPT');
    fetchDpt(pagination.value.current_page);
  } catch (e) {
    toast.error('Gagal menghapus');
  }
}
</script>
