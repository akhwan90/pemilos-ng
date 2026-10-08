<template>
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Pelaporan Pengawasan</h1>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <p class="text-gray-600 mb-6">
                Silakan lengkapi form pelaporan hasil pengawasan pelaksanaan Pemilos di bawah ini.
            </p>
            
            <form @submit.prevent="submitData" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <BaseInput
                        v-model="form.nomor_laporan"
                        label="Nomor Laporan"
                        placeholder="Masukkan nomor laporan"
                        required
                    />
                    
                    <BaseInput
                        v-model="form.tahapan"
                        label="Tahapan yang Diawasi"
                        placeholder="Contoh: Pemungutan Suara"
                        required
                    />
                    
                    <BaseInput
                        v-model="form.pelaksana"
                        label="Nama Pelaksana Tugas"
                        placeholder="Masukkan nama lengkap"
                        required
                    />
                    
                    <BaseInput
                        v-model="form.jabatan"
                        label="Jabatan"
                        placeholder="Contoh: Ketua PPO"
                        required
                    />
                    
                    <BaseInput
                        v-model="form.nama_sekolah"
                        label="Nama Sekolah"
                        placeholder="Masukkan nama sekolah"
                        required
                    />
                    
                    <BaseInput
                        v-model="form.tujuan"
                        label="Tujuan"
                        placeholder="Tujuan pengawasan"
                        required
                    />
                    
                    <BaseInput
                        v-model="form.sasaran"
                        label="Sasaran"
                        placeholder="Sasaran pengawasan"
                        required
                    />
                    
                    <BaseInput
                        v-model="form.waktu_tempat"
                        label="Waktu dan Tempat"
                        placeholder="Contoh: Senin, 10 Okt 2026 di TPS 01"
                        required
                    />
                </div>

                <div class="mt-4">
                    <BaseTextarea
                        v-model="form.uraian"
                        label="Uraian Singkat"
                        placeholder="Ceritakan uraian singkat hasil pengawasan..."
                        :rows="5"
                        required
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button 
                        type="button" 
                        @click="cetakPelaporan"
                        class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium transition-colors"
                    >
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Laporan
                        </span>
                    </button>

                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors disabled:opacity-50"
                        :disabled="isLoading"
                    >
                        <span v-if="isLoading">Menyimpan...</span>
                        <span v-else>Simpan Pelaporan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../services/api'
import BaseInput from '../../components/BaseInput.vue'
import BaseTextarea from '../../components/BaseTextarea.vue'
import { useToast } from '../../composables/useToast'

const toast = useToast()
const isLoading = ref(false)

const form = ref({
    nomor_laporan: '',
    tahapan: '',
    pelaksana: '',
    jabatan: '',
    nama_sekolah: '',
    tujuan: '',
    sasaran: '',
    waktu_tempat: '',
    uraian: ''
})

// Karena backend belum dibuat spesifik untuk pelaporan, 
// fungsi ini disiapkan untuk mengambil dan menyimpan data nantinya.
const loadData = async () => {
    try {
        const response = await api.get('/admin-sekolah/pelaporan')
        if (response.data && response.data.pelaporan) {
            const data = JSON.parse(response.data.pelaporan)
            form.value = { ...form.value, ...data }
        }
    } catch (error) {
        console.error('Gagal memuat data pelaporan', error)
    }
}

const submitData = async () => {
    isLoading.value = true
    try {
        await api.post('/admin-sekolah/pelaporan', { pelaporan: JSON.stringify(form.value) })
        
        toast.success('Data pelaporan berhasil disimpan')
    } catch (error) {
        toast.error('Terjadi kesalahan saat menyimpan data.')
        console.error(error)
    } finally {
        isLoading.value = false
    }
}

const cetakPelaporan = () => {
    window.open('/admin-sekolah/cetak-pelaporan', '_blank')
}

onMounted(() => {
    loadData()
})
</script>
