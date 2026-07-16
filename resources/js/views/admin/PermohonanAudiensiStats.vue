<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <button @click="$router.push('/admin/permohonan-audiensi')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </button>
        <h2 class="text-xl font-bold text-gray-800">Statistik Permohonan Audiensi</h2>
        <p class="text-sm text-gray-500">Ringkasan data permohonan audiensi tahun ini.</p>
      </div>
      
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600 font-medium mr-2">Tahun:</span>
        <select v-model="selectedYear" @change="loadStats" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
          <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <div v-else class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Statistik Rombongan -->
        <BaseCard class="h-full flex flex-col">
          <template #header>
            <h2 class="text-lg font-bold text-gray-800">Jumlah Rombongan Kunjungan ({{ selectedYear }})</h2>
            <p class="text-xs text-gray-500 font-normal">Total Rombongan: <span class="font-bold text-indigo-600 text-sm">{{ totalRombongan }}</span> rombongan</p>
          </template>
          <div class="h-64 flex-1">
            <Bar :data="chartDataRombongan" :options="chartOptions" />
          </div>
        </BaseCard>

        <!-- Statistik Peserta -->
        <BaseCard class="h-full flex flex-col">
          <template #header>
            <h2 class="text-lg font-bold text-gray-800">Jumlah Peserta Kunjungan ({{ selectedYear }})</h2>
            <p class="text-xs text-gray-500 font-normal">Total Peserta: <span class="font-bold text-emerald-600 text-sm">{{ totalPeserta }}</span> orang</p>
          </template>
          <div class="h-64 flex-1">
            <Bar :data="chartDataPeserta" :options="chartOptions" />
          </div>
        </BaseCard>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import BaseCard from '@/components/BaseCard.vue';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';
import { Bar } from 'vue-chartjs';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const router = useRouter();
const loading = ref(true);

const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
// Generate available years (dari current year mundur 5 tahun)
const availableYears = Array.from({ length: 6 }, (_, i) => currentYear - i);

const statsData = ref({
  rombongan: [],
  peserta: []
});

const totalRombongan = computed(() => {
  return statsData.value.rombongan.reduce((acc, curr) => acc + curr, 0);
});

const totalPeserta = computed(() => {
  return statsData.value.peserta.reduce((acc, curr) => acc + curr, 0);
});

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

const chartDataRombongan = computed(() => ({
  labels: months,
  datasets: [
    {
      label: 'Jumlah Rombongan',
      backgroundColor: '#6366f1', // indigo-500
      data: statsData.value.rombongan
    }
  ]
}));

const chartDataPeserta = computed(() => ({
  labels: months,
  datasets: [
    {
      label: 'Jumlah Peserta',
      backgroundColor: '#10b981', // emerald-500
      data: statsData.value.peserta
    }
  ]
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        precision: 0
      }
    }
  }
};

async function loadStats() {
  loading.value = true;
  try {
    const res = await api.get('/admin/permohonan-audiensi/stats', {
      params: { year: selectedYear.value }
    });
    
    if (res.data.success) {
      statsData.value.rombongan = res.data.data.rombongan;
      statsData.value.peserta = res.data.data.peserta;
    }
  } catch (error) {
    console.error('Error loading stats:', error);
    alert('Gagal memuat data statistik');
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadStats();
});
</script>