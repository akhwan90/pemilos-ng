<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Formulir Model C.Hasil (C1)</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="max-w-4xl mx-auto">
                <div class="mb-6 border-b border-gray-100 pb-4 flex justify-between items-end">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Berita Acara & Sertifikat Hasil</h2>
                        <p class="text-sm text-gray-500">Rekapitulasi perhitungan suara tingkat TPS.</p>
                    </div>
                    
                    <button v-if="hasilData" type="button" @click="cetakC1" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak PDF
                    </button>
                </div>

                <!-- Skeleton Loader -->
                <div v-if="loading" class="animate-pulse space-y-6">
                    <div class="h-40 bg-gray-100 rounded-lg"></div>
                    <div class="h-64 bg-gray-100 rounded-lg"></div>
                </div>

                <!-- Pesan Peringatan jika belum ditutup -->
                <div v-else-if="errorMessage" class="bg-red-50 border border-red-200 p-8 rounded-xl text-center">
                    <div class="w-16 h-16 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-red-800 mb-2">Akses Ditolak</h3>
                    <p class="text-red-600 mb-6 max-w-lg mx-auto">{{ errorMessage }}</p>
                    <router-link to="/admin-tps/selesai" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition">
                        Ke Menu Selesai Pemilihan
                    </router-link>
                </div>

                <!-- Laporan C1 Ready -->
                <div v-else-if="hasilData">
                    <!-- Data Pemilih (DPT) -->
                    <div class="mb-8">
                        <h3 class="font-bold text-gray-800 bg-gray-50 p-3 rounded-t-lg border border-gray-200">I. DATA PEMILIH DAN PENGGUNA HAK PILIH</h3>
                        <div class="border border-t-0 border-gray-200 rounded-b-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700 w-3/4">1. Jumlah Pemilih dalam Daftar Pemilih Tetap (DPT)</td>
                                        <td class="px-4 py-3 text-right font-bold">{{ hasilData.hasil.statistik.total_dpt }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-700">2. Jumlah Pengguna Hak Pilih (Suara Masuk)</td>
                                        <td class="px-4 py-3 text-right font-bold text-indigo-700">{{ hasilData.hasil.statistik.suara_masuk }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700 text-red-600">3. Jumlah Pemilih yang Tidak Menggunakan Hak Pilih</td>
                                        <td class="px-4 py-3 text-right font-bold text-red-600">{{ hasilData.hasil.statistik.tidak_memilih }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Perolehan Suara Paslon -->
                    <div class="mb-8">
                        <h3 class="font-bold text-gray-800 bg-gray-50 p-3 rounded-t-lg border border-gray-200">II. DATA PEROLEHAN SUARA PASANGAN CALON</h3>
                        <div class="border border-t-0 border-gray-200 rounded-b-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Calon / Paslon</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">Suara Sah</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <tr v-for="paslon in hasilData.hasil.perolehan" :key="paslon.id_calon">
                                        <td class="px-4 py-3 font-bold text-center bg-gray-50">{{ paslon.no_urut }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900" v-html="paslon.nama_lengkap"></td>
                                        <td class="px-4 py-3 text-right font-bold text-xl text-indigo-600">{{ paslon.jumlah_suara }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Rekap Akhir -->
                    <div class="mb-8">
                        <h3 class="font-bold text-gray-800 bg-gray-50 p-3 rounded-t-lg border border-gray-200">III. DATA SUARA SAH DAN TIDAK SAH</h3>
                        <div class="border border-t-0 border-gray-200 rounded-b-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700 w-3/4">A. Jumlah Suara Sah (Total dari seluruh Paslon)</td>
                                        <td class="px-4 py-3 text-right font-bold text-green-600 text-lg">{{ hasilData.hasil.statistik.suara_sah }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700">B. Jumlah Suara Tidak Sah</td>
                                        <td class="px-4 py-3 text-right font-bold text-red-600 text-lg">{{ hasilData.hasil.statistik.suara_tidak_sah }}</td>
                                    </tr>
                                    <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                                        <td class="px-4 py-3 font-bold text-indigo-900">C. TOTAL KESELURUHAN (A + B)</td>
                                        <td class="px-4 py-3 text-right font-bold text-indigo-900 text-xl">{{ hasilData.hasil.statistik.suara_sah + hasilData.hasil.statistik.suara_tidak_sah }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Upload Scan (Opsional) -->
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">Scan Dokumen Fisik</h4>
                            <p class="text-xs text-gray-500">Anda dapat mengunggah hasil scan/foto dokumen C1 yang telah ditandatangani.</p>
                        </div>
                        <button class="px-4 py-2 bg-white border border-gray-300 rounded shadow-sm text-sm font-medium hover:bg-gray-50 transition">
                            Upload File
                        </button>
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
import moment from 'moment';

const toast = useToast();
const loading = ref(true);
const hasilData = ref(null);
const errorMessage = ref(null);

onMounted(() => {
    fetchHasil();
});

async function fetchHasil() {
    try {
        const res = await api.get('/admin-tps/hasil-c1');
        if (res.data.success) {
            hasilData.value = res.data.data;
        }
    } catch (error) {
        console.error('Error C1:', error);
        errorMessage.value = error.response?.data?.message || 'Gagal memuat data C1.';
        if (error.response?.status !== 400) {
            toast.error('Gagal mengambil data laporan C1');
        }
    } finally {
        loading.value = false;
    }
}

function cetakC1() {
    toast.success('Mempersiapkan dokumen PDF...');
    // TODO: Implementasi export ke PDF atau window.print()
    setTimeout(() => {
        window.print();
    }, 500);
}
</script>