<template>
    <div class="print-container bg-white text-black p-8 font-serif w-full max-w-4xl mx-auto">
        <!-- Skeleton Loader -->
        <div v-if="loading" class="text-center py-20">
            <p>Memuat data pelaporan...</p>
        </div>

        <!-- Laporan Ready -->
        <div v-else-if="pelaporanData">
            <!-- Header -->
            <div class="text-center mb-8 border-b-2 border-black pb-4">
                <h1 class="text-2xl font-bold uppercase">LAPORAN HASIL PENGAWASAN PEMILOS</h1>
                <p class="text-lg mt-2">Nomor: {{ pelaporanData.nomor_laporan }}</p>
            </div>

            <!-- 1. Data Pengawasan -->
            <div class="mb-6 text-justify">
                <h3 class="font-bold mb-3 text-lg">1. Data Pengawasan</h3>
                <div class="pl-5 space-y-3">
                    <div class="flex">
                        <div class="w-1/3">a. Tahapan yang Diawasi</div>
                        <div class="w-2/3 flex"><span class="mr-2">:</span> <span class="flex-1">{{ pelaporanData.tahapan }}</span></div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">b. Nama Pelaksana Tugas</div>
                        <div class="w-2/3 flex"><span class="mr-2">:</span> <span class="flex-1">{{ pelaporanData.pelaksana }}</span></div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">c. Jabatan</div>
                        <div class="w-2/3 flex"><span class="mr-2">:</span> <span class="flex-1">{{ pelaporanData.jabatan }}</span></div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">d. Nama Sekolah</div>
                        <div class="w-2/3 flex"><span class="mr-2">:</span> <span class="flex-1">{{ pelaporanData.nama_sekolah }}</span></div>
                    </div>
                </div>
            </div>

            <!-- 2. Kegiatan Pengawasan -->
            <div class="mb-6 text-justify">
                <h3 class="font-bold mb-3 text-lg">2. Kegiatan Pengawasan</h3>
                <div class="pl-5 space-y-3">
                    <div class="flex">
                        <div class="w-1/3">a. Tujuan Pengawasan</div>
                        <div class="w-2/3 flex"><span class="mr-2">:</span> <span class="flex-1">{{ pelaporanData.tujuan }}</span></div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">b. Sasaran</div>
                        <div class="w-2/3 flex"><span class="mr-2">:</span> <span class="flex-1">{{ pelaporanData.sasaran }}</span></div>
                    </div>
                    <div class="flex">
                        <div class="w-1/3">c. Waktu dan Tempat</div>
                        <div class="w-2/3 flex"><span class="mr-2">:</span> <span class="flex-1">{{ pelaporanData.waktu_tempat }}</span></div>
                    </div>
                </div>
            </div>

            <!-- 3. Uraian Singkat -->
            <div class="mb-8">
                <h3 class="font-bold mb-3 text-lg">3. Uraian Singkat Hasil Pengawasan</h3>
                <div class="pl-5">
                    <div class="border border-black p-4 min-h-[200px] whitespace-pre-wrap text-justify">
                        {{ pelaporanData.uraian }}
                    </div>
                </div>
            </div>

            <!-- Signature -->
            <div class="flex justify-end mt-12">
                <div class="text-center w-72">
                    <p class="mb-1">........................................, {{ getTanggalHariIni() }}</p>
                    <p class="mb-20">Pengawas TPS / PPO,</p>
                    <p class="font-bold underline uppercase">{{ pelaporanData.pelaksana }}</p>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-20 text-red-600 font-sans">
            <h3 class="text-xl font-bold mb-2">Data Tidak Ditemukan</h3>
            <p>Belum ada data pelaporan yang disimpan.</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import api from '../../services/api'
import { useToast } from '../../composables/useToast'

const toast = useToast()
const loading = ref(true)
const pelaporanData = ref(null)

const getTanggalHariIni = () => {
    const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
    const date = new Date()
    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`
}

const loadData = async () => {
    try {
        const response = await api.get('/admin-sekolah/pelaporan')
        if (response.data && response.data.pelaporan) {
            pelaporanData.value = JSON.parse(response.data.pelaporan)
            
            // Tunggu render Vue selesai, lalu otomatis buka jendela print
            nextTick(() => {
                setTimeout(() => {
                    window.print()
                }, 500)
            })
        }
    } catch (error) {
        console.error('Gagal memuat data pelaporan', error)
        toast.error('Gagal memuat data cetakan')
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    loadData()
})
</script>

<style scoped>
@media print {
    @page {
        size: A4 portrait;
        margin: 2cm;
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
