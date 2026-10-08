<template>
    <BaseModal v-model="isOpen" title="Monitoring Hasil Vote" max-width="4xl">
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
                Memuat data hasil vote...
            </div>
            
            <div v-else-if="errorMessage" class="p-12 text-center text-red-600 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="text-lg font-bold mb-2">Perhatian</h3>
                <p>{{ errorMessage }}</p>
            </div>
            
            <div v-else-if="hasilData && hasilData.length > 0" class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                        <tr>
                            <th rowspan="2" class="px-4 py-3 border-r align-middle">No</th>
                            <th rowspan="2" class="px-4 py-3 border-r align-middle">Nama TPS</th>
                            <th colspan="4" class="px-4 py-2 border-r border-b text-center">Statistik</th>
                            <th :colspan="Object.keys(hasilData[0].hasil.perolehan_paslon || {}).length" class="px-4 py-2 border-b text-center">Perolehan Paslon</th>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 border-r text-center">DPT</th>
                            <th class="px-4 py-2 border-r text-center">Suara Masuk</th>
                            <th class="px-4 py-2 border-r text-center">Sah</th>
                            <th class="px-4 py-2 border-r text-center">Tidak Sah</th>
                            
                            <template v-if="hasilData[0] && hasilData[0].hasil.perolehan_paslon">
                                <th v-for="(paslon, key) in hasilData[0].hasil.perolehan_paslon" :key="key" class="px-4 py-2 border-r text-center whitespace-nowrap">
                                    {{ paslon.nama }} (No. {{ paslon.no }})
                                </th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(tps, index) in hasilData" :key="index" class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-center border-r">{{ index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 border-r whitespace-nowrap">
                                {{ tps.nama_tps }}
                                <span v-if="tps.is_tps_luar_sekolah" class="ml-2 px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs">Luar Sekolah</span>
                            </td>
                            <td class="px-4 py-3 text-center border-r">{{ tps.hasil.statistik?.total_dpt || 0 }}</td>
                            <td class="px-4 py-3 text-center border-r font-bold text-indigo-600">{{ tps.hasil.statistik?.suara_masuk || 0 }}</td>
                            <td class="px-4 py-3 text-center border-r text-green-600">{{ tps.hasil.statistik?.suara_sah || 0 }}</td>
                            <td class="px-4 py-3 text-center border-r text-red-600">{{ tps.hasil.statistik?.suara_tidak_sah || 0 }}</td>
                            
                            <template v-if="tps.hasil.perolehan_paslon">
                                <td v-for="(paslon, key) in tps.hasil.perolehan_paslon" :key="key" class="px-4 py-3 text-center font-bold text-gray-800 border-r">
                                    {{ paslon.total }}
                                </td>
                            </template>
                        </tr>
                        
                        <!-- Row Total Akhir -->
                        <tr class="bg-indigo-50 font-bold text-gray-900">
                            <td colspan="2" class="px-4 py-3 text-right border-r uppercase">Total Keseluruhan</td>
                            <td class="px-4 py-3 text-center border-r">{{ totalGlobal.dpt }}</td>
                            <td class="px-4 py-3 text-center border-r text-indigo-700">{{ totalGlobal.masuk }}</td>
                            <td class="px-4 py-3 text-center border-r text-green-700">{{ totalGlobal.sah }}</td>
                            <td class="px-4 py-3 text-center border-r text-red-700">{{ totalGlobal.tidaksah }}</td>
                            <template v-if="hasilData.length > 0 && hasilData[0].hasil.perolehan_paslon">
                                <td v-for="(paslon, key) in hasilData[0].hasil.perolehan_paslon" :key="key" class="px-4 py-3 text-center border-r">
                                    {{ calculateTotalPaslon(key) }}
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State if setting exists but JSON array is empty -->
            <div v-else-if="!isLoading" class="p-12 text-center text-gray-500 bg-white border border-gray-200 rounded-lg">
                Tidak ada data TPS yang dapat ditampilkan.
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
const hasilData = ref([]);
const errorMessage = ref('');

const fetchData = async () => {
    if (!props.npsn || !props.tahun) return;
    
    isLoading.value = true;
    errorMessage.value = '';
    hasilData.value = [];
    
    try {
        const response = await api.get(`/admin/data-sekolah/${props.npsn}/hasil-vote?tahun=${props.tahun}`);
        if (response.data.success) {
            hasilData.value = response.data.data;
        }
    } catch (error) {
        if (error.response?.status === 404) {
            errorMessage.value = error.response.data.message || 'Pemilos belum diselesaikan';
        } else {
            errorMessage.value = 'Terjadi kesalahan saat memuat data hasil vote.';
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

const calculateTotalPaslon = (paslonId) => {
    return hasilData.value.reduce((sum, tps) => {
        const paslon = tps.hasil.perolehan_paslon?.[paslonId];
        return sum + (paslon ? parseInt(paslon.total || 0) : 0);
    }, 0);
};

const totalGlobal = computed(() => {
    return hasilData.value.reduce((acc, tps) => {
        acc.dpt += parseInt(tps.hasil.statistik?.total_dpt || 0);
        acc.masuk += parseInt(tps.hasil.statistik?.suara_masuk || 0);
        acc.sah += parseInt(tps.hasil.statistik?.suara_sah || 0);
        acc.tidaksah += parseInt(tps.hasil.statistik?.suara_tidak_sah || 0);
        return acc;
    }, { dpt: 0, masuk: 0, sah: 0, tidaksah: 0 });
});

</script>
