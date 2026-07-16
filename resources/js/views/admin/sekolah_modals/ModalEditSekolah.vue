<template>
  <BaseModal v-model="isOpen" :title="isEditMode ? 'Edit Profil Sekolah' : 'Tambah Sekolah Baru'" max-width="4xl">
    <div v-if="npsn || !isEditMode">
      <div v-if="isLoading" class="p-8 text-center text-gray-500">Memuat data sekolah...</div>
      <form v-else @submit.prevent="submitForm" class="space-y-4">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NPSN</label>
              <!-- Bisa diisi kalau Tambah, readonly kalau Edit -->
              <input v-model="form.npsn" type="text" required :readonly="isEditMode" :class="[
                'w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500', 
                isEditMode ? 'bg-gray-100 cursor-not-allowed' : ''
              ]">
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
                <img :src="logoPreview || form.logo_url" class="h-24 object-contain bg-white p-2 rounded shadow-sm border" />
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
          <BaseButton type="button" variant="secondary" @click="isOpen = false">Batal</BaseButton>
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">{{ isEditMode ? 'Simpan Perubahan' : 'Tambah Sekolah' }}</BaseButton>
        </div>
      </form>
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
  npsn: { type: [String, Number], default: null }, // Null artinya mode Tambah Baru
  isEditMode: { type: Boolean, default: true }
});

const emit = defineEmits(['update:modelValue', 'updated']);
const toast = useToast();

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

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

watch(() => isOpen.value, (newVal) => {
  if (newVal) {
    logoPreview.value = null;
    fileLogo.value = null;
    
    if (props.isEditMode && props.npsn) {
      fetchSekolah();
    } else {
      // Mode Tambah, reset form
      form.value = {
        npsn: '',
        nama_sekolah: '',
        alamat_sekolah: '',
        kepala_sekolah: '',
        jenjang: 'SMP',
        jenjang2: 'SMA',
        desa: '',
        kecamatan: '',
        negeri_or_swasta: 1,
        is_kemenag: false,
        logo_url: null
      };
    }
  }
});

async function fetchSekolah() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin/data-sekolah/${props.npsn}`);
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
    isOpen.value = false;
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
    formData.append('is_kemenag', form.value.is_kemenag ? '1' : '0');
    
    if (fileLogo.value) {
      formData.append('logo', fileLogo.value);
    }

    if (props.isEditMode) {
      await api.post(`/admin/data-sekolah/${props.npsn}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      toast.success('Profil sekolah berhasil diperbarui');
    } else {
      await api.post(`/admin/data-sekolah`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      toast.success('Sekolah baru berhasil ditambahkan');
    }

    isOpen.value = false;
    emit('updated'); // Trigger refresh di list
  } catch (error) {
    toast.error('Gagal menyimpan perubahan');
  } finally {
    isSubmitting.value = false;
  }
}
</script>
