<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Permohonan Audiensi</h1>
      <div class="flex items-center gap-2">
        <input v-model="search" type="text" placeholder="Cari..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" />
        <select v-model="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2"><option value="">Semua</option><option value="baru">Baru</option><option value="diproses">Diproses</option><option value="disetujui">Disetujui</option><option value="ditolak">Ditolak</option><option value="selesai">Selesai</option></select>
        <div class="flex gap-2">
          <button @click="$router.push('/admin/permohonan-audiensi/stats')" class="px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 flex items-center gap-2 border border-indigo-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            Statistik
          </button>
          <button @click="exportData" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>Ekspor</button>
        </div>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full"><thead class="bg-gray-50 border-b border-gray-200"><tr><th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">#</th><th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Nama</th><th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Kelompok</th><th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">HP</th><th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Peserta</th><th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Status</th><th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase">Aksi</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="(item, i) in items" :key="item.id" class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-500">{{ meta.from + i }}</td>
            <td class="px-4 py-3"><p class="text-sm font-medium text-gray-800">{{ item.nama }}</p></td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ item.nama_instansi_kelompok_paguyuban_komunitas }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ item.nomor_hp_narahubung }}</td>
            <td class="px-4 py-3 text-sm text-gray-600">{{ item.jumlah_peserta }} org</td>
            <td class="px-4 py-3"><StatusBadge :status="item.status" :label="item.status_label" /></td>
            <td class="px-4 py-3 text-right"><router-link :to="`/admin/permohonan-audiensi/${item.id}`" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Detail</router-link></td>
          </tr>
          <tr v-if="!items.length"><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">Belum ada data.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script setup>
import { ref, watch, onMounted } from 'vue';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge.vue';
const items = ref([]), meta = ref({ from: 0 }), search = ref(''), filterStatus = ref('');
async function loadData() {
  try { const params = { page: 1, per_page: 50, search: search.value }; if (filterStatus.value) params.status = filterStatus.value; const res = await api.get('/admin/permohonan-audiensi', { params }); items.value = res.data.data; meta.value = res.data.meta; } catch (e) { console.error(e); }
}
watch([search, filterStatus], loadData);
async function exportData() { const params = {}; if (filterStatus.value) params.status = filterStatus.value; const res = await api.get('/admin/permohonan-audiensi/export', { params }); const blob = new Blob([JSON.stringify(res.data.data, null, 2)], { type: 'application/json' }); const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'audiensi-export.json'; a.click(); }
onMounted(loadData);
</script>
