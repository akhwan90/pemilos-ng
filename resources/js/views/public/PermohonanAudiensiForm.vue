<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <header class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-4xl mx-auto px-4 py-3 flex items-center gap-3">
        <router-link to="/" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></router-link>
        <div class="w-8 h-8 bg-amber-600 rounded-lg flex items-center justify-center"><span class="text-white font-bold text-sm">A</span></div>
        <div><h1 class="text-sm font-semibold text-gray-800">Permohonan Audiensi</h1><p class="text-xs text-gray-500">GESIT</p></div>
      </div>
    </header>
    <div class="max-w-3xl mx-auto px-4 py-8">
      <BaseCard>
        <template #header>
          <h2 class="text-xl font-bold text-gray-800 mb-2">Permohonan Audiensi</h2>
          <p class="text-sm text-gray-500">Ajukan permohonan pertemuan formal (audiensi) dengan dewan atau pejabat terkait.</p>
        </template>
        
        <div v-if="errorMsg" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 whitespace-pre-line">{{ errorMsg }}</div>
        
        <form @submit.prevent="handlePreSubmit">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseInput v-model="form.nama" label="Nama Lengkap" required />
            <BaseInput v-model="form.nama_instansi_kelompok" label="Nama Instansi/Lembaga/Kelompok/Komunitas" required />
            
            <div class="md:col-span-2">
              <BaseTextarea v-model="form.maksud_tujuan" label="Maksud & Tujuan Audiensi" rows="4" required />
            </div>
            
            <BaseInput v-model="form.nama_jabatan_ketua" label="Nama & Jabatan Ketua Rombongan" required />
            <BaseInput v-model="form.nomor_hp" type="tel" label="Nomor HP Narahubung" required />
            <BaseInput v-model="form.jumlah_peserta" type="number" label="Jumlah Peserta" required />
            
            <div class="md:col-span-2">
              <BaseFile label="File Permohonan Audiensi (PDF, max 5MB)" accept=".pdf" @change="e => selectedFile = e" required />
            </div>
          </div>
          
          <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex items-center gap-3 mb-6">
              <input type="checkbox" id="captcha" v-model="captchaPassed" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" required />
              <label for="captcha" class="text-sm text-gray-600">Saya bukan robot dan setuju data saya diproses sesuai ketentuan yang berlaku</label>
            </div>
            
            <BaseButton type="submit" variant="secondary" :disabled="!captchaPassed" :loading="loading" :block="true" class="md:w-auto px-8 py-3">
              Kirim Formulir
            </BaseButton>
          </div>
        </form>
      </BaseCard>
    </div>
    
    <GeneralConsentModal 
      v-model:show="showConsent" 
      :loading="loading" 
      @agree="submitForm" 
    />
    
    <ToastNotification ref="toastRef" />
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import BaseCard from '../../components/BaseCard.vue';
import BaseInput from '../../components/BaseInput.vue';
import BaseTextarea from '../../components/BaseTextarea.vue';
import BaseFile from '../../components/BaseFile.vue';
import BaseButton from '../../components/BaseButton.vue';
import GeneralConsentModal from '../../components/GeneralConsentModal.vue';
import ToastNotification from '../../components/ToastNotification.vue';

const router = useRouter();
const loading = ref(false);
const errorMsg = ref('');
const captchaPassed = ref(false);
const showConsent = ref(false);
const toastRef = ref(null);

const form = reactive({
  nama: '', nama_instansi_kelompok: '', maksud_tujuan: '',
  nama_jabatan_ketua: '', nomor_hp: '', jumlah_peserta: 1,
});

let selectedSurat = null;
let selectedDaftar = null;

function onSuratChange(file) { selectedSurat = file; }
function onDaftarChange(file) { selectedDaftar = file; }

function handlePreSubmit() {
  if (!captchaPassed.value) return;
  showConsent.value = true;
}

async function submitForm() {
  loading.value = true; errorMsg.value = '';
  const fd = new FormData();
  fd.append('nama_jabatan_ketua_rombongan', form.nama_jabatan_ketua);
  fd.append('nomor_hp_narahubung', form.nomor_hp);
  fd.append('jumlah_peserta', form.jumlah_peserta);
  if (selectedFile) fd.append('file_permohonan_audiensi', selectedFile);

  try {
    await api.post('/permohonan-audiensi', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
    showConsent.value = false;
    router.push({ path: '/sukses', query: { type: 'audiensi' } });
  } catch (e) {
    showConsent.value = false;
    const resp = e.response;
    const errText = resp?.status === 422 && resp.data?.errors
      ? Object.values(resp.data.errors).flat().join('\n')
      : (resp?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.');
    errorMsg.value = errText;
    if (toastRef.value) {
      toastRef.value.addToast(errText, 'error');
    }
  } finally { loading.value = false; }
}
</script>
