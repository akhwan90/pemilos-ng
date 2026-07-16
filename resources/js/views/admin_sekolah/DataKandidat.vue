<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Data Calon Kandidat</h2>
        <p class="text-sm text-gray-500">Kelola informasi, visi misi, dan foto calon ketua (Kandidat).</p>
      </div>
    </div>

    <!-- Tabel List Kandidat (View Mode) -->
    <BaseCard v-if="!isEditing" class="!p-0">
      <div class="overflow-x-auto">
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
              <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada kandidat. (Hubungi Super Admin jika ingin menambah kandidat baru)</td>
            </tr>
            <tr v-else v-for="k in kandidatList" :key="k.id" class="bg-white border-b hover:bg-gray-50">
              <td class="px-4 py-3 text-center text-xl font-black text-gray-400">{{ k.no }}</td>
              <td class="px-4 py-3">
                <img v-if="k.photo_url" :src="k.photo_url" alt="Foto" class="w-16 h-20 object-cover rounded shadow border" />
                <div v-else class="w-16 h-20 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">No Pic</div>
              </td>
              <td class="px-4 py-3">
                <div class="font-bold text-lg text-indigo-700">{{ k.nama }}</div>
                <div class="text-xs text-gray-500 mb-2">NISN: {{ k.nisn }}</div>
                <div v-if="k.kampanye" class="text-sm italic bg-yellow-50 text-yellow-800 p-2 border border-yellow-200 rounded">"{{ k.kampanye }}"</div>
              </td>
              <td class="px-4 py-3 text-center align-middle">
                <div class="flex flex-col gap-2 justify-center items-center">
                  <BaseButton @click="editKandidat(k)" variant="primary" class="!py-1 !px-3 text-xs w-20">Edit</BaseButton>
                  <BaseButton @click="deleteKandidat(k.id)" variant="danger" class="!py-1 !px-3 text-xs w-20">Hapus</BaseButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </BaseCard>

    <!-- Form Edit Kandidat -->
    <BaseCard v-else class="!p-0 border-indigo-200 shadow-md">
      <div class="bg-indigo-50 border-b border-indigo-100 p-4 flex justify-between items-center">
        <h3 class="font-bold text-indigo-800 flex items-center gap-2">
          <span class="bg-indigo-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">{{ form.no }}</span> 
          Edit Data Kandidat
        </h3>
        <button @click="cancelEdit" class="text-sm font-semibold text-gray-500 hover:text-gray-800 bg-white px-3 py-1 rounded border shadow-sm">Batal / Kembali</button>
      </div>

      <form @submit.prevent="submitForm" class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          
          <!-- Kolom Kiri -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="form.nama" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
              <input v-model="form.nisn" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Slogan / Jargon Kampanye</label>
              <input v-model="form.kampanye" type="text" placeholder="Cth: Muda Mudi Berkarya" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
            </div>
            
            <div class="pt-2 border-t mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Upload Foto Kandidat Baru</label>
              <input type="file" ref="photoInput" @change="handlePhotoChange" accept="image/jpeg,image/png,image/jpg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mb-2">
              <div v-if="photoPreview" class="mt-2">
                <p class="text-xs text-gray-500 mb-1">Preview Foto Baru:</p>
                <img :src="photoPreview" class="w-32 h-40 object-cover rounded border border-gray-300 shadow-sm" />
              </div>
              <div v-else-if="form.existing_photo_url" class="mt-2">
                <p class="text-xs text-gray-500 mb-1">Foto Saat Ini:</p>
                <img :src="form.existing_photo_url" class="w-32 h-40 object-cover rounded border border-gray-300 shadow-sm" />
              </div>
            </div>
          </div>

          <!-- Kolom Kanan (Textareas) -->
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Visi</label>
              <textarea v-model="form.visi" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Misi</label>
              <textarea v-model="form.misi" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Program Kerja</label>
              <textarea v-model="form.proker" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pengalaman</label>
                <textarea v-model="form.pengalaman" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500"></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prestasi</label>
                <textarea v-model="form.prestasi" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500"></textarea>
              </div>
            </div>
          </div>

        </div>

        <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-4 bg-gray-50 -mx-6 -mb-6 p-4 rounded-b-lg">
          <BaseButton type="button" variant="secondary" @click="cancelEdit">Batalkan</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">Simpan Profil Kandidat</BaseButton>
        </div>
      </form>
    </BaseCard>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const toast = useToast();

const kandidatList = ref([]);
const isLoading = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);

const photoInput = ref(null);
const photoPreview = ref(null);
const fileToUpload = ref(null);

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
  existing_photo_url: null
});

onMounted(() => {
  fetchKandidat();
});

async function fetchKandidat() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin-sekolah/kandidat`);
    kandidatList.value = res.data.data;
  } catch (error) {
    toast.error('Gagal mengambil data kandidat');
  } finally {
    isLoading.value = false;
  }
}

function handlePhotoChange(event) {
  const file = event.target.files[0];
  if (!file) {
    fileToUpload.value = null;
    photoPreview.value = null;
    return;
  }
  
  if (file.size > 2 * 1024 * 1024) {
    toast.error('Ukuran file foto maksimal 2MB');
    event.target.value = '';
    return;
  }
  
  fileToUpload.value = file;
  photoPreview.value = URL.createObjectURL(file);
}

async function editKandidat(k) {
  isEditing.value = true;
  photoPreview.value = null;
  fileToUpload.value = null;
  
  // Ambil detail kandidat (karena list mungkin tidak menarik visi misi yg panjang)
  try {
    const res = await api.get(`/admin-sekolah/kandidat/${k.id}`);
    const data = res.data.data;
    
    form.value = {
      id: data.id,
      no: data.no,
      nama: data.nama || '',
      nisn: data.nisn || '',
      kampanye: data.kampanye || '',
      visi: data.visi || '',
      misi: data.misi || '',
      proker: data.proker || '',
      pengalaman: data.pengalaman || '',
      prestasi: data.prestasi || '',
      existing_photo_url: data.photo_url || null
    };
  } catch (e) {
    toast.error('Gagal mengambil detail kandidat');
    cancelEdit();
  }
}

function cancelEdit() {
  isEditing.value = false;
  if (photoInput.value) {
    photoInput.value.value = '';
  }
  photoPreview.value = null;
  fileToUpload.value = null;
}

async function submitForm() {
  isSubmitting.value = true;
  
  const formData = new FormData();
  formData.append('nama', form.value.nama);
  formData.append('nisn', form.value.nisn);
  formData.append('kampanye', form.value.kampanye);
  formData.append('visi', form.value.visi);
  formData.append('misi', form.value.misi);
  formData.append('proker', form.value.proker);
  formData.append('pengalaman', form.value.pengalaman);
  formData.append('prestasi', form.value.prestasi);
  
  if (fileToUpload.value) {
    formData.append('photo', fileToUpload.value);
  }

  try {
    await api.post(`/admin-sekolah/kandidat/${form.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });
    
    toast.success(`Kandidat No ${form.value.no} berhasil diperbarui`);
    isEditing.value = false;
    fetchKandidat();
  } catch (error) {
    toast.error('Gagal memperbarui kandidat');
  } finally {
    isSubmitting.value = false;
  }
}

async function deleteKandidat(id) {
  if (!confirm('Anda yakin ingin menghapus kandidat ini secara permanen?')) return;
  try {
    await api.delete(`/admin-sekolah/kandidat/${id}`);
    toast.success('Kandidat berhasil dihapus');
    fetchKandidat();
  } catch (error) {
    toast.error('Gagal menghapus kandidat');
  }
}
</script>
