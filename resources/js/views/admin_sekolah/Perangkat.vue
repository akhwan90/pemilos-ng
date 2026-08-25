<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Anggota PPO</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <div class="max-w-3xl mx-auto">
                <div class="mb-8">
                    <p class="text-gray-500">
                        Pengaturan susunan Panitia Pemilihan OSIS (PPO) pada periode ini. Data ini akan ditampilkan pada dokumen Berita Acara.
                    </p>
                </div>

                <form @submit.prevent="savePerangkat" class="space-y-8">
                    <!-- Ketua -->
                    <div class="bg-indigo-50/50 border border-indigo-100 p-6 rounded-xl">
                        <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Ketua PPO
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" v-model="form.ketua.nama" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Masukkan nama ketua">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIS / Identitas</label>
                                <input type="text" v-model="form.ketua.identitas" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Masukkan NIS/NIK">
                            </div>
                        </div>
                    </div>

                    <!-- Anggota 1 -->
                    <div class="bg-gray-50 border border-gray-200 p-6 rounded-xl">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Anggota PPO 1
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" v-model="form.anggota_1.nama" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Masukkan nama anggota 1">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIS / Identitas</label>
                                <input type="text" v-model="form.anggota_1.identitas" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Masukkan NIS/NIK">
                            </div>
                        </div>
                    </div>

                    <!-- Anggota 2 -->
                    <div class="bg-gray-50 border border-gray-200 p-6 rounded-xl">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Anggota PPO 2
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" v-model="form.anggota_2.nama" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Masukkan nama anggota 2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NIS / Identitas</label>
                                <input type="text" v-model="form.anggota_2.identitas" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Masukkan NIS/NIK">
                            </div>
                        </div>
                    </div>

                    <!-- Saksi Paslon -->
                    <div class="bg-gray-50 border border-gray-200 p-6 rounded-xl">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Saksi Pasangan Calon
                            </h3>
                            <button type="button" @click="tambahSaksi" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Saksi
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Tambahkan saksi-saksi dari setiap utusan paslon jika diperlukan.</p>
                        
                        <div class="space-y-6">
                            <div v-if="form.saksi.length === 0" class="text-center py-6 bg-white rounded-lg border border-gray-200 border-dashed">
                                <p class="text-sm text-gray-500">Belum ada saksi yang ditambahkan.</p>
                            </div>

                            <div v-for="(saksiItem, index) in form.saksi" :key="index" class="p-4 border border-gray-200 rounded-lg bg-white relative">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-600">Saksi {{ index + 1 }}</h4>
                                    <button type="button" @click="hapusSaksi(index)" class="text-gray-400 hover:text-red-600 focus:outline-none" title="Hapus Saksi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                        <input type="text" v-model="saksiItem.nama" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Nama Saksi">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">NIS / Identitas</label>
                                        <input type="text" v-model="saksiItem.identitas" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="NIS/NIK">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Utusan Paslon</label>
                                        <input type="text" v-model="saksiItem.paslon" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Cth: Paslon 1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200 flex justify-end">
                        <button 
                            type="submit" 
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Perangkat
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
import { useAuthStore } from '../../stores/auth';
import { useToast } from '../../composables/useToast';

const authStore = useAuthStore();
const toast = useToast();

const loading = ref(false);
const tpsName = ref(authStore.user?.nama || 'TPS');

const form = ref({
    ketua: { nama: '', identitas: '' },
    anggota_1: { nama: '', identitas: '' },
    anggota_2: { nama: '', identitas: '' },
    saksi: [
        { nama: '', identitas: '', paslon: '' },
        { nama: '', identitas: '', paslon: '' }
    ]
});

onMounted(() => {
    fetchPerangkat();
});

async function fetchPerangkat() {
    try {
        const res = await api.get('/admin-sekolah/perangkat');
        if (res.data.success && res.data.data) {
            form.value = {
                ketua: res.data.data.ketua || { nama: '', identitas: '' },
                anggota_1: res.data.data.anggota_1 || { nama: '', identitas: '' },
                anggota_2: res.data.data.anggota_2 || { nama: '', identitas: '' },
                saksi: res.data.data.saksi || [
                    { nama: '', identitas: '', paslon: '' },
                    { nama: '', identitas: '', paslon: '' }
                ]
            };
        }
    } catch (error) {
        console.error('Gagal memuat perangkat TPS:', error);
    }
}

async function savePerangkat() {
    loading.value = true;
    try {
        const res = await api.post('/admin-sekolah/perangkat', form.value);
        if (res.data.success) {
            toast.success('Data Anggota PPO berhasil disimpan.');
        }
    } catch (error) {
        console.error('Gagal menyimpan:', error);
        toast.error('Terjadi kesalahan saat menyimpan.');
    } finally {
        loading.value = false;
    }
}

function tambahSaksi() {
    form.value.saksi.push({ nama: '', identitas: '', paslon: '' });
}

function hapusSaksi(index) {
    if (confirm('Hapus saksi ini?')) {
        form.value.saksi.splice(index, 1);
    }
}
</script>