<template>
  <div class="space-y-6 max-w-5xl mx-auto py-6">
    
    <!-- Hero / Banner Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 overflow-hidden relative">
      <div class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-blue-50 opacity-50 pointer-events-none"></div>
      
      <div class="p-8 md:p-10 relative z-10">
        <div class="flex items-center gap-4 mb-6">
          <div class="bg-indigo-600 rounded-xl p-3 shadow-md">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard Admin <span class="text-indigo-600">{{ namaSekolah }}</span></h1>
            <p class="text-indigo-600 font-semibold mt-1">Sistem e-Pemilos Kabupaten Kulon Progo</p>
          </div>
        </div>

        <div class="prose prose-indigo max-w-none text-gray-600 text-justify leading-relaxed">
          <p>
            <strong>e-Pemilos</strong> adalah sebuah aplikasi yang diinisiasi dan didesain oleh <strong>Komisi Pemilihan Umum Kabupaten Kulon Progo</strong> sejak Tahun 2020 yang disesuaikan dengan format dan tahapan penyelenggaraan Pemilu. Aplikasi ini dibuat oleh <strong>Dinas Komunikasi dan Informatika Kabupaten Kulon Progo</strong>, dan didukung oleh Badan Kesatuan Bangsa dan Politik Kabupaten Kulon Progo, Dinas Pendidikan Kabupaten Kulon Progo, Balai Pendidikan Menengah Kabupaten Kulon Progo, dan Kantor Kementerian Agama Kabupaten Kulon Progo.
          </p>
          <p>
            Aplikasi ini digunakan untuk <strong>Pemilihan Ketua OSIS</strong> tingkat menengah se-Kabupaten Kulon Progo secara daring, sebagai wadah pendidikan pemilih sesuai asas pemilu yang dilaksanakan secara efektif, murah, aman, cepat, serta ramah lingkungan.
          </p>
        </div>
      </div>
    </div>

    <!-- Jadwal Pelaksanaan Section -->
    <div class="mt-10">
      <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-3">
        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Jadwal Pelaksanaan Pemilos
      </h2>

      <div v-if="isLoading" class="py-10 text-center text-gray-500 animate-pulse">
        Memeriksa jadwal pemilihan...
      </div>
      
      <div v-else-if="jadwal.length === 0" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-yellow-700">Belum ada pengaturan jadwal pemilihan dari Super Admin untuk sekolah / jenjang Anda.</p>
          </div>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <div v-for="j in jadwal" :key="j.id" class="bg-white rounded-xl border p-5 shadow-sm relative overflow-hidden flex flex-col"
             :class="{
               'border-green-400 ring-1 ring-green-400 bg-green-50': j.status === 'aktif',
               'border-gray-200': j.status !== 'aktif'
             }">
             
          <!-- Pita Aktif -->
          <div v-if="j.status === 'aktif'" class="absolute top-0 right-0 bg-green-500 text-white text-[10px] font-bold px-3 py-1 uppercase tracking-wider rounded-bl-lg shadow-sm">
            Sedang Berlangsung
          </div>
          <div v-else-if="j.status === 'selesai'" class="absolute top-0 right-0 bg-gray-500 text-white text-[10px] font-bold px-3 py-1 uppercase tracking-wider rounded-bl-lg">
            Selesai
          </div>
          <div v-else class="absolute top-0 right-0 bg-blue-500 text-white text-[10px] font-bold px-3 py-1 uppercase tracking-wider rounded-bl-lg">
            Akan Datang
          </div>

          <h3 class="font-bold text-gray-800 text-lg mb-4 mt-2 pr-20">{{ j.jenis }}</h3>
          
          <div class="mt-auto space-y-3">
            <div class="flex items-start gap-3">
              <div class="mt-0.5 bg-gray-100 p-1.5 rounded text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 11V9a2 2 0 00-2-2m2 4v4a2 2 0 104 0v-1m-4-3H9m2 0h4m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Mulai</p>
                <p class="text-sm font-medium text-gray-900">{{ formatDate(j.waktu_mulai) }}</p>
              </div>
            </div>
            
            <div class="flex items-start gap-3">
              <div class="mt-0.5 bg-gray-100 p-1.5 rounded text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              </div>
              <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Selesai</p>
                <p class="text-sm font-medium text-gray-900">{{ formatDate(j.waktu_selesai) }}</p>
              </div>
            </div>
          </div>
          
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const namaSekolah = ref('');
const jadwal = ref([]);
const isLoading = ref(true);

onMounted(() => {
  fetchDashboardInfo();
});

function formatDate(dateString) {
  if (!dateString) return '-';
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
}

async function fetchDashboardInfo() {
  isLoading.value = true;
  try {
    const res = await api.get('/admin-sekolah/dashboard');
    namaSekolah.value = res.data.data.sekolah;
    jadwal.value = res.data.data.jadwal;
  } catch (error) {
    toast.error('Gagal mengambil info dashboard');
  } finally {
    isLoading.value = false;
  }
}
</script>
