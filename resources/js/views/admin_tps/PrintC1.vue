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

            <p class="mb-6 text-justify">
                {{ formatBeritaAcaraTerbilang(hasilData.waktu_selesai) }} 
                Kelompok Penyelenggara Pemungutan Suara (KPPS) mengadakan Rapat Pemungutan dan Penghitungan
                Suara dalam Pemilihan Ketua OSIS yang dihadiri oleh Saksi dan / atau Pengawas TPS *), bertempat di: 
            </p>

            <p class="text-xl mb-6 font-bold text-center">
                TPS: {{ hasilData.tps }}
            </p>

            <p class="mb-6 text-justify">
                Kegiatan KPPS dalam Rapat Pemungutan Suara yang dipimpin oleh Ketua KPPS dimulai Pukul 07.00 s.d. 13.00 waktu setempat dan Rapat Penghitungan Suara dimulai Pukul 13.00 waktu setempat, dengan hasil rapat sebagai berikut:
            </p>

            <!-- Data Pemilih (DPT) -->
            <div class="mb-6">
                <h3 class="font-bold text-sm mb-2">I. DATA PEMILIH DAN PENGGUNA HAK PILIH</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-black px-3 py-2 w-6/12text-left">URAIAN</th>
                            <th class="border border-black px-3 py-2 w-2/12 text-center">LAKI-LAKI</th>
                            <th class="border border-black px-3 py-2 w-2/12 text-center">PEREMPUAN</th>
                            <th class="border border-black px-3 py-2 w-2/12 text-center">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-3 py-2 w-3/4">1. Jumlah Pemilih dalam Daftar Pemilih Tetap (DPT)</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.total_dpt[0].jumlah_l }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.total_dpt[0].jumlah_p }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.total_dpt[0].total }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2">2. Jumlah Pengguna Hak Pilih (Suara Masuk)</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.suara_masuk[0].jumlah_l }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.suara_masuk[0].jumlah_p }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.suara_masuk[0].total }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2">3. Jumlah Pemilih yang Tidak Menggunakan Hak Pilih</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.total_dpt[0].jumlah_l - hasilData.hasil.suara_masuk[0].jumlah_l }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.total_dpt[0].jumlah_p - hasilData.hasil.suara_masuk[0].jumlah_p }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ hasilData.hasil.total_dpt[0].total - hasilData.hasil.suara_masuk[0].total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Data Pemilih DIFABLE -->
            <div class="mb-6">
                <h3 class="font-bold text-sm mb-2">II. DATA PENGGUNAAN HAK PILIH DISABILITAS /                     PENYANDANG CACAT</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-black px-3 py-2 w-6/12 text-left">URAIAN</th>
                            <th class="border border-black px-3 py-2 w-2/12 text-center">LAKI-LAKI</th>
                            <th class="border border-black px-3 py-2 w-2/12 text-center">PEREMPUAN</th>
                            <th class="border border-black px-3 py-2 w-2/12 text-center">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-black px-3 py-2 w-3/4">1. Jumlah Pemilih dalam Daftar Pemilih Tetap
                                (DPT)</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{
                                hasilData.hasil.difabel[0].jumlah_l }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{
                                hasilData.hasil.difabel[0].jumlah_p }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{
                                hasilData.hasil.difabel[0].total }}</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2">2. Jumlah Pengguna Hak Pilih (Suara Masuk)</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{
                                hasilData.hasil.difabel_memilih[0].jumlah_l }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{
                                hasilData.hasil.difabel_memilih[0].jumlah_p }}</td>
                            <td class="border border-black px-3 py-2 text-center font-bold">{{
                                hasilData.hasil.difabel_memilih[0].total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Perolehan Suara Paslon -->
            <div class="mb-10">
                <h3 class="font-bold text-sm mb-2">III. DATA PEROLEHAN SUARA PASANGAN CALON</h3>
                <table class="w-full border-collapse border border-black text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-black px-3 py-2 w-12 text-center">No</th>
                            <th class="border border-black px-3 py-2 text-left">Nama Calon / Pasangan Calon</th>
                            <th class="border border-black px-3 py-2 w-32 text-center">Suara Sah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="paslon in hasilData.hasil.perolehan_paslon" :key="paslon.id_calon">
                            <td class="border border-black px-3 py-2 text-center font-bold">{{ paslon.no }}</td>
                            <td class="border border-black px-3 py-2" v-html="paslon.nama"></td>
                            <td class="border border-black px-3 py-2 text-center font-bold text-lg">{{ paslon.total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Rekap Akhir -->
            <div class="mb-8">
                <h3 class="font-bold text-sm mb-2">IV. DATA SUARA SAH DAN TIDAK SAH</h3>
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
                <div class="text-center mb-4 font-bold">NAMA DAN TANDA TANGAN KELOMPOK PENYELENGGARA PEMUNGUTAN SUARA</div>
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
                            <td class="border border-black px-3 py-2 text-left">{{ hasilData.perangkat_tps.ketua.nama }}</td>
                            <td class="border border-black px-3 py-2 text-left">Ketua</td>
                            <td class="border border-black px-3 pt-3 text-left">1. ................</td>                            
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2 text-center">2</td>
                            <td class="border border-black px-3 py-2 text-left">{{ hasilData.perangkat_tps.anggota_1.nama }}
                            </td>
                            <td class="border border-black px-3 py-2 text-left">Anggota 1</td>
                            <td class="border border-black px-3 pt-3 text-left">2. ................</td>
                        </tr>
                        <tr>
                            <td class="border border-black px-3 py-2 text-center">3</td>
                            <td class="border border-black px-3 py-2 text-left">{{ hasilData.perangkat_tps.anggota_2.nama }}
                            </td>
                            <td class="border border-black px-3 py-2 text-left">Anggota 2</td>
                            <td class="border border-black px-3 pt-3 text-left">3. ................</td>
                        </tr>
                    </tbody>
                </table>
            </div>

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
                        <tr v-for="(saksi, index) in hasilData.perangkat_tps.saksi" :key="index">
                            <td class="border border-black px-3 py-2 text-center">{{ index + 1 }}</td>
                            <td class="border border-black px-3 py-2 text-left">{{ saksi.nama }}
                            </td>
                            <td class="border border-black px-3 py-2 text-left">{{ saksi.paslon }}</td>
                            <td class="border border-black px-3 pt-3 text-left">{{ index + 1 }}. ................</td>
                        </tr>
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
                // window.print();
            }, 1000);
        }
    } catch (error) {
        console.error('Error C1:', error);
        errorMessage.value = error.response?.data?.message || 'Gagal memuat data C1.';
    } finally {
        loading.value = false;
    }
}

function angkaKeTerbilang(n) {
    const satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
    if (n < 12) return satuan[n];
    if (n < 20) return angkaKeTerbilang(n - 10) + ' belas';
    if (n < 100) return angkaKeTerbilang(Math.floor(n / 10)) + ' puluh ' + satuan[n % 10];
    if (n < 200) return 'seratus ' + angkaKeTerbilang(n - 100);
    if (n < 1000) return angkaKeTerbilang(Math.floor(n / 100)) + ' ratus ' + angkaKeTerbilang(n % 100);
    if (n < 2000) return 'seribu ' + angkaKeTerbilang(n - 1000);
    if (n < 1000000) return angkaKeTerbilang(Math.floor(n / 1000)) + ' ribu ' + angkaKeTerbilang(n % 1000);
    return n;
}

function formatBeritaAcaraTerbilang(dateString) {
    const date = new Date(dateString.replace(' ', 'T'));

    const hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(date);
    const tanggalAngka = date.getDate();
    const tanggalTerbilang = angkaKeTerbilang(tanggalAngka);
    const bulan = new Intl.DateTimeFormat('id-ID', { month: 'long' }).format(date);
    const tahunAngka = date.getFullYear();
    const tahunTerbilang = angkaKeTerbilang(tahunAngka);

    return `Pada hari ini, ${hari} tanggal ${tanggalAngka} (${tanggalTerbilang}) bulan ${bulan} tahun ${tahunAngka} (${tahunTerbilang})`;
}
</script>

<style>
@media print {
    @page { margin: 0.5cm 0 1cm 0; }
    /* Override sudah diatur global di app.css, ini untuk spesifik komponen bila perlu */
    .print-container { overflow: visible !important; height: auto !important; }
}
</style>