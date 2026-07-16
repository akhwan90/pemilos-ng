<template>
  <div>
    <div class="flex items-center gap-3 mb-6">
      <router-link to="/admin/permohonan-audiensi" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
      </router-link>
      <h1 class="text-2xl font-bold text-gray-800">Detail Permohonan Audiensi</h1>
    </div>
    <div v-if="loading" class="text-center py-12">
      <svg class="animate-spin h-8 w-8 mx-auto text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg>
      <p class="text-gray-500 mt-2">Memuat data...</p>
    </div>
    <template v-if="!loading && item">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pemohon</h2>
            <dl class="grid grid-cols-2 gap-4">
              <div><dt class="text-xs text-gray-500">Nama</dt><dd class="text-sm font-medium text-gray-800">{{ item.nama }}</dd></div>
              <div><dt class="text-xs text-gray-500">Instansi/Kelompok</dt><dd class="text-sm font-medium text-gray-800">{{ item.nama_instansi_kelompok_paguyuban_komunitas }}</dd></div>
              <div class="col-span-2"><dt class="text-xs text-gray-500">Maksud & Tujuan</dt><dd class="text-sm text-gray-800 whitespace-pre-wrap">{{ item.maksud_tujuan_audiensi }}</dd></div>
              <div><dt class="text-xs text-gray-500">Ketua Rombongan</dt><dd class="text-sm text-gray-800">{{ item.nama_jabatan_ketua_rombongan }}</dd></div>
              <div><dt class="text-xs text-gray-500">HP Narahubung</dt><dd class="text-sm text-gray-800">{{ item.nomor_hp_narahubung }}</dd></div>
              <div><dt class="text-xs text-gray-500">Jumlah Peserta</dt><dd class="text-sm text-gray-800">{{ item.jumlah_peserta }} orang</dd></div>
            </dl>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">File</h2>
            <a v-if="item.file_permohonan_audiensi" :href="item.file_permohonan_audiensi" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              Download Surat Permohonan Audiensi
            </a>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Catatan Internal</h2>
            <div class="space-y-3 mb-4">
              <div v-for="note in item.notes" :key="note.id" class="p-3 bg-gray-50 rounded-lg"><p class="text-sm text-gray-700">{{ note.note }}</p><p class="text-xs text-gray-400 mt-1">{{ note.admin?.name }} — {{ note.created_at }}</p></div>
              <p v-if="!item.notes?.length" class="text-sm text-gray-500">Belum ada catatan.</p>
            </div>
            <div class="flex gap-2">
              <input v-model="newNote" type="text" placeholder="Tambah catatan..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" @keyup.enter="addNote" />
              <button @click="addNote" :disabled="!newNote.trim()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">Tambah</button>
            </div>
          </div>
        </div>
        <div class="space-y-6">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Dokumen PDF</h2>
            
            <div class="space-y-4 mb-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Penerima Audiensi (Alkap)</label>
                  <select v-model="generateForm.alkap_penerima" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                    <option v-for="opt in alkapOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat PPID</label>
                  <input type="text" v-model="generateForm.nomor_surat_ppid" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Otomatis jika kosong" />
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pelaksanaan</label>
                  <input type="date" v-model="generateForm.tanggal_pelaksanaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Jam Pelaksanaan</label>
                  <select v-model="generateForm.jam_pelaksanaan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white">
                    <option v-for="opt in jamOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                  </select>
                </div>
              </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
              <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{'border-indigo-500 bg-indigo-50': generateDocType === 'daftar_hadir'}">
                <input type="radio" v-model="generateDocType" value="daftar_hadir" class="text-indigo-600" />
                <span class="text-sm font-medium">Daftar Hadir</span>
              </label>
              <label class="flex items-center gap-2 p-3 border rounded-lg cursor-pointer hover:bg-gray-50" :class="{'border-indigo-500 bg-indigo-50': generateDocType === 'ppid'}">
                <input type="radio" v-model="generateDocType" value="ppid" class="text-indigo-600" />
                <span class="text-sm font-medium">Form PPID</span>
              </label>
            </div>
            
            <button @click="generateDocument" :disabled="isGenerating" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">
              <svg v-if="isGenerating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              {{ isGenerating ? 'Memproses...' : 'Generate PDF' }}
            </button>
            
            <div class="mt-4 flex flex-col gap-2">
              <a v-if="item.file_daftar_hadir" :href="item.file_daftar_hadir" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800">
                📄 Daftar Hadir (Generated)
              </a>
              <a v-if="item.file_dokumen_ppid" :href="item.file_dokumen_ppid" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800">
                📄 Form PPID (Generated)
              </a>
            </div>
          </div>
          
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Status</h2>
            <div class="mb-4"><StatusBadge :status="item.status" :label="item.status_label" /></div>
            <select v-model="newStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white mb-3">
              <option value="baru">Baru</option><option value="diproses">Diproses</option><option value="disetujui">Disetujui</option><option value="ditolak">Ditolak</option><option value="selesai">Selesai</option>
            </select>
            <button @click="updateStatus" :disabled="newStatus === item.status" class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">Simpan</button>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Hubungi via WhatsApp</h2>
            <a :href="whatsappUrl" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606 .134-.133.298-.347.446-.52 .149-.174.198-.298.298-.497 .099-.198.05-.371-.025-.52 -.075-.149-.669-1.612-.916-2.207 -.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01 -.198 0-.52.074-.792.372 -.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074 .149.198 2.096 3.2 5.077 4.487 .709.306 1.262.489 1.694.625 .712.227 1.36.195 1.871.118 .571-.085 1.758-.719 2.006-1.413 .248-.694.248-1.289.173-1.413 -.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982 .998-3.648 -.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Hubungi via WhatsApp
            </a>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi</h2>
            <dl class="space-y-2"><div><dt class="text-xs text-gray-500">Dibuat</dt><dd class="text-sm text-gray-800">{{ item.created_at }}</dd></div><div><dt class="text-xs text-gray-500">Diperbarui</dt><dd class="text-sm text-gray-800">{{ item.updated_at }}</dd></div></dl>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge.vue';
const route = useRoute();
const item = ref(null);
const loading = ref(true);
const newStatus = ref('baru');
const newNote = ref('');

const whatsappUrl = computed(() => {
  if (!item.value) return '#';
  const phone = item.value.nomor_hp_narahubung?.replace(/[^0-9]/g, '');
  const msg = encodeURIComponent(`Yth. ${item.value.nama},\n\nKami dari Sekretariat DPRD Kab. Kulon Progo menindaklanjuti permohonan audiensi melalui sistem GESIT.\n\nMohon kesediaannya untuk konfirmasi jadwal.\n\nTerima kasih.`);
  return `https://wa.me/${phone?.startsWith('62') ? '' : '62'}${phone}?text=${msg}`;
});

const alkapOptions = [
  { value: 'Pimpinan DPRD', label: 'Pimpinan DPRD' },
  { value: 'Komisi I', label: 'Komisi I' },
  { value: 'Komisi II', label: 'Komisi II' },
  { value: 'Komisi III', label: 'Komisi III' },
  { value: 'Komisi IV', label: 'Komisi IV' },
  { value: 'Bamus', label: 'Bamus' },
  { value: 'Banggar', label: 'Banggar' },
  { value: 'Bapemperda', label: 'Bapemperda' },
  { value: 'BK', label: 'BK' },
  { value: 'Pansus', label: 'Pansus' },
  { value: 'Fraksi', label: 'Fraksi' },
];

const jamOptions = [
  { value: '08:00', label: '08.00' },
  { value: '08:30', label: '08.30' },
  { value: '09:00', label: '09.00' },
  { value: '09:30', label: '09.30' },
  { value: '10:00', label: '10.00' },
  { value: '10:30', label: '10.30' },
  { value: '11:00', label: '11.00' },
  { value: '11:30', label: '11.30' },
  { value: '12:00', label: '12.00' },
  { value: '12:30', label: '12.30' },
  { value: '13:00', label: '13.00' },
  { value: '13:30', label: '13.30' },
  { value: '14:00', label: '14.00' },
];

const generateForm = ref({
    tanggal_pelaksanaan: '',
    jam_pelaksanaan: '',
    alkap_penerima: 'Pimpinan DPRD',
    nomor_surat_ppid: ''
});

const generateDocType = ref('daftar_hadir');
const generatedUrl = ref('');
const isGenerating = ref(false);

async function generateDocument() {
  if (generateDocType.value === 'daftar_hadir' && (!generateForm.value.tanggal_pelaksanaan || !generateForm.value.jam_pelaksanaan)) {
      alert('Tanggal dan Jam pelaksanaan wajib diisi untuk Daftar Hadir.');
      return;
  }
  
  isGenerating.value = true;
  try {
    // 1. Simpan data detail (Update PermohonanAudiensi)
    const updateRes = await api.put(`/admin/permohonan-audiensi/${item.value.id}`, {
      tanggal_pelaksanaan: generateForm.value.tanggal_pelaksanaan,
      jam_pelaksanaan: generateForm.value.jam_pelaksanaan,
      alkap_penerima: generateForm.value.alkap_penerima,
      nomor_surat_ppid: generateForm.value.nomor_surat_ppid
    });
    item.value = updateRes.data.data;
    
    // 2. Generate PDF
    const res = await api.post(`/admin/permohonan-audiensi/${item.value.id}/generate`, {
      jenis_dokumen: generateDocType.value
    });
    
    if (res.data.success && res.data.url) {
      item.value = res.data.data;
      
      let finalUrl = res.data.url;
      if (finalUrl.startsWith('/')) {
        finalUrl = window.location.origin + finalUrl;
      }
      
      generatedUrl.value = finalUrl;
      window.open(finalUrl, '_blank');
    }
  } catch (error) {
    console.error('Error generating document:', error);
    alert('Gagal membuat dokumen. Periksa konsol untuk detail.');
  } finally {
    isGenerating.value = false;
  }
}

onMounted(async () => {
  try { 
      const res = await api.get(`/admin/permohonan-audiensi/${route.params.id}`); 
      item.value = res.data.data; 
      newStatus.value = item.value.status; 
      
      // Init form values
      if (item.value) {
          if (item.value.tanggal_pelaksanaan) generateForm.value.tanggal_pelaksanaan = item.value.tanggal_pelaksanaan;
          if (item.value.jam_pelaksanaan) generateForm.value.jam_pelaksanaan = item.value.jam_pelaksanaan;
          if (item.value.alkap_penerima) generateForm.value.alkap_penerima = item.value.alkap_penerima;
          if (item.value.nomor_surat_ppid) generateForm.value.nomor_surat_ppid = item.value.nomor_surat_ppid;
      }
  } catch (e) { console.error(e); } finally { loading.value = false; }
});

async function updateStatus() {
  try { const res = await api.patch(`/admin/permohonan-audiensi/${route.params.id}/status`, { status: newStatus.value }); item.value = res.data.data; } catch (e) { console.error(e); }
}

async function addNote() {
  if (!newNote.value.trim()) return;
  try { const res = await api.post(`/admin/notes/audiensi/${route.params.id}`, { note: newNote.value }); item.value.notes.push(res.data.data); newNote.value = ''; } catch (e) { console.error(e); }
}
</script>
