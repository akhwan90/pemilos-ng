<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">Pengaturan Aplikasi</h1>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 max-w-3xl">
      <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Pengaturan Tanda Tangan PPID / Daftar Hadir</h2>
      
      <div v-if="loading" class="flex justify-center py-8">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>

      <form v-else @submit.prevent="saveSettings" class="space-y-5">
        
        <div class="border-b pb-4 mb-4">
          <h3 class="text-md font-semibold text-gray-700 mb-3">Kontak Aduan & Aspirasi</h3>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Aduan</label>
          <input 
            type="text" 
            v-model="form.nomor_whatsapp_aduan" 
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
          />
          <p class="text-xs text-gray-500 mt-1">Contoh: 6281234567890 (Gunakan kode negara 62 tanpa +)</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Penerima Kunjungan</label>
          <input 
            type="text" 
            v-model="form.penerima_kunjungan_jabatan" 
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
            required
          />
          <p class="text-xs text-gray-500 mt-1">Contoh: Pejabat Pengelola Informasi dan Dokumentasi</p>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima Kunjungan</label>
          <input 
            type="text" 
            v-model="form.penerima_kunjungan_nama" 
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
            required
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">NIP Penerima Kunjungan</label>
          <input 
            type="text" 
            v-model="form.penerima_kunjungan_nip" 
            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
          />
          <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak menggunakan NIP</p>
        </div>

        <div class="pt-4 flex items-center justify-end border-t border-gray-100">
          <span v-if="successMessage" class="text-green-600 text-sm mr-4">{{ successMessage }}</span>
          <button 
            type="submit" 
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 flex items-center"
            :disabled="saving"
          >
            <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Simpan Pengaturan
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const loading = ref(true);
const saving = ref(false);
const successMessage = ref('');

const form = ref({
  penerima_kunjungan_jabatan: '',
  penerima_kunjungan_nama: '',
  penerima_kunjungan_nip: '',
  nomor_whatsapp_aduan: '',
});

async function loadSettings() {
  loading.value = true;
  try {
    const res = await api.get('/admin/settings');
    if (res.data.success) {
      form.value = {
        penerima_kunjungan_jabatan: res.data.data.penerima_kunjungan_jabatan || '',
        penerima_kunjungan_nama: res.data.data.penerima_kunjungan_nama || '',
        penerima_kunjungan_nip: res.data.data.penerima_kunjungan_nip || '',
        nomor_whatsapp_aduan: res.data.data.nomor_whatsapp_aduan || '',
      };
    }
  } catch (error) {
    console.error('Error loading settings:', error);
    alert('Gagal memuat pengaturan');
  } finally {
    loading.value = false;
  }
}

async function saveSettings() {
  saving.value = true;
  successMessage.value = '';
  
  try {
    const res = await api.post('/admin/settings', form.value);
    if (res.data.success) {
      successMessage.value = 'Pengaturan berhasil disimpan!';
      setTimeout(() => {
        successMessage.value = '';
      }, 3000);
    }
  } catch (error) {
    console.error('Error saving settings:', error);
    alert('Gagal menyimpan pengaturan');
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  loadSettings();
});
</script>