<template>
  <div class="space-y-6 max-w-7xl mx-auto pb-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-2">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Super Admin</h1>
        <p class="text-sm text-gray-500 mt-1">Pemantauan Statistik e-Pemilos Kabupaten Kulon Progo Tahun {{ tahunAktif }}</p>
      </div>
      <div class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Tahun Ajaran: {{ tahunAktif }}
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
      <svg class="animate-spin h-10 w-10 mx-auto text-indigo-600 mb-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
      <p class="text-gray-500 font-medium">Memuat statistik daerah...</p>
    </div>

    <template v-else-if="stats">
      <!-- 4 Stat Cards Utama -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 group hover:border-blue-200 transition-colors">
          <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-colors">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Total Sekolah</p>
            <p class="text-3xl font-black text-gray-900 mt-0.5">{{ stats.totals.sekolah.toLocaleString('id-ID') }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 group hover:border-indigo-200 transition-colors">
          <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Siswa Terdata</p>
            <p class="text-3xl font-black text-gray-900 mt-0.5">{{ stats.totals.siswa_aktif.toLocaleString('id-ID') }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 group hover:border-purple-200 transition-colors">
          <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-purple-600 group-hover:text-white transition-colors">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Kandidat Paslon</p>
            <p class="text-3xl font-black text-gray-900 mt-0.5">{{ stats.totals.kandidat.toLocaleString('id-ID') }}</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4 group hover:border-emerald-200 transition-colors">
          <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Bilik TPS</p>
            <p class="text-3xl font-black text-gray-900 mt-0.5">{{ stats.totals.tps.toLocaleString('id-ID') }}</p>
          </div>
        </div>

      </div>

      <!-- Section Dua: Progress Partisipasi -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        
        <!-- Partisipasi (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Tingkat Partisipasi Daerah
          </h3>
          
          <div class="flex flex-col md:flex-row gap-8 items-center">
            <!-- Lingkaran Persentase -->
            <div class="relative shrink-0">
              <svg class="w-32 h-32 transform -rotate-90">
                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="12" fill="transparent" class="text-gray-100" />
                <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="12" fill="transparent" :stroke-dasharray="2 * Math.PI * 56" :stroke-dashoffset="(2 * Math.PI * 56) * (1 - stats.partisipasi.persentase / 100)" class="text-indigo-600 transition-all duration-1000" />
              </svg>
              <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-black text-gray-900">{{ stats.partisipasi.persentase }}%</span>
              </div>
            </div>
            
            <!-- Detail Angka -->
            <div class="flex-1 w-full space-y-4">
              <div>
                <div class="flex justify-between mb-1">
                  <span class="text-sm font-semibold text-gray-600">Total DPT Valid</span>
                  <span class="text-sm font-bold text-gray-900">{{ stats.partisipasi.total_dpt.toLocaleString('id-ID') }} Suara</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                  <div class="bg-gray-400 h-2.5 rounded-full" style="width: 100%"></div>
                </div>
              </div>
              
              <div>
                <div class="flex justify-between mb-1">
                  <span class="text-sm font-semibold text-green-600">Sudah Mencoblos</span>
                  <span class="text-sm font-bold text-green-700">{{ stats.partisipasi.sudah_memilih.toLocaleString('id-ID') }} Suara</span>
                </div>
                <div class="w-full bg-green-100 rounded-full h-2.5">
                  <div class="bg-green-500 h-2.5 rounded-full" :style="`width: ${stats.partisipasi.persentase}%`"></div>
                </div>
              </div>

              <div>
                <div class="flex justify-between mb-1">
                  <span class="text-sm font-semibold text-red-500">Belum Memilih</span>
                  <span class="text-sm font-bold text-red-600">{{ stats.partisipasi.belum_memilih.toLocaleString('id-ID') }} Suara</span>
                </div>
                <div class="w-full bg-red-100 rounded-full h-2.5">
                  <div class="bg-red-400 h-2.5 rounded-full" :style="`width: ${100 - stats.partisipasi.persentase}%`"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Jadwal Sedang Berjalan (1 Kolom) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
          <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            Jadwal Global Berlangsung
          </h3>
          
          <div v-if="stats.jadwal_berlangsung.length === 0" class="text-center py-6 text-gray-500">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm">Tidak ada jadwal pemilihan global yang sedang berlangsung pada hari ini.</p>
          </div>
          
          <div v-else class="space-y-4">
            <div v-for="j in stats.jadwal_berlangsung" :key="j.id" class="p-4 rounded-xl border border-green-200 bg-green-50 relative overflow-hidden">
              <div class="absolute top-0 right-0 p-2 text-green-200 opacity-50 pointer-events-none">
                <svg class="w-12 h-12 transform rotate-12" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"></path></svg>
              </div>
              <h4 class="font-bold text-green-900">{{ j.jenis }}</h4>
              <p class="text-sm font-semibold text-green-700 mt-1 uppercase tracking-wide bg-white px-2 py-0.5 rounded inline-block">Jenjang {{ j.jenjang }}</p>
              
              <div class="mt-4 flex flex-col gap-1 text-sm text-green-800">
                <div class="flex justify-between border-b border-green-200 border-opacity-50 pb-1">
                  <span class="opacity-80">Dimulai</span>
                  <span class="font-bold">{{ formatDate(j.waktu_mulai) }}</span>
                </div>
                <div class="flex justify-between pt-1">
                  <span class="opacity-80">Berakhir</span>
                  <span class="font-bold">{{ formatDate(j.waktu_selesai) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();
const loading = ref(true);
const stats = ref(null);
const tahunAktif = ref(new Date().getFullYear()); // Fallback visual, backend handle by env

onMounted(async () => {
  try {
    const res = await api.get('/admin/dashboard/stats');
    stats.value = res.data.data;
  } catch (e) {
    console.error('Gagal memuat dashboard:', e);
    toast.error('Gagal mengambil ringkasan statistik dari server');
  } finally {
    loading.value = false;
  }
});

function formatDate(dateString) {
  if (!dateString) return '-';
  const options = { day: '2-digit', month: 'short', year: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
}
</script>
