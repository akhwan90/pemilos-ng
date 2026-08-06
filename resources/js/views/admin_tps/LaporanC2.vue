<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Formulir C2 (Kejadian Khusus)</h1>
            <button type="button" @click="cetakC2" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Form C2
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="max-w-4xl mx-auto">
                <div class="mb-8 border-b border-gray-100 pb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-2">Catatan Kejadian Khusus dan/atau Pernyataan Keberatan Saksi</h2>
                    <p class="text-gray-500 text-sm">
                        Catat segala kejadian khusus atau pernyataan keberatan yang diajukan oleh saksi selama pelaksanaan pemungutan dan penghitungan suara di TPS. Data ini akan dilampirkan pada Berita Acara.
                    </p>
                </div>

                <form @submit.prevent="saveC2" class="space-y-8">
                    <!-- Toggle Kejadian Khusus -->
                    <div class="flex items-center gap-6 p-4 rounded-xl" :class="form.ada_kejadian ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-200'">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">Apakah terdapat kejadian khusus atau keberatan saksi?</h3>
                            <p class="text-sm text-gray-500 mt-1">Pilih "Ya" jika terdapat insiden atau keberatan, dan catat rinciannya di bawah.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" v-model="form.ada_kejadian" :value="false" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-2 text-gray-700 font-medium">Tidak Ada (Nihil)</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" v-model="form.ada_kejadian" :value="true" class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="ml-2 text-gray-700 font-medium">Ya, Ada</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Form if ada_kejadian is true -->
                    <div v-if="form.ada_kejadian" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-md font-bold text-gray-800">Daftar Kejadian / Keberatan</h3>
                            <button type="button" @click="tambahKejadian" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Catatan
                            </button>
                        </div>

                        <div v-if="form.kejadian.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200 border-dashed">
                            <p class="text-sm text-gray-500">Belum ada rincian kejadian. Klik "Tambah Catatan" untuk mulai menulis.</p>
                        </div>

                        <div v-for="(item, index) in form.kejadian" :key="index" class="p-5 border border-gray-200 rounded-lg bg-white shadow-sm relative">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                                <h4 class="text-sm font-bold text-gray-700">Kejadian #{{ index + 1 }}</h4>
                                <button type="button" @click="hapusKejadian(index)" class="text-gray-400 hover:text-red-600 focus:outline-none" title="Hapus Catatan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Kejadian (Jam)</label>
                                    <input type="time" v-model="item.waktu" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pelapor / Pihak yang Mengajukan Keberatan</label>
                                    <input type="text" v-model="item.pelapor" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Nama saksi / pemilih / pelapor (jika ada)">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Uraian Kejadian Khusus / Keberatan</label>
                                <textarea v-model="item.uraian" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Jelaskan secara singkat kejadian atau keberatan yang terjadi..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Info Message for NIHIL -->
                    <div v-if="!form.ada_kejadian" class="p-4 bg-blue-50 text-blue-800 rounded-lg text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p>Catatan kejadian khusus akan dicetak sebagai <strong>NIHIL</strong> pada dokumen hasil pemilihan.</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Formulir C2
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();
const loading = ref(false);

const form = ref({
    ada_kejadian: false,
    kejadian: []
});

onMounted(() => {
    fetchC2Config();
});

async function fetchC2Config() {
    try {
        const res = await api.get('/admin-tps/c2');
        if (res.data.success && res.data.data && res.data.data.c2_config) {
            form.value.ada_kejadian = res.data.data.c2_config.ada_kejadian === true || res.data.data.c2_config.ada_kejadian === 'true';
            form.value.kejadian = res.data.data.c2_config.kejadian || [];
        }
    } catch (error) {
        console.error('Gagal memuat config C2:', error);
    }
}

async function saveC2() {
    loading.value = true;
    try {
        const res = await api.post('/admin-tps/c2', form.value);
        if (res.data.success) {
            toast.success('Formulir C2 (Kejadian Khusus) berhasil disimpan.');
        }
    } catch (error) {
        console.error('Gagal menyimpan:', error);
        toast.error('Terjadi kesalahan saat menyimpan formulir C2.');
    } finally {
        loading.value = false;
    }
}

function tambahKejadian() {
    form.value.kejadian.push({ waktu: '', pelapor: '', uraian: '' });
}

function hapusKejadian(index) {
    if (confirm('Hapus catatan kejadian ini?')) {
        form.value.kejadian.splice(index, 1);
    }
}

function cetakC2() {
    window.open('/admin-tps/print-c2', '_blank');
}
</script>