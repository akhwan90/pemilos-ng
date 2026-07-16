<template>
  <BaseModal v-model="isOpen" title="Data Kandidat (Calon Ketua)" max-width="5xl">
    <div v-if="npsn" class="space-y-4">
      
      <!-- Tabel List Kandidat (View Mode) -->
      <div v-if="!isEditing" class="overflow-hidden border border-gray-200 rounded-lg">
        <table class="w-full text-sm text-left">
          <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
            <tr>
              <th class="px-4 py-3 w-16 text-center">No Urut</th>
              <th class="px-4 py-3 w-24">Photo</th>
              <th class="px-4 py-3">Nama Lengkap &amp; Kampanye</th>
              <th class="px-4 py-3 w-32 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading" class="bg-white">
              <td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat data kandidat...</td>
            </tr>
            <tr v-else-if="kandidatList.length === 0" class="bg-white">
              <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada kandidat yang di-inputkan oleh Admin Sekolah ini.</td>
            </tr>
            <tr v-else v-for="k in kandidatList" :key="k.id" class="bg-white border-b hover:bg-gray-50">
              <td class="px-4 py-3 text-center text-lg font-bold text-gray-700">{{ k.no }}</td>
              <td class="px-4 py-3">
                <img v-if="k.photo_url" :src="k.photo_url" alt="Foto" class="w-16 h-16 object-cover rounded shadow-sm border" />
                <div v-else class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">No Pic</div>
              </td>
              <td class="px-4 py-3">
                <div class="font-bold text-base">{{ k.nama }}</div>
                <div class="text-xs text-gray-500 mb-1">NISN: {{ k.nisn }}</div>
                <div v-if="k.kampanye" class="text-xs italic bg-yellow-50 text-yellow-800 p-1 rounded">"{{ k.kampanye }}"</div>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex gap-2 justify-center">
                  <button @click="editKandidat(k)" class="text-xs text-blue-600 hover:text-blue-900 bg-blue-50 px-2 py-1 rounded border border-blue-200">Edit</button>
                  <button @click="deleteKandidat(k.id)" class="text-xs text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded border border-red-200">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Form Edit Kandidat -->
      <div v-else class="bg-white p-4 rounded-lg border border-gray-200">
        <div class="flex justify-between items-center mb-4 pb-2 border-b">
          <h4 class="font-bold text-gray-800">Edit Kandidat Nomor Urut {{ form.no }}</h4>
          <button @click="cancelEdit" class="text-sm text-gray-500 hover:text-gray-700">Tutup Form</button>
        </div>
        
        <form @submit.prevent="submitForm" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Kolom Kiri -->
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input v-model="form.nama" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">NISN</label>
                <input v-model="form.nisn" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Slogan Kampanye</label>
                <textarea v-model="form.kampanye" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
              </div>
              
              <div class="border p-3 rounded-lg bg-gray-50">
                <label class="block text-xs font-medium text-gray-700 mb-1">Photo Kandidat</label>
                <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <div v-if="photoPreview || form.photo_url" class="mt-2">
                  <img :src="photoPreview || form.photo_url" class="h-24 w-24 object-cover rounded shadow-sm" />
                </div>
              </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="space-y-4">
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Visi</label>
                <textarea v-model="form.visi" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Misi</label>
                <textarea v-model="form.misi" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Program Kerja</label>
                <textarea v-model="form.proker" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Pengalaman</label>
                  <textarea v-model="form.pengalaman" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">Prestasi</label>
                  <textarea v-model="form.prestasi" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
                </div>
              </div>
            </div>
          </div>
          
          <div class="pt-4 flex justify-end gap-2 border-t">
            <BaseButton type="button" variant="danger" @click="cancelEdit">Batal</BaseButton>
            <BaseButton type="submit" variant="primary" :loading="isSubmitting">Simpan Perubahan</BaseButton>
          </div>
        </form>
      </div>

    </div>
  </BaseModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';
import BaseButton from '../../../components/BaseButton.vue';
import api from '../../../services/api';
import { useToast } from '../../../composables/useToast';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  npsn: { type: [String, Number], default: null }
});

const emit = defineEmits(['update:modelValue']);
const toast = useToast();

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const kandidatList = ref([]);
const isLoading = ref(false);

const isEditing = ref(false);
const isSubmitting = ref(false);
const photoPreview = ref(null);
const filePhoto = ref(null);

const form = ref({
  id: null,
  no: null,
  nama: '',
  nisn: '',
  kampanye: '',
  visi: '',
  misi: '',
  proker: '',
  pengalaman: '',
  prestasi: '',
  photo_url: null
});

watch(() => isOpen.value, (newVal) => {
  if (newVal && props.npsn) {
    isEditing.value = false;
    fetchKandidat();
  }
});

async function fetchKandidat() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin/data-sekolah/${props.npsn}/kandidat`);
    kandidatList.value = res.data.data;
  } catch (error) {
    toast.error('Gagal mengambil data kandidat');
  } finally {
    isLoading.value = false;
  }
}

function editKandidat(k) {
  isEditing.value = true;
  form.value = { ...k };
  photoPreview.value = null;
  filePhoto.value = null;
}

function cancelEdit() {
  isEditing.value = false;
  photoPreview.value = null;
  filePhoto.value = null;
}

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    filePhoto.value = file;
    // Create preview
    const reader = new FileReader();
    reader.onload = e => { photoPreview.value = e.target.result; };
    reader.readAsDataURL(file);
  }
}

async function submitForm() {
  isSubmitting.value = true;
  try {
    const formData = new FormData();
    formData.append('nama', form.value.nama || '');
    formData.append('nisn', form.value.nisn || '');
    formData.append('kampanye', form.value.kampanye || '');
    formData.append('visi', form.value.visi || '');
    formData.append('misi', form.value.misi || '');
    formData.append('proker', form.value.proker || '');
    formData.append('pengalaman', form.value.pengalaman || '');
    formData.append('prestasi', form.value.prestasi || '');
    
    if (filePhoto.value) {
      formData.append('photo', filePhoto.value);
    }
    
    await api.post(`/admin/data-sekolah/${props.npsn}/kandidat/${form.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    toast.success('Data kandidat berhasil diperbarui');
    isEditing.value = false;
    fetchKandidat();
  } catch (error) {
    toast.error('Gagal memperbarui kandidat');
  } finally {
    isSubmitting.value = false;
  }
}

async function deleteKandidat(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus kandidat ini? Semua data terkait dan fotonya akan dihapus.')) return;
  
  try {
    await api.delete(`/admin/data-sekolah/${props.npsn}/kandidat/${id}`);
    toast.success('Kandidat berhasil dihapus');
    fetchKandidat();
  } catch (error) {
    toast.error('Gagal menghapus kandidat');
  }
}
</script>
