<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <svg class="animate-spin h-8 w-8 mx-auto text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
      <p class="text-gray-500 mt-2">Memuat data...</p>
    </div>

    <template v-if="!loading && stats">
      <!-- Stat Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Total Aduan</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totals.total_aduan }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg></div>
          </div>
          <div v-if="stats.new_submissions.aduan_baru > 0" class="mt-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ stats.new_submissions.aduan_baru }} baru</span>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Total Tamu Setwan</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totals.total_tamu_setwan }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg></div>
          </div>
          <div v-if="stats.new_submissions.setwan_baru > 0" class="mt-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ stats.new_submissions.setwan_baru }} baru</span>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Total Tamu DPRD</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totals.total_tamu_dprd }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
          </div>
          <div v-if="stats.new_submissions.dprd_baru > 0" class="mt-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ stats.new_submissions.dprd_baru }} baru</span>
          </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">Total Audiensi</p>
              <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.totals.total_audiensi }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center"><svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
          </div>
          <div v-if="stats.new_submissions.audiensi_baru > 0" class="mt-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ stats.new_submissions.audiensi_baru }} baru</span>
          </div>
        </div>
      </div>

      <!-- Submission baru card -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <h3 class="font-semibold text-gray-800 mb-4">Ringkasan Pengajuan Baru</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
              <span class="text-sm text-gray-600">Aduan & Aspirasi</span>
              <span class="text-sm font-semibold text-red-600">{{ stats.new_submissions.aduan_baru }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
              <span class="text-sm text-gray-600">Tamu Setwan</span>
              <span class="text-sm font-semibold text-blue-600">{{ stats.new_submissions.setwan_baru }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
              <span class="text-sm text-gray-600">Tamu DPRD</span>
              <span class="text-sm font-semibold text-emerald-600">{{ stats.new_submissions.dprd_baru }}</span>
            </div>
            <div class="flex items-center justify-between py-2">
              <span class="text-sm text-gray-600">Permohonan Audiensi</span>
              <span class="text-sm font-semibold text-amber-600">{{ stats.new_submissions.audiensi_baru }}</span>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <h3 class="font-semibold text-gray-800 mb-4">Tren Pengajuan (6 Bulan)</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
              <span class="text-sm text-gray-600">Total Aduan & Aspirasi</span>
              <span class="text-sm font-semibold text-gray-800">{{ stats.totals.total_aduan }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
              <span class="text-sm text-gray-600">Total Kunjungan (Setwan + DPRD)</span>
              <span class="text-sm font-semibold text-gray-800">{{ stats.totals.total_tamu_setwan + stats.totals.total_tamu_dprd }}</span>
            </div>
            <div class="flex items-center justify-between py-2">
              <span class="text-sm text-gray-600">Total Audiensi</span>
              <span class="text-sm font-semibold text-gray-800">{{ stats.totals.total_audiensi }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Akses Cepat</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <router-link to="/admin/aduan-aspirasi" class="text-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
            <p class="text-sm font-medium text-red-700">Aduan & Aspirasi</p>
          </router-link>
          <router-link to="/admin/tamu-setwan" class="text-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            <p class="text-sm font-medium text-blue-700">Tamu Setwan</p>
          </router-link>
          <router-link to="/admin/tamu-dprd" class="text-center p-4 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
            <p class="text-sm font-medium text-emerald-700">Tamu DPRD</p>
          </router-link>
          <router-link to="/admin/permohonan-audiensi" class="text-center p-4 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
            <p class="text-sm font-medium text-amber-700">Audiensi</p>
          </router-link>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';

const loading = ref(true);
const stats = ref(null);

onMounted(async () => {
  try {
    const res = await api.get('/admin/dashboard/stats');
    stats.value = res.data.data;
  } catch (e) {
    console.error('Gagal memuat dashboard:', e);
  } finally {
    loading.value = false;
  }
});
</script>
