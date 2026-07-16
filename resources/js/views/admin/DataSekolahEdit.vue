<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-bold text-gray-800">Edit Data Sekolah</h2>
      <BaseButton variant="secondary" @click="$router.push('/admin/data-sekolah')">Kembali</BaseButton>
    </div>
    
    <BaseCard>
      <div v-if="isLoading" class="p-8 text-center text-gray-500">Memuat data sekolah...</div>
      <form v-else @submit.prevent="submitForm" class="space-y-4">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NPSN</label>
              <input type="text" :value="form.npsn" readonly class="w-full px-3 py-2 border rounded-lg text-sm bg-gray-100 cursor-not-allowed">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sekolah</label>
              <input v-model="form.nama_sekolah" type="text" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kepala Sekolah</label>
              <input v-model="form.kepala_sekolah" type="text" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
              <textarea v-model="form.alamat_sekolah" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500"></textarea>
            </div>
          </div>
          
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Dasar</label>
                <select v-model="form.jenjang" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
                  <option value="SMP">SMP</option>
                  <option value="MTs">MTs</option>
                  <option value="SMA">SMA</option>
                  <option value="SMK">SMK</option>
                  <option value="MA">MA</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Lanjutan</label>
                <select v-model="form.jenjang2" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
                  <option value="SMP">SMP</option>
                  <option value="SMA">SMA</option>
                </select>
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                <input v-model="form.kecamatan" type="text" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desa</label>
                <input v-model="form.desa" type="text" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Negeri/Swasta</label>
                <select v-model="form.negeri_or_swasta" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
                  <option :value="1">Negeri</option>
                  <option :value="2">Swasta</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Kemenag</label>
                <select v-model="form.is_kemenag" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500">
                  <option :value="false">Bukan Kemenag (Diknas)</option>
                  <option :value="true">Kemenag (MTs/MA)</option>
                </select>
              </div>
            </div>

            <div class="border p-4 rounded-lg bg-gray-50 mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Logo Sekolah</label>
              <input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
              <div v-if="logoPreview || form.logo_url" class="mt-4">
                <img :src="logoPreview || form.logo_url" class="h-32 object-contain bg-white p-2 rounded shadow-sm border" />
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
          <BaseButton type="button" variant="secondary" @click="$router.push('/admin/data-sekolah')">Batal</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">Simpan Perubahan Sekolah</BaseButton>
        </div>
      </form>
    </BaseCard>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import BaseCard from '../../components/BaseCard.vue';
import BaseButton from '../../components/BaseButton.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const npsn = route.params.npsn;

const isLoading = ref(false);
const isSubmitting = ref(false);

const logoPreview = ref(null);
const fileLogo = ref(null);

const form = ref({
  npsn: '',
  nama_sekolah: '',
  alamat_sekolah: '',
  kepala_sekolah: '',
  jenjang: '',
  jenjang2: '',
  desa: '',
  kecamatan: '',
  negeri_or_swasta: 1,
  is_kemenag: false,
  logo_url: null
});

onMounted(() => {
  if (npsn) {
    fetchSekolah();
  }
});

async function fetchSekolah() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin/data-sekolah/${npsn}`);
    const data = res.data.data;
    form.value = {
      npsn: data.npsn,
      nama_sekolah: data.nama_sekolah || '',
      alamat_sekolah: data.alamat_sekolah || '',
      kepala_sekolah: data.kepala_sekolah || '',
      jenjang: data.jenjang || '',
      jenjang2: data.jenjang2 || '',
      desa: data.desa || '',
      kecamatan: data.kecamatan || '',
      negeri_or_swasta: data.negeri_or_swasta,
      is_kemenag: data.is_kemenag == 1,
      logo_url: data.logo_url
    };
  } catch (error) {
    toast.error('Gagal mengambil data sekolah');
    router.push('/admin/data-sekolah');
  } finally {
    isLoading.value = false;
  }
}

function handleFileUpload(event) {
  const file = event.target.files[0];
  if (file) {
    fileLogo.value = file;
    const reader = new FileReader();
    reader.onload = e => { logoPreview.value = e.target.result; };
    reader.readAsDataURL(file);
  }
}

async function submitForm() {
  isSubmitting.value = true;
  try {
    const formData = new FormData();
    formData.append('nama_sekolah', form.value.nama_sekolah || '');
    formData.append('alamat_sekolah', form.value.alamat_sekolah || '');
    formData.append('kepala_sekolah', form.value.kepala_sekolah || '');
    formData.append('jenjang', form.value.jenjang || '');
    formData.append('jenjang2', form.value.jenjang2 || '');
    formData.append('desa', form.value.desa || '');
    formData.append('kecamatan', form.value.kecamatan || '');
    formData.append('negeri_or_swasta', form.value.negeri_or_swasta || 1);
    // Boolean true/false convert ke string
    formData.append('is_kemenag', form.value.is_kemenag ? '1' : '0');
    
    if (fileLogo.value) {
      formData.append('logo', fileLogo.value);
    }

    await api.post(`/admin/data-sekolah/${npsn}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    toast.success('Profil sekolah berhasil diperbarui');
    router.push('/admin/data-sekolah');
  } catch (error) {
    toast.error('Gagal menyimpan perubahan');
  } finally {
    isSubmitting.value = false;
  }
}
</script>
