<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Selesai Pemilihan</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-6 md:p-8">
            <div class="text-center max-w-2xl mx-auto">
                <!-- Icon Status -->
                <div 
                    class="mx-auto flex items-center justify-center h-20 w-20 rounded-full mb-6"
                    :class="isSelesai ? 'bg-green-100' : 'bg-blue-100'"
                >
                    <!-- Icon Centang (Selesai) -->
                    <svg v-if="isSelesai" class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <!-- Icon Waktu / Proses (Belum) -->
                    <svg v-else class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ isSelesai ? 'Pemilihan Telah Selesai' : 'Tutup Proses Pemilihan?' }}
                </h2>

                <div v-if="isSelesai" class="text-gray-500 mb-8 space-y-2">
                    <p>
                        Status pemilihan di TPS Anda ({{ tpsName }}) sudah dinyatakan <strong class="text-gray-700">ditutup</strong> pada sistem. 
                        Tindakan ini tidak bisa dibatalkan secara sepihak.
                    </p>
                    <div class="inline-flex items-center gap-2 mt-4 bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg text-sm font-mono text-gray-600">
                        Waktu Penutupan: {{ formatTime(selesaiWaktu) }}
                    </div>
                </div>

                <div v-else class="text-gray-500 mb-8 space-y-4">
                    <p>
                        Dengan mengklik tombol di bawah, Anda selaku Admin TPS menyatakan bahwa proses pemungutan suara di <strong>{{ tpsName }}</strong> telah resmi berakhir.
                    </p>
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg text-sm text-left">
                        <strong class="block mb-1 font-bold">Peringatan:</strong>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Siswa (pemilih) tidak akan bisa login lagi ke dalam Bilik Suara.</li>
                            <li>Token yang belum digunakan akan otomatis hangus.</li>
                            <li>Sistem akan mencatat waktu penutupan TPS Anda saat ini juga.</li>
                        </ul>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button 
                        v-if="!isSelesai"
                        @click="showConfirmModal = true"
                        :disabled="loading"
                        class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3.5 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg v-if="loading" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Akhiri Pemilihan Sekarang
                    </button>
                    
                    <button 
                        v-else
                        disabled
                        class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3.5 border border-transparent text-base font-medium rounded-xl text-green-700 bg-green-100 cursor-not-allowed"
                    >
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        TPS Sudah Ditutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi -->
        <div v-if="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showConfirmModal = false"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Konfirmasi Akhiri Pemilihan</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menutup proses pemilihan? Tindakan ini <strong>permanen</strong> dan Anda tidak dapat membukanya kembali dari halaman ini. Pastikan semua bilik sudah selesai digunakan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="button" 
                            @click="submitSelesai"
                            :disabled="loading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            Ya, Tutup Pemilihan
                        </button>
                        <button 
                            type="button" 
                            @click="showConfirmModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../../services/api';
import { useAuthStore } from '../../stores/auth';
import moment from 'moment';

const authStore = useAuthStore();
// notifStore diganti alert karena gesit pakai manual atau BaseModal (tapi alert cukup)
const notifStore = {
    showNotification: (msg, type) => {
        alert((type === 'error' ? 'ERROR: ' : 'SUKSES: ') + msg);
    }
};

const loading = ref(false);
const showConfirmModal = ref(false);

const tpsName = ref(authStore.user?.nama || 'TPS Anda');
const isSelesai = ref(false);
const selesaiWaktu = ref(null);

onMounted(() => {
    fetchStatus();
});

async function fetchStatus() {
    try {
        const res = await api.get('/admin-tps/status-pemilihan');
        if (res.data.success) {
            const status = res.data.data;
            if (status && status.selesai_pemilihan_time) {
                isSelesai.value = true;
                selesaiWaktu.value = status.selesai_pemilihan_time;
            }
        }
    } catch (error) {
        console.error('Gagal memuat status TPS', error);
    }
}

async function submitSelesai() {
    loading.value = true;
    try {
        const res = await api.post('/admin-tps/akhiri-pemilihan');
        if (res.data.success) {
            notifStore.showNotification('Pemilihan berhasil diakhiri!', 'success');
            isSelesai.value = true;
            selesaiWaktu.value = res.data.selesai_time || new Date().toISOString();
            showConfirmModal.value = false;
        }
    } catch (error) {
        console.error('Gagal mengakhiri pemilihan', error);
        notifStore.showNotification('Terjadi kesalahan sistem saat mencoba menutup TPS.', 'error');
    } finally {
        loading.value = false;
    }
}

function formatTime(datetime) {
    if (!datetime) return '-';
    // Format standar Indonesia (Misal: 23 Juli 2026, 15:30 WIB)
    return moment(datetime).locale('id').format('DD MMMM YYYY, HH:mm');
}
</script>