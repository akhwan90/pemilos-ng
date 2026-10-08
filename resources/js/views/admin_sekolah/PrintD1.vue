<template>
    <div class="print-container bg-white text-black p-8 font-serif w-full max-w-5xl mx-auto">
        <!-- Skeleton Loader -->
        <div v-if="loading" class="text-center py-20">
            <p>Memuat data cetakan D1...</p>
        </div>

        <div v-else-if="errorMessage" class="text-center py-20 text-red-600 font-sans">
            <h3 class="text-xl font-bold mb-2">Akses Ditolak</h3>
            <p>{{ errorMessage }}</p>
        </div>

        <!-- Laporan D1 Ready -->
        <div v-else-if="hasilData">
            <!-- Header -->
            <div class="text-center mb-6 border-b-2 border-black pb-4">
                <h1 class="text-xl font-bold uppercase">MODEL D.HASIL - SEKOLAH</h1>
                <h2 class="text-lg font-bold uppercase">BERITA ACARA DAN SERTIFIKAT HASIL PENGHITUNGAN SUARA TINGKAT SEKOLAH</h2>
                <p class="text-md mt-1">PEMILIHAN KETUA DAN WAKIL KETUA OSIS</p>
                <p class="text-md font-bold mt-2">SEKOLAH: {{ hasilData.tps }}</p>
            </div>

            <p class="mb-6 text-justify">
                Kelompok Penyelenggara Pemungutan Suara (KPPS) atau PPO Tingkat Sekolah mengadakan Rapat Rekapitulasi Hasil Penghitungan Suara dalam Pemilihan Ketua OSIS yang dihadiri oleh Saksi dan atau Pengawas Sekolah, bertempat di <strong>{{ hasilData.tps }}</strong>. Dengan hasil rekapitulasi sebagai berikut:
            </p>

            <!-- Data Pemilih (DPT) -->
            <div class="mb-6">
                <h3 class="font-bold text-sm mb-2">I. DATA PEMILIH DAN PENGGUNA HAK PILIH</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr>
                            <th rowspan="2" class="border border-black p-2 text-left" style="width: 25%">Uraian</th>
                            <th colspan="3" v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center uppercase">
                                {{ tps.nama_tps.substring(0, 5) }}
                            </th>
                            <th colspan="3" class="border border-black p-2 text-center font-bold bg-gray-100 uppercase">
                                Jumlah Akhir
                            </th>
                        </tr>
                        <tr>
                            <template v-for="(tps, index) in hasilData.hasil" :key="'sub_'+index">
                                <th class="border border-black p-1 text-center font-normal">L</th>
                                <th class="border border-black p-1 text-center font-normal">P</th>
                                <th class="border border-black p-1 text-center font-normal">Total</th>
                            </template>
                            <th class="border border-black p-1 text-center font-bold bg-gray-100">L</th>
                            <th class="border border-black p-1 text-center font-bold bg-gray-100">P</th>
                            <th class="border border-black p-1 text-center font-bold bg-gray-100">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black p-2">1. Jumlah Pemilih dalam Daftar Pemilih Tetap (DPT)</td>
                            <template v-for="(tps, index) in hasilData.hasil" :key="index">
                                <td class="border border-black p-2 text-center">{{ tps.hasil.total_dpt[0].jumlah_l }}</td>
                                <td class="border border-black p-2 text-center">{{ tps.hasil.total_dpt[0].jumlah_p }}</td>
                                <td class="border border-black p-2 text-center font-bold">{{ tps.hasil.total_dpt[0].total }}</td>
                            </template>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.total_dpt[0].jumlah_l || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.total_dpt[0].jumlah_p || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.total_dpt[0].total || 0), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">2. Jumlah Pengguna Hak Pilih (Suara Masuk)</td>
                            <template v-for="(tps, index) in hasilData.hasil" :key="index">
                                <td class="border border-black p-2 text-center">{{ tps.hasil.suara_masuk[0].jumlah_l }}</td>
                                <td class="border border-black p-2 text-center">{{ tps.hasil.suara_masuk[0].jumlah_p }}</td>
                                <td class="border border-black p-2 text-center font-bold">{{ tps.hasil.suara_masuk[0].total }}</td>
                            </template>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.suara_masuk[0].jumlah_l || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.suara_masuk[0].jumlah_p || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.suara_masuk[0].total || 0), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">3. Jumlah Pemilih yang Tidak Menggunakan Hak Pilih</td>
                            <template v-for="(tps, index) in hasilData.hasil" :key="index">
                                <td class="border border-black p-2 text-center">{{ tps.hasil.total_dpt[0].jumlah_l - tps.hasil.suara_masuk[0].jumlah_l }}</td>
                                <td class="border border-black p-2 text-center">{{ tps.hasil.total_dpt[0].jumlah_p - tps.hasil.suara_masuk[0].jumlah_p }}</td>
                                <td class="border border-black p-2 text-center font-bold">{{ tps.hasil.total_dpt[0].total - tps.hasil.suara_masuk[0].total }}</td>
                            </template>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + (parseInt(t.hasil.total_dpt[0].jumlah_l || 0) - parseInt(t.hasil.suara_masuk[0].jumlah_l || 0)), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + (parseInt(t.hasil.total_dpt[0].jumlah_p || 0) - parseInt(t.hasil.suara_masuk[0].jumlah_p || 0)), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + (parseInt(t.hasil.total_dpt[0].total || 0) - parseInt(t.hasil.suara_masuk[0].total || 0)), 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Data Difabel -->
            <div class="mb-6">
                <h3 class="font-bold text-sm mb-2">II. DATA PEMILIH DAN PENGGUNAAN HAK PILIH DISABILITAS / PENYANDANG CACAT</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr>
                            <th rowspan="2" class="border border-black p-2 text-left" style="width: 25%">Uraian</th>
                            <th colspan="3" v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center uppercase">
                                {{ tps.nama_tps.substring(0, 5) }}
                            </th>
                            <th colspan="3" class="border border-black p-2 text-center font-bold bg-gray-100 uppercase">
                                Jumlah Akhir
                            </th>
                        </tr>
                        <tr>
                            <template v-for="(tps, index) in hasilData.hasil" :key="'sub_'+index">
                                <th class="border border-black p-1 text-center font-normal">L</th>
                                <th class="border border-black p-1 text-center font-normal">P</th>
                                <th class="border border-black p-1 text-center font-normal">Total</th>
                            </template>
                            <th class="border border-black p-1 text-center font-bold bg-gray-100">L</th>
                            <th class="border border-black p-1 text-center font-bold bg-gray-100">P</th>
                            <th class="border border-black p-1 text-center font-bold bg-gray-100">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black p-2">1. Jumlah Pemilih Disabilitas / Penyandang Cacat</td>
                            <template v-for="(tps, index) in hasilData.hasil" :key="index">
                                <td class="border border-black p-2 text-center">{{ tps.hasil.difabel[0].jumlah_l }}</td>
                                <td class="border border-black p-2 text-center">{{ tps.hasil.difabel[0].jumlah_p }}</td>
                                <td class="border border-black p-2 text-center font-bold">{{ tps.hasil.difabel[0].total }}</td>
                            </template>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.difabel[0].jumlah_l || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.difabel[0].jumlah_p || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.difabel[0].total || 0), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">2. Jumlah Pemilih Disabilitas yang menggunakan hak pilih</td>
                            <template v-for="(tps, index) in hasilData.hasil" :key="index">
                                <td class="border border-black p-2 text-center">{{ tps.hasil.difabel_memilih[0].jumlah_l }}</td>
                                <td class="border border-black p-2 text-center">{{ tps.hasil.difabel_memilih[0].jumlah_p }}</td>
                                <td class="border border-black p-2 text-center font-bold">{{ tps.hasil.difabel_memilih[0].total }}</td>
                            </template>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.difabel_memilih[0].jumlah_l || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.difabel_memilih[0].jumlah_p || 0), 0) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.difabel_memilih[0].total || 0), 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Perolehan Suara Paslon -->
            <div class="mb-6">
                <h3 class="font-bold text-sm mb-2">III. DATA PEROLEHAN SUARA PASANGAN CALON</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr>
                            <th class="border border-black p-2 text-center" style="width: 5%">No</th>
                            <th class="border border-black p-2 text-left" style="width: 25%">Nama Calon / Paslon</th>
                            <th v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center uppercase">
                                {{ tps.nama_tps.substring(0, 5) }}
                            </th>
                            <th class="border border-black p-2 text-center font-bold bg-gray-100 uppercase">
                                Jumlah Akhir
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="paslon in hasilData.calons" :key="paslon.id">
                            <td class="border border-black p-2 text-center font-bold">{{ paslon.no }}</td>
                            <td class="border border-black p-2" v-html="paslon.nama"></td>
                            <td v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center font-bold">
                                {{ tps.hasil?.perolehan_paslon?.[paslon.id]?.total ?? 0 }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil?.perolehan_paslon?.[paslon.id]?.total || 0), 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Rekap Akhir Sah dan Tidak Sah -->
            <div class="mb-8">
                <h3 class="font-bold text-sm mb-2">IV. DATA SUARA SAH DAN TIDAK SAH</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr>
                            <th class="border border-black p-2 text-left" style="width: 40%">Uraian</th>
                            <th v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center uppercase">
                                {{ tps.nama_tps.substring(0, 5) }}
                            </th>
                            <th class="border border-black p-2 text-center font-bold bg-gray-100 uppercase">
                                Jumlah Akhir
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black p-2">A. Jumlah Suara Sah (Total dari seluruh Paslon)</td>
                            <td v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center font-bold">
                                {{ tps.hasil.statistik.suara_sah }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.statistik.suara_sah || 0), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">B. Jumlah Suara Tidak Sah</td>
                            <td v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center font-bold">
                                {{ tps.hasil.statistik.suara_tidak_sah }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.statistik.suara_tidak_sah || 0), 0) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2 font-bold">C. TOTAL KESELURUHAN (A + B)</td>
                            <td v-for="(tps, index) in hasilData.hasil" :key="index" class="border border-black p-2 text-center font-bold">
                                {{ parseInt(tps.hasil.statistik.suara_sah) + parseInt(tps.hasil.statistik.suara_tidak_sah) }}
                            </td>
                            <td class="border border-black p-2 text-center font-bold bg-gray-100">
                                {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.statistik.suara_sah || 0) + parseInt(t.hasil.statistik.suara_tidak_sah || 0), 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures PPO -->
            <div class="mt-10 break-inside-avoid">
                <div class="text-center mb-4 font-bold">NAMA DAN TANDA TANGAN PANITIA PEMILIHAN ORGANISASI (PPO) TINGKAT SEKOLAH</div>
                <table class="w-full text-center text-sm border-none">
                    <thead>
                        <tr>
                            <th class="border border-black px-3 py-2 w-1/12 text-center">No</th>
                            <th class="border border-black px-3 py-2 w-4/12 text-left">Nama</th>
                            <th class="border border-black px-3 py-2 w-3/12 text-left">Jabatan</th>
                            <th class="border border-black px-3 py-2 w-4/12 text-left">Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-3 py-2 text-center">1</td>
                            <td class="border border-black px-3 py-2 text-left">{{ hasilData?.perangkat_tps?.ketua?.nama }}</td>
                            <td class="border border-black px-3 py-2 text-left">Ketua</td>
                            <td class="border border-black px-3 pt-3 text-left">1. ................</td>                            
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2 text-center">2</td>
                            <td class="border border-black px-3 py-2 text-left">{{ hasilData?.perangkat_tps?.anggota_1?.nama }}</td>
                            <td class="border border-black px-3 py-2 text-left">Anggota 1</td>
                            <td class="border border-black px-3 pt-3 text-left">2. ................</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2 text-center">3</td>
                            <td class="border border-black px-3 py-2 text-left">{{ hasilData?.perangkat_tps?.anggota_2?.nama }}</td>
                            <td class="border border-black px-3 py-2 text-left">Anggota 2</td>
                            <td class="border border-black px-3 pt-3 text-left">3. ................</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures Saksi -->
            <div class="mt-12 break-inside-avoid">
                <div class="text-center mb-4 font-bold">NAMA DAN TANDA TANGAN SAKSI PASANGAN CALON</div>
                <table class="w-full text-center text-sm border-none">
                    <thead>
                        <tr>
                            <th class="border border-black px-3 py-2 w-1/12 text-center">No</th>
                            <th class="border border-black px-3 py-2 w-4/12 text-left">Nama</th>
                            <th class="border border-black px-3 py-2 w-3/12 text-left">Paslon</th>
                            <th class="border border-black px-3 py-2 w-4/12 text-left">Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(saksi, index) in hasilData?.perangkat_tps?.saksi" :key="index">
                            <td class="border border-black px-3 py-2 text-center">{{ index + 1 }}</td>
                            <td class="border border-black px-3 py-2 text-left">{{ saksi.nama }}</td>
                            <td class="border border-black px-3 py-2 text-left">{{ saksi.paslon }}</td>
                            <td class="border border-black px-3 pt-3 text-left">{{ index + 1 }}. ................</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import api from '../../services/api';

const loading = ref(true);
const hasilData = ref(null);
const errorMessage = ref('');
const ppoName = ref('');

const fetchHasil = async () => {
    try {
        const res = await api.get('/admin-sekolah/hasil-d1');
        
        if (res.data.success) {
            hasilData.value = res.data.data;
            ppoName.value = res.data.data?.tps || 'KPU Sekolah'; // Mengambil nama instansi/sekolah

            nextTick(() => {
                setTimeout(() => {
                    window.print();
                }, 1000);
            });
        }
    } catch (error) {
        console.error('Error:', error);
        if (error.response?.status === 403) {
            errorMessage.value = error.response.data.message || 'Pemilihan belum selesai. D1 tidak dapat diakses.';
        } else {
            errorMessage.value = 'Gagal memuat data D1.';
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchHasil();
});
</script>

<style scoped>
@media print {
    @page {
        size: landscape;
        margin: 1cm;
    }
    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .print-container {
        padding: 0 !important;
        max-width: 100% !important;
    }
}
</style>
