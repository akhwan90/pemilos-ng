<template>
    <BaseModal v-model="isOpen" title="Monitoring Pelaporan" max-width="4xl">
        <div v-if="npsn" class="space-y-4">
            
            <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Tahun Aktif</h3>
                    <p class="text-lg font-bold text-indigo-600">{{ tahun }}</p>
                </div>
                <div class="text-right">
                    <button @click="fetchData" class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-200 transition">
                        Refresh Data
                    </button>
                </div>
            </div>

            <div v-if="isLoading" class="p-12 text-center text-gray-500 bg-white border border-gray-200 rounded-lg">
                Memuat data pelaporan...
            </div>
            
            <div v-else-if="errorMessage" class="p-12 text-center text-red-600 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="text-lg font-bold mb-2">Perhatian</h3>
                <p>{{ errorMessage }}</p>
            </div>
            
            <div v-else-if="pelaporanData" class="bg-white border border-gray-200 rounded-lg overflow-hidden text-sm">
                <table class="w-full text-left border-collapse">
                    <tbody>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 w-1/3 border-r">Nomor Laporan</th><td class="px-4 py-3">{{ pelaporanData.nomor_laporan }}</td></tr>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 border-r">Tahapan yang diawasi</th><td class="px-4 py-3">{{ pelaporanData.tahapan }}</td></tr>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 border-r">Nama Pelaksana Tugas</th><td class="px-4 py-3">{{ pelaporanData.pelaksana }}</td></tr>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 border-r">Jabatan</th><td class="px-4 py-3">{{ pelaporanData.jabatan }}</td></tr>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 border-r">Nama Sekolah</th><td class="px-4 py-3">{{ pelaporanData.nama_sekolah }}</td></tr>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 border-r">Tujuan</th><td class="px-4 py-3">{{ pelaporanData.tujuan }}</td></tr>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 border-r">Sasaran</th><td class="px-4 py-3">{{ pelaporanData.sasaran }}</td></tr>
                        <tr class="border-b"><th class="px-4 py-3 bg-gray-50 border-r">Waktu dan Tempat</th><td class="px-4 py-3">{{ pelaporanData.waktu_tempat }}</td></tr>
                    </tbody>
                </table>
                <div class="px-4 py-3 bg-gray-50 font-bold border-b">Uraian Singkat Hasil Pengawasan</div>
                <div class="p-4 text-justify whitespace-pre-wrap leading-relaxed">{{ pelaporanData.uraian }}</div>
            </div>

        </div>
    </BaseModal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import api from '../../../services/api';
import BaseModal from '../../../components/BaseModal.vue';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    npsn: {
        type: String,
        default: null
    },
    tahun: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const isLoading = ref(false);
const pelaporanData = ref(null);
const errorMessage = ref('');

const fetchData = async () => {
    if (!props.npsn || !props.tahun) return;
    
    isLoading.value = true;
    errorMessage.value = '';
    pelaporanData.value = null;
    
    try {
        const response = await api.get(`/admin/data-sekolah/${props.npsn}/pelaporan?tahun=${props.tahun}`);
        if (response.data.success) {
            pelaporanData.value = response.data.data;
        }
    } catch (error) {
        if (error.response?.status === 404) {
            errorMessage.value = error.response.data.message || 'Laporan belum dikirimkan oleh sekolah ini';
        } else {
            errorMessage.value = 'Terjadi kesalahan saat memuat data pelaporan.';
        }
    } finally {
        isLoading.value = false;
    }
};

watch(() => props.modelValue, (newVal) => {
    if (newVal && props.npsn) {
        fetchData();
    }
});
</script>
