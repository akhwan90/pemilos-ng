<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <header class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-4xl mx-auto px-4 py-3 flex items-center gap-3">
        <router-link to="/" class="text-gray-400 hover:text-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </router-link>
        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center"><span class="text-white font-bold text-sm">G</span></div>
        <div><h1 class="text-sm font-semibold text-gray-800">Aduan & Aspirasi</h1><p class="text-xs text-gray-500">GESIT</p></div>
      </div>
    </header>
    <div class="max-w-3xl mx-auto px-4 py-8">
      <BaseCard>
        <template #header>
          <h2 class="text-xl font-bold text-gray-800 mb-2">Aduan & Aspirasi</h2>
          <p class="text-sm text-gray-500">Sampaikan keluhan, laporan, kritik, maupun aspirasi Anda kepada pemerintah/dewan.</p>
        </template>
        
        <div v-if="whatsappNumber" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
          <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <div>
            <h3 class="text-sm font-semibold text-green-800">Layanan melalui WhatsApp</h3>
            <p class="text-sm text-green-700 mt-1">
              Aduan dan aspirasi juga dapat disampaikan secara langsung melalui WhatsApp di nomor 
              <a :href="`https://wa.me/${whatsappNumber}`" target="_blank" class="font-bold underline hover:text-green-800">{{ whatsappNumber }}</a>.
            </p>
          </div>
        </div>
        
        <div v-if="errorMsg" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 whitespace-pre-line">{{ errorMsg }}</div>
        
        <form @submit.prevent="handlePreSubmit">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <BaseInput v-model="form.nama" label="Nama Lengkap" required />
            <BaseInput v-model="form.nik" label="NIK (16 digit)" maxlength="16" required />
            <div class="md:col-span-2">
              <BaseTextarea v-model="form.alamat" label="Alamat" rows="2" required />
            </div>
            <BaseInput v-model="form.pekerjaan" label="Pekerjaan" required />
            <BaseInput v-model="form.nomor_hp" type="tel" label="Nomor HP" required />
            <BaseInput v-model="form.email" type="email" label="Email" required />
            <BaseSelect v-model="form.kategori_aduan_id" label="Kategori Aduan" :options="kategoriList" required />
            <div class="md:col-span-2">
              <BaseTextarea v-model="form.isi_aduan" label="Isi Aduan" rows="4" required />
            </div>
            <div class="md:col-span-2">
              <BaseFile label="File Berkas Aduan (PDF/JPG/PNG, max 5MB)" accept=".pdf,.jpg,.jpeg,.png" @change="onFileChange" />
            </div>
          </div>
          <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex items-center gap-3 mb-6">
              <input type="checkbox" id="captcha" v-model="captchaPassed" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" required />
              <label for="captcha" class="text-sm text-gray-600">Saya bukan robot dan setuju data saya diproses sesuai ketentuan yang berlaku</label>
            </div>
            <BaseButton type="submit" :disabled="!captchaPassed" :loading="loading" :block="true" class="md:w-auto px-8 py-3">
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
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import BaseCard from '../../components/BaseCard.vue';
import BaseInput from '../../components/BaseInput.vue';
import BaseTextarea from '../../components/BaseTextarea.vue';
import BaseSelect from '../../components/BaseSelect.vue';
import BaseFile from '../../components/BaseFile.vue';
import BaseButton from '../../components/BaseButton.vue';
import GeneralConsentModal from '../../components/GeneralConsentModal.vue';
import ToastNotification from '../../components/ToastNotification.vue';

const router = useRouter();
const fileInput = ref(null);
const kategoriList = ref([]);
const loading = ref(false);
const errorMsg = ref('');
const captchaPassed = ref(false);
const showConsent = ref(false);
const toastRef = ref(null);
const whatsappNumber = ref('');

const form = reactive({
  nama: '', nik: '', alamat: '', pekerjaan: '', nomor_hp: '',
  email: '', kategori_aduan_id: '', isi_aduan: '',
});
let selectedFile = null;

function onFileChange(file) {
  selectedFile = file;
}

onMounted(async () => {
  try {
    const res = await api.get('/kategori-aduan');
    kategoriList.value = res.data.data;
    if (res.data.whatsapp) {
      whatsappNumber.value = res.data.whatsapp;
    }
  } catch (e) { console.error(e); }
});

function handlePreSubmit() {
  if (!captchaPassed.value) return;
  showConsent.value = true;
}

async function submitForm() {
  loading.value = true;
  errorMsg.value = '';

  const fd = new FormData();
  Object.entries(form).forEach(([k, v]) => fd.append(k, v));
  if (selectedFile) fd.append('file_berkas_aduan', selectedFile);

  try {
    await api.post('/aduan-aspirasi', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    showConsent.value = false;
    router.push({ path: '/sukses', query: { type: 'aduan' } });
  } catch (e) {
    showConsent.value = false;
    const resp = e.response;
    let errText = '';
    if (resp?.status === 422 && resp.data?.errors) {
      errText = Object.values(resp.data.errors).flat().join('\n');
    } else {
      errText = resp?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.';
    }
    errorMsg.value = errText;
    if (toastRef.value) {
      toastRef.value.addToast(errText, 'error');
    }
  } finally {
    loading.value = false;
  }
}
</script>
