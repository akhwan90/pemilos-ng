<template>
    <div class="print-container bg-white text-black p-8 font-serif w-full max-w-4xl mx-auto">
        <!-- Skeleton Loader -->
        <div v-if="loading" class="text-center py-20">
            <p>Memuat data cetakan...</p>
        </div>

        <div v-else-if="errorMessage" class="text-center py-20 text-red-600 font-sans">
            <h3 class="text-xl font-bold mb-2">Akses Ditolak</h3>
            <p>{{ errorMessage }}</p>
        </div>

        <!-- Laporan C1 Ready -->
        <div v-else-if="hasilData">
            <!-- Header -->
            <div class="text-center mb-6 border-b-2 border-black pb-4">
                <h1 class="text-xl font-bold uppercase">MODEL C.HASIL - TPS</h1>
                <h2 class="text-lg font-bold uppercase">BERITA ACARA DAN SERTIFIKAT HASIL PENGHITUNGAN SUARA</h2>
                <p class="text-md mt-1">PEMILIHAN KETUA DAN WAKIL KETUA OSIS</p>
                <p class="text-md font-bold mt-2">TPS: {{ hasilData.tps }}</p>
            </div>

            <!-- Data Pemilih (DPT) -->
            <div class="mb-6">
                <h3 class="font-bold text-sm mb-2">I. DATA PEMILIH DAN PENGGUNA HAK PILIH</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <tbody>
                        <tr>
                            <td class="border border-black px-3 py-2 w-3/4">1. Jumlah Pemilih dalam Daftar Pemilih Tetap (DPT)</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.statistik.total_dpt }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2">2. Jumlah Pengguna Hak Pilih (Suara Masuk)</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.statistik.suara_masuk }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2">3. Jumlah Pemilih yang Tidak Menggunakan Hak Pilih</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.statistik.tidak_memilih }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Perolehan Suara Paslon -->
            <div class="mb-6">
                <h3 class="font-bold text-sm mb-2">II. DATA PEROLEHAN SUARA PASANGAN CALON</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-black px-3 py-2 w-12 text-center">No</th>
                            <th class="border border-black px-3 py-2 text-left">Nama Calon / Pasangan Calon</th>
                            <th class="border border-black px-3 py-2 w-32 text-center">Suara Sah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="paslon in hasilData.hasil.perolehan" :key="paslon.id_calon">
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ paslon.no_urut }}</td>
                            <td class="border border-black px-3 py-2" v-html="paslon.nama_lengkap"></td>
                            <td class="border border-black px-3 py-2 text-center font-bold text-lg">{{ paslon.jumlah_suara }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Rekap Akhir -->
            <div class="mb-8">
                <h3 class="font-bold text-sm mb-2">III. DATA SUARA SAH DAN TIDAK SAH</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <tbody>
                        <tr>
                            <td class="border border-black px-3 py-2 w-3/4">A. Jumlah Suara Sah</td>
                            <td class="border border-black px-3 py-2 text-center font-bold text-lg">{{ hasilData.hasil.statistik.suara_sah }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2">B. Jumlah Suara Tidak Sah</td>
                            <td class="border border-black px-3 py-2 text-center font-bold text-lg">{{ hasilData.hasil.statistik.suara_tidak_sah }}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="border border-black px-3 py-2 font-bold uppercase">C. TOTAL KESELURUHAN (A + B)</td>
                            <td class="border border-black px-3 py-2 text-center font-bold text-xl">{{ hasilData.hasil.statistik.suara_sah + hasilData.hasil.statistik.suara_tidak_sah }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tanda Tangan -->
            <div class="mt-12 break-inside-avoid">
                <table class="w-full text-center text-sm border-none">
                    <tbody>
                        <tr>
                            <td colspan="3" class="pb-12 font-bold">KELOMPOK PENYELENGGARA PEMUNGUTAN SUARA (KPPS) / PERANGKAT TPS</td>
                        </tr>
                        <tr>
                            <td class="w-1/3 pb-8">
                                <span v-if="hasilData.perangkat_tps && hasilData.perangkat_tps.ketua && hasilData.perangkat_tps.ketua.nama">
                                    ({{ hasilData.perangkat_tps.ketua.nama }})
                                </span>
                                <span v-else>(...................................................)</span>
                                <br>Ketua
                            </td>
                            <td class="w-1/3 pb-8">
                                <span v-if="hasilData.perangkat_tps && hasilData.perangkat_tps.anggota_1 && hasilData.perangkat_tps.anggota_1.nama">
                                    ({{ hasilData.perangkat_tps.anggota_1.nama }})
                                </span>
                                <span v-else>(...................................................)</span>
                                <br>Anggota 1
                            </td>
                            <td class="w-1/3 pb-8">
                                <span v-if="hasilData.perangkat_tps && hasilData.perangkat_tps.anggota_2 && hasilData.perangkat_tps.anggota_2.nama">
                                    ({{ hasilData.perangkat_tps.anggota_2.nama }})
                                </span>
                                <span v-else>(...................................................)</span>
                                <br>Anggota 2
                            </td>
                        </tr>
                        <template v-if="hasilData.perangkat_tps && hasilData.perangkat_tps.saksi && hasilData.perangkat_tps.saksi.length > 0">
                            <tr>
                                <td colspan="3" class="pt-8 pb-12 font-bold">SAKSI PASANGAN CALON</td>
                            </tr>
                            <tr v-for="i in Math.ceil(hasilData.perangkat_tps.saksi.length / 3)" :key="'saksi-row-' + i">
                                <td v-for="j in 3" :key="'saksi-col-' + i + '-' + j" class="w-1/3 pb-8">
                                    <template v-if="hasilData.perangkat_tps.saksi[(i-1)*3 + (j-1)]">
                                        <span>({{ hasilData.perangkat_tps.saksi[(i-1)*3 + (j-1)].nama }})</span><br>Saksi
                                    </template>
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="3" class="pt-8 pb-12 font-bold">SAKSI PASANGAN CALON</td>
                            </tr>
                            <tr>
                                <td class="w-1/3 pb-8">(...................................................)<br>Saksi 1</td>
                                <td class="w-1/3 pb-8"></td>
                                <td class="w-1/3 pb-8">(...................................................)<br>Saksi 2</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-10 text-xs text-gray-500 text-right font-sans">
                Dicetak oleh sistem pada: {{ currentDate }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import moment from 'moment';

const loading = ref(true);
const hasilData = ref(null);
const errorMessage = ref(null);
const currentDate = ref(moment().locale('id').format('DD MMMM YYYY, HH:mm:ss'));

onMounted(() => {
    fetchHasil();
    // Beri gaya khusus global untuk body khusus di rute ini agar putih total
    document.body.style.backgroundColor = 'white';
});

async function fetchHasil() {
    try {
        const res = await api.get('/admin-tps/hasil-c1');
        if (res.data.success) {
            hasilData.value = res.data.data;
            // Delay sedikit lalu panggil print dialog otomatis
            setTimeout(() => {
                window.print();
            }, 1000);
        }
    } catch (error) {
        console.error('Error C1:', error);
        errorMessage.value = error.response?.data?.message || 'Gagal memuat data C1.';
    } finally {
        loading.value = false;
    }
}
</script>

<style>
@media print {
    @page { margin: 1cm; }
    /* Override sudah diatur global di app.css, ini untuk spesifik komponen bila perlu */
    .print-container { overflow: visible !important; height: auto !important; }
}
</style>