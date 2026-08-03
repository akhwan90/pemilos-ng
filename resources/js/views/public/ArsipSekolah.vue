<template>
  <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
    <!-- Navbar / Header (Reusable Component) -->
    <PublicHeader />

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
      <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
          <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Arsip Data e-Pemilos</h2>
          <p class="text-gray-500 mt-2">Data historis pelaksanaan pemilihan berdasarkan tahun.</p>
        </div>

        <div class="w-full md:w-48">
          <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tahun</label>
          <select 
            v-model="selectedYear"
            @change="fetchData(1)"
            class="w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg shadow-sm border"
          >
            <option v-for="year in availableYears" :key="year" :value="year">Tahun {{ year }}</option>
          </select>
        </div>
      </div>

      <!-- Konten Arsip -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
        
        <!-- Loading State -->
        <div v-if="loading" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
            <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Tabel Arsip -->
        <div class="overflow-x-auto min-h-[300px]">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Sekolah</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total DPT</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sudah Memilih</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Partisipasi</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              
              <tr v-if="!loading && arsipData.length === 0">
                  <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                      Tidak ada data arsip untuk tahun {{ selectedYear }}
                  </td>
              </tr>

              <tr v-for="(item, index) in arsipData" :key="item.npsn" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    {{ (pagination.currentPage - 1) * pagination.perPage + index + 1 }}
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-bold text-gray-900">{{ item.nama_sekolah }}</div>
                  <div class="text-xs text-gray-500 font-mono">NPSN: {{ item.npsn }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">{{ formatNumber(item.jml_dpt) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">{{ formatNumber(item.jml_dpt_memilih) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[120px]">
                      <div 
                        :class="getProgressBarClass(item.persentase)" 
                        class="h-2.5 rounded-full" 
                        :style="{ width: `${Math.min(100, Math.max(0, item.persentase || 0))}%` }">
                      </div>
                    </div>
                    <span :class="getTextClass(item.persentase)" class="text-sm font-bold">
                        {{ formatPercent(item.persentase) }}%
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                  <button @click="openModalHasil(item)" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition-colors">
                    Lihat Hasil
                  </button>
                </td>
              </tr>

            </tbody>
            <!-- Baris Total -->
            <tfoot v-if="arsipData.length > 0" class="bg-gray-100 font-bold border-t-2 border-gray-200">
              <tr>
                <td colspan="2" class="px-6 py-4 text-center text-gray-700">TOTAL</td>
                <td class="px-6 py-4 text-center text-gray-900">{{ formatNumber(totalDpt) }}</td>
                <td class="px-6 py-4 text-center text-gray-900">{{ formatNumber(totalDptMemilih) }}</td>
                <td class="px-6 py-4">
                  <span class="text-sm text-gray-900">{{ formatPercent(rataRataPersentase) }} %</span>
                </td>
                <td class="px-6 py-4 text-center text-gray-900">
                  {{ formatNumber(totalCalon) }} Calon
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
          <span class="text-sm text-gray-500">
              Menampilkan {{ arsipData.length > 0 ? (pagination.currentPage - 1) * pagination.perPage + 1 : 0 }} 
              - {{ Math.min(pagination.currentPage * pagination.perPage, pagination.total) }} 
              dari {{ pagination.total }} Sekolah
          </span>
          <div class="flex gap-2 text-sm">
            <button 
                @click="fetchData(pagination.currentPage - 1)" 
                :disabled="pagination.currentPage === 1"
                class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100">
                Sebelumnya
            </button>
            <button 
                @click="fetchData(pagination.currentPage + 1)" 
                :disabled="pagination.currentPage * pagination.perPage >= pagination.total"
                class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-100">
                Selanjutnya
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal Hasil Arsip -->
    <BaseModal v-model="isModalOpen" :title="`Hasil Pemilihan: ${selectedSekolah?.nama_sekolah || ''}`">
      <div v-if="loadingHasil" class="flex justify-center items-center py-12">
        <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      
      <div v-else-if="hasilData">
        <div class="bg-gray-50 p-4 rounded-lg mb-6 text-sm flex flex-col md:flex-row gap-4 justify-between">
          <div class="flex items-center gap-2 mb-4 md:mb-0">
            <img v-if="hasilData.sekolah?.logo" :src="hasilData.sekolah.logo" class="w-10 h-10 object-contain bg-white rounded-md border" />
            <span class="font-bold text-gray-800">{{ hasilData.sekolah?.nama_sekolah }}</span>
          </div>
          <div class="flex gap-4">
              <div>
                <p class="text-gray-500 mb-1">NPSN</p>
                <p class="font-bold text-gray-900">{{ hasilData.sekolah.npsn }}</p>
              </div>
              <div>
                <p class="text-gray-500 mb-1">Total DPT</p>
                <p class="font-bold text-gray-900">{{ formatNumber(selectedSekolah?.jml_dpt) }}</p>
              </div>
              <div>
                <p class="text-gray-500 mb-1">Tahun</p>
                <p class="font-bold text-gray-900">{{ selectedYear }}</p>
              </div>
          </div>
        </div>

        <div class="space-y-4">
          <h4 class="font-bold text-gray-900 border-b pb-2">Perolehan Suara Kandidat</h4>
          
          <div v-if="hasilData.hasil.length === 0" class="text-center py-6 text-gray-500">
            Tidak ada data perolehan suara.
          </div>
          
          <div v-for="kandidat in hasilData.hasil" :key="kandidat.no" class="bg-white border rounded-lg p-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-4">
              <div class="bg-blue-100 text-blue-800 font-bold w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                {{ kandidat.no }}
              </div>
              <div v-if="kandidat.photo" class="w-12 h-12 rounded bg-gray-100 overflow-hidden flex-shrink-0">
                  <img 
                      :src="getPhotoUrl(kandidat.photo)" 
                      @error="handleImageError"
                      class="w-full h-full object-cover" 
                      alt="Foto Kandidat" 
                  />
              </div>
              <div v-else class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
              </div>
              <div>
                <p class="font-bold text-gray-900">{{ kandidat.nama }}</p>
                <div class="mt-1 flex items-center gap-2">
                  <div class="w-32 bg-gray-200 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full" :style="{ width: `${calculatePercentage(kandidat.jml_pemilih, selectedSekolah?.jml_dpt_memilih)}%` }"></div>
                  </div>
                  <span class="text-xs text-gray-500">{{ calculatePercentage(kandidat.jml_pemilih, selectedSekolah?.jml_dpt_memilih) }}%</span>
                </div>
              </div>
            </div>
            <div class="text-right flex-shrink-0 ml-4">
              <p class="text-2xl font-bold text-gray-900">{{ formatNumber(kandidat.jml_pemilih) }}</p>
              <p class="text-xs text-gray-500 uppercase">Suara</p>
            </div>
          </div>
          
          <!-- Tidak Menggunakan Hak Pilih -->
          <div v-if="hasilData.hasil.length > 0" class="bg-red-50 border border-red-100 rounded-lg p-4 flex items-center justify-between shadow-sm mt-4">
            <div class="font-bold text-red-600 ml-[4.5rem]">TIDAK MENGGUNAKAN HAK PILIH</div>
            <div class="text-right flex-shrink-0 ml-4">
              <p class="text-xl font-bold text-red-600">{{ formatNumber(jumlahTidakMemilih) }}</p>
              <p class="text-xs text-red-400 uppercase">Pemilih</p>
            </div>
          </div>
          
          <!-- Total DPT di Modal (untuk menyesuaikan Arsip.php hasil) -->
          <div v-if="hasilData.hasil.length > 0" class="bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-center justify-between shadow-sm mt-4">
            <div class="font-bold text-blue-600 ml-[4.5rem]">TOTAL DPT</div>
            <div class="text-right flex-shrink-0 ml-4">
              <p class="text-xl font-bold text-blue-600">{{ formatNumber(totalDptModal) }}</p>
            </div>
          </div>
          
          <!-- Suara Sah (opsional, untuk memperjelas dari mana asalnya jumlah_memilih) -->
          <div v-if="hasilData.hasil.length > 0" class="bg-emerald-50 border border-emerald-100 rounded-lg p-4 flex items-center justify-between shadow-sm mt-4">
            <div class="font-bold text-emerald-600 ml-[4.5rem]">TOTAL SUARA SAH MASUK</div>
            <div class="text-right flex-shrink-0 ml-4">
              <p class="text-xl font-bold text-emerald-600">{{ formatNumber(totalSuaraMasuk) }}</p>
            </div>
          </div>

        </div>
      </div>
    </BaseModal>

    <!-- Footer (Reusable Component) -->
    <PublicFooter />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import PublicHeader from '../../components/public/PublicHeader.vue';
import PublicFooter from '../../components/public/PublicFooter.vue';
import BaseModal from '../../components/BaseModal.vue';

const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const loading = ref(false);
const arsipData = ref([]);

const pagination = ref({
    currentPage: 1,
    perPage: 50,
    total: 0
});

// Modal State
const isModalOpen = ref(false);
const loadingHasil = ref(false);
const selectedSekolah = ref(null);
const hasilData = ref(null);

// Simulasi pilihan tahun dari 10 tahun ke belakang
const availableYears = computed(() => {
  const years = [];
  for (let i = 0; i <= 10; i++) {
      years.push(currentYear - i);
  }
  return years;
});

const fetchData = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/public/arsip/${selectedYear.value}`, {
            params: {
                page: page,
                per_page: pagination.value.perPage
            }
        });
        
        if (response.data.success) {
            arsipData.value = response.data.data;
            pagination.value.total = response.data.total;
            pagination.value.currentPage = response.data.current_page;
        }
    } catch (error) {
        console.error('Error fetching arsip data:', error);
    } finally {
        loading.value = false;
    }
};

const openModalHasil = async (sekolah) => {
    selectedSekolah.value = sekolah;
    isModalOpen.value = true;
    loadingHasil.value = true;
    hasilData.value = null;

    try {
        const response = await axios.get(`/api/public/arsip/${selectedYear.value}/${sekolah.npsn}`);
        if (response.data.success) {
            hasilData.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching arsip hasil data:', error);
    } finally {
        loadingHasil.value = false;
    }
};

const formatNumber = (num) => {
    if (!num && num !== 0) return '-';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const formatPercent = (val) => {
    if (!val && val !== 0) return '0.00';
    let num = parseFloat(val); if(num > 100) { num = 100; } return num.toFixed(2);
};

const calculatePercentage = (val, total) => {
    if (!total || total == 0) return '0.00';
    let percent = (val / total) * 100; if(percent > 100) { percent = 100; } return percent.toFixed(2);
};

const getProgressBarClass = (percent) => {
    const val = parseFloat(percent || 0);
    if (val >= 90) return 'bg-emerald-500';
    if (val >= 70) return 'bg-blue-500';
    if (val >= 50) return 'bg-amber-500';
    return 'bg-red-500';
};

const getTextClass = (percent) => {
    const val = parseFloat(percent || 0);
    if (val >= 90) return 'text-emerald-600';
    if (val >= 70) return 'text-blue-600';
    if (val >= 50) return 'text-amber-600';
    return 'text-red-600';
};

const getPhotoUrl = (photoName) => {
    if (!photoName) return null;
    // Get full URL including subdomain if necessary, but API runs relative.
    const baseUrl = window.location.origin;
    if(import.meta.env.VITE_SUB_DIR){
       return `${baseUrl}/${import.meta.env.VITE_SUB_DIR}/upload/kandidat/${photoName}`;
    }
    return `${baseUrl}/upload/kandidat/${photoName}`;
};

const handleImageError = (e) => {
    e.target.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZTVlN2ViIiBzdHJva2Utd2lkdGg9IjIiPjxyZWN0IHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgZmlsbD0iI2YzZjRmNiIvPjxwYXRoIGQ9Ik0yMSAxNWwtNS01TDUgMjEiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjxjaXJjbGUgY3g9IjkiIGN5PSI5IiByPSIzIi8+PC9zdmc+';
};

// Computed Properties untuk Footer Tabel Arsip
const totalDpt = computed(() => {
    return arsipData.value.reduce((sum, item) => sum + (parseInt(item.jml_dpt) || 0), 0);
});

const totalDptMemilih = computed(() => {
    return arsipData.value.reduce((sum, item) => sum + (parseInt(item.jml_dpt_memilih) || 0), 0);
});

const totalCalon = computed(() => {
    return arsipData.value.reduce((sum, item) => sum + (parseInt(item.jml_calon) || 0), 0);
});

const rataRataPersentase = computed(() => {
    if (arsipData.value.length === 0) return 0;
    const maxPercent = 100;
    const sum = arsipData.value.reduce((acc, item) => acc + (parseFloat(item.persentase) || 0), 0);
    let avg = sum / arsipData.value.length;
    return avg > 100 ? 100 : avg;
});

// Computed Properties untuk Modal Hasil
const totalSuaraMasuk = computed(() => {
    if (!hasilData.value || !hasilData.value.hasil) return 0;
    return hasilData.value.hasil.reduce((sum, kandidat) => sum + (parseInt(kandidat.jml_pemilih) || 0), 0);
});

// Computed property for Total DPT di modal
const totalDptModal = computed(() => {
    if (hasilData.value && hasilData.value.jumlahDpt && hasilData.value.jumlahDpt.jml_dpt) {
        return parseInt(hasilData.value.jumlahDpt.jml_dpt) || 0;
    }
    return jumlahDptSekolah.value;
});

const jumlahDptSekolah = computed(() => {
    if (hasilData.value && hasilData.value.jumlahDpt && hasilData.value.jumlahDpt.jml_dpt) {
        return parseInt(hasilData.value.jumlahDpt.jml_dpt) || 0;
    }
    if (selectedSekolah.value && selectedSekolah.value.jml_dpt) return parseInt(selectedSekolah.value.jml_dpt) || 0;
    if (!selectedSekolah.value) return 0;
    return parseInt(selectedSekolah.value.jml_dpt) || 0;
});

const jumlahTidakMemilih = computed(() => {
    const dpt = jumlahDptSekolah.value;
    const suara = totalSuaraMasuk.value;
    return Math.max(0, dpt - suara);
});


onMounted(() => {
    fetchData(1);
});
</script>
