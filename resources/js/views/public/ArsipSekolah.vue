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
            class="w-full pl-3 pr-10 py-2.5 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg shadow-sm border"
          >
            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>
      </div>

      <!-- Placeholder Konten Arsip -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Tabel Arsip (Static Placeholder) -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Sekolah</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total DPT</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sudah Memilih</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Partisipasi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              
              <!-- Placeholder Baris 1 -->
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">1</td>
                <td class="px-6 py-4">
                  <div class="text-sm font-bold text-gray-900">SMA Negeri 1 Wates</div>
                  <div class="text-xs text-gray-500 font-mono">NPSN: 20401234</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">850</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">810</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[120px]">
                      <div class="bg-emerald-500 h-2.5 rounded-full" style="width: 95.2%"></div>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">95.2%</span>
                  </div>
                </td>
              </tr>

              <!-- Placeholder Baris 2 -->
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">2</td>
                <td class="px-6 py-4">
                  <div class="text-sm font-bold text-gray-900">SMK Negeri 2 Pengasih</div>
                  <div class="text-xs text-gray-500 font-mono">NPSN: 20405678</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">1,200</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">1,050</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[120px]">
                      <div class="bg-blue-500 h-2.5 rounded-full" style="width: 87.5%"></div>
                    </div>
                    <span class="text-sm font-bold text-blue-600">87.5%</span>
                  </div>
                </td>
              </tr>
              
              <!-- Placeholder Baris 3 -->
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">3</td>
                <td class="px-6 py-4">
                  <div class="text-sm font-bold text-gray-900">SMP Negeri 1 Sentolo</div>
                  <div class="text-xs text-gray-500 font-mono">NPSN: 20409012</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">620</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center font-medium">410</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-3">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[120px]">
                      <div class="bg-amber-500 h-2.5 rounded-full" style="width: 66.1%"></div>
                    </div>
                    <span class="text-sm font-bold text-amber-600">66.1%</span>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
        
        <!-- Dummy Footer Pagination -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-between">
          <span class="text-sm text-gray-500">Menampilkan 3 dari 45 Sekolah (Tahun {{ selectedYear }})</span>
          <div class="flex gap-2 text-sm text-gray-500">
            <span class="font-medium text-gray-400 cursor-not-allowed">Halaman Data Dummy</span>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer (Reusable Component) -->
    <PublicFooter />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import PublicHeader from '../../components/public/PublicHeader.vue';
import PublicFooter from '../../components/public/PublicFooter.vue';

const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);

// Simulasi pilihan tahun dari 3 tahun ke belakang
const availableYears = computed(() => {
  return [currentYear, currentYear - 1, currentYear - 2, currentYear - 3];
});
</script>