<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Dokumentasi Pelaksanaan</h2>
        <p class="text-sm text-gray-500">Unggah foto-foto kegiatan Pemilos (kampanye, pencoblosan, dll) untuk arsip instansi.</p>
      </div>
      <BaseButton variant="primary" @click="openUploadModal" class="!py-2 shadow-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Upload Foto Baru
      </BaseButton>
    </div>

    <!-- Gallery Grid -->
    <div v-if="isLoading" class="py-12 text-center text-gray-500 bg-white rounded-lg border">
      Memuat galeri dokumentasi...
    </div>
    
    <div v-else-if="items.length === 0" class="py-20 text-center bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
      </svg>
      <h3 class="text-lg font-bold text-gray-600 mb-1">Belum Ada Dokumentasi</h3>
      <p class="text-sm text-gray-400">Silakan upload foto kegiatan untuk ditampilkan di sini.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div v-for="item in items" :key="item.id" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition duration-200">
        <!-- Image Container -->
        <div class="relative h-48 bg-gray-100 flex items-center justify-center overflow-hidden">
          <img v-if="item.foto_url" :src="item.foto_url" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
          <span v-else class="text-gray-400">Broken Image</span>
          
          <!-- Delete Overlay -->
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
            <button @click="deleteFoto(item.id)" class="bg-red-600 hover:bg-red-700 text-white p-3 rounded-full shadow-lg transform translate-y-4 group-hover:translate-y-0 transition duration-300" title="Hapus Foto">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </div>
        </div>
        
        <!-- Info Footer -->
        <div class="p-3 bg-white flex justify-between items-center border-t border-gray-100">
          <div class="text-xs text-gray-500 font-mono flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ formatTime(item.created_at) }}
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Upload -->
    <BaseModal v-model="isUploadModalOpen" title="Upload Dokumentasi Baru" max-width="md">
      <form @submit.prevent="submitUpload" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Foto Kegiatan</label>
          <div class="border-2 border-dashed border-indigo-200 rounded-lg p-6 text-center hover:bg-indigo-50 transition-colors"
               :class="{'bg-indigo-50': fileToUpload}">
            
            <input type="file" ref="fileInput" @change="handleFileChange" accept="image/jpeg,image/png,image/jpg" class="hidden">
            
            <div v-if="!fileToUpload">
              <svg class="mx-auto h-12 w-12 text-indigo-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="mt-4 flex text-sm text-gray-600 justify-center">
                <button type="button" @click="$refs.fileInput.click()" class="font-bold text-indigo-600 hover:text-indigo-500 focus:outline-none">
                  Jelajahi File
                </button>
                <p class="pl-1">atau drag and drop</p>
              </div>
              <p class="text-xs text-gray-500 mt-2">PNG, JPG, JPEG maks 5MB</p>
            </div>
            
            <!-- Preview terpilih -->
            <div v-else class="relative">
              <img :src="photoPreview" class="mx-auto h-40 object-contain rounded" />
              <button type="button" @click.prevent="clearFile" class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-100 text-red-600 hover:bg-red-200 rounded-full p-1 shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
              <p class="text-xs font-mono text-indigo-600 mt-2 break-words">{{ fileToUpload.name }} ({{ (fileToUpload.size / 1024 / 1024).toFixed(1) }} MB)</p>
            </div>
            
          </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
          <BaseButton type="button" variant="secondary" @click="isUploadModalOpen = false">Batal</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting" :disabled="!fileToUpload">Mulai Upload Foto</BaseButton>
        </div>
      </form>
    </BaseModal>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseModal from '../../components/BaseModal.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const items = ref([]);
const isLoading = ref(false);

const isUploadModalOpen = ref(false);
const isSubmitting = ref(false);
const fileInput = ref(null);
const fileToUpload = ref(null);
const photoPreview = ref(null);

onMounted(() => {
  fetchDokumentasi();
});

function formatTime(datetime) {
  if (!datetime) return '-';
  const date = new Date(datetime);
  return date.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

async function fetchDokumentasi() {
  isLoading.value = true;
  try {
    const res = await api.get('/admin-sekolah/dokumentasi');
    items.value = res.data.data;
  } catch (error) {
    toast.error('Gagal mengambil galeri dokumentasi');
  } finally {
    isLoading.value = false;
  }
}

function openUploadModal() {
  clearFile();
  isUploadModalOpen.value = true;
}

function handleFileChange(event) {
  const file = event.target.files[0];
  if (!file) {
    clearFile();
    return;
  }
  
  if (file.size > 5 * 1024 * 1024) {
    toast.error('Ukuran file foto maksimal 5MB');
    event.target.value = '';
    return;
  }
  
  fileToUpload.value = file;
  photoPreview.value = URL.createObjectURL(file);
}

function clearFile() {
  if (fileInput.value) fileInput.value.value = '';
  fileToUpload.value = null;
  if (photoPreview.value) {
    URL.revokeObjectURL(photoPreview.value);
    photoPreview.value = null;
  }
}

async function submitUpload() {
  if (!fileToUpload.value) return;

  const formData = new FormData();
  formData.append('foto', fileToUpload.value);

  isSubmitting.value = true;
  try {
    await api.post('/admin-sekolah/dokumentasi', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    toast.success('Foto dokumentasi berhasil diupload');
    isUploadModalOpen.value = false;
    clearFile();
    fetchDokumentasi();
  } catch (error) {
    if (error.response?.data?.message) {
      toast.error(error.response.data.message);
    } else {
      toast.error('Gagal mengupload foto');
    }
  } finally {
    isSubmitting.value = false;
  }
}

async function deleteFoto(id) {
  if (!confirm('Hapus foto dokumentasi ini secara permanen?')) return;
  
  try {
    await api.delete(`/admin-sekolah/dokumentasi/${id}`);
    toast.success('Foto berhasil dihapus');
    fetchDokumentasi();
  } catch (error) {
    toast.error('Gagal menghapus foto');
  }
}
</script>
