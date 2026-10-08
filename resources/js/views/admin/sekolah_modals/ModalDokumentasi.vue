<template>
    <BaseModal v-model="isOpen" title="Monitoring Dokumentasi" max-width="5xl">
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
                Memuat data dokumentasi...
            </div>
            
            <div v-else-if="errorMessage" class="p-12 text-center text-red-600 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="text-lg font-bold mb-2">Perhatian</h3>
                <p>{{ errorMessage }}</p>
            </div>
            
            <div v-else-if="dokumentasiData && dokumentasiData.length > 0" class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="dok in dokumentasiData" :key="dok.id" class="border border-gray-200 rounded-lg overflow-hidden shadow-sm group">
                        <div class="aspect-w-16 aspect-h-12 bg-gray-100 overflow-hidden relative">
                            <a :href="dok.foto_url" target="_blank" class="block cursor-pointer">
                                <img :src="dok.foto_url" :alt="'Dokumentasi ' + dok.id" class="object-cover w-full h-48 group-hover:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition flex items-center justify-center">
                                    <span class="text-white opacity-0 group-hover:opacity-100 font-medium px-3 py-1 bg-black bg-opacity-50 rounded-lg">Perbesar</span>
                                </div>
                            </a>
                        </div>
                        <div class="p-3 bg-gray-50 text-xs text-gray-500 flex justify-between items-center border-t border-gray-200">
                            <span>ID: {{ dok.id }}</span>
                            <span>{{ formatDate(dok.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </BaseModal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import api from '../../../services/api';
import BaseModal from '../../../components/BaseModal.vue';
import moment from 'moment';
import 'moment/locale/id';

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
const dokumentasiData = ref([]);
const errorMessage = ref('');

const fetchData = async () => {
    if (!props.npsn || !props.tahun) return;
    
    isLoading.value = true;
    errorMessage.value = '';
    dokumentasiData.value = [];
    
    try {
        const response = await api.get(`/admin/data-sekolah/${props.npsn}/dokumentasi?tahun=${props.tahun}`);
        if (response.data.success) {
            dokumentasiData.value = response.data.data;
        }
    } catch (error) {
        if (error.response?.status === 404) {
            errorMessage.value = error.response.data.message || 'Belum ada dokumentasi untuk sekolah ini';
        } else {
            errorMessage.value = 'Terjadi kesalahan saat memuat data dokumentasi.';
        }
    } finally {
        isLoading.value = false;
    }
};

const formatDate = (date) => {
    if (!date) return '-';
    return moment(date).locale('id').format('DD MMM YYYY, HH:mm');
};

watch(() => props.modelValue, (newVal) => {
    if (newVal && props.npsn) {
        fetchData();
    }
});
</script>
