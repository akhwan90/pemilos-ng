<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Aduan & Aspirasi</h1>
      <div class="flex items-center gap-2">
        <BaseInput v-model="search" placeholder="Cari..." />
        
        <BaseSelect v-model="filterStatus" :options="statusOptions" valueKey="value" labelKey="label" placeholder="Semua Status" />
        
        <BaseButton @click="openAddModal" variant="success">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Tambah
        </BaseButton>
        <BaseButton @click="exportData" variant="primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
          Ekspor Excel
        </BaseButton>
      </div>
    </div>

    <!-- Full Width List View -->
    <div class="space-y-4">
      <BaseCard v-for="(item, i) in items" :key="item.id" class="hover:shadow-md transition-shadow">
        <div class="flex flex-col md:flex-row gap-6">
          
          <!-- Tanggal & Kategori -->
          <div class="md:w-1/4 shrink-0 space-y-3">
            <div>
              <p class="text-xs text-gray-500 mb-1">Tanggal</p>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <p class="text-sm font-medium text-gray-800">{{ item.created_at }}</p>
              </div>
            </div>
            
            <div>
              <p class="text-xs text-gray-500 mb-1">Kategori</p>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                <p class="text-sm font-medium text-gray-800">{{ item.kategori_aduan?.nama || '-' }}</p>
              </div>
            </div>
            
            <div class="pt-2">
              <StatusBadge :status="item.status" :label="item.status_label" />
            </div>
          </div>

          <!-- Isi Aduan -->
          <div class="md:w-2/4 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 flex-1 flex flex-col">
            <div class="mb-2">
              <p class="text-sm font-bold text-gray-800">{{ item.nama }} <span class="font-normal text-gray-500 ml-2">{{ item.nik }}</span></p>
            </div>
            <p class="text-sm text-gray-700 whitespace-pre-wrap line-clamp-3 mb-4">{{ item.isi_aduan }}</p>
          
            <div v-if="item.file_berkas_aduan" class="mt-auto">
              <a :href="item.file_berkas_aduan" target="_blank" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 text-gray-600 rounded-md text-xs font-medium hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                Bukti Dukung
              </a>
            </div>
          </div>

          <!-- Aksi -->
          <div class="md:w-1/4 shrink-0 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 flex flex-col gap-2 justify-center">
            <button @click="openDetailModal(item)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              Detail
            </button>
            
            <button @click="openEditModal(item)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              Edit
            </button>
            
            <button @click="updateProgress(item.id)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-amber-50 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
              Progress
            </button>
            
            <button @click="deleteItem(item.id)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              Hapus
            </button>
          </div>
          
        </div>
      </BaseCard>

      <!-- Empty State -->
      <BaseCard v-if="!items.length" class="text-center py-12 border-dashed">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
        <p class="text-gray-500">Belum ada data.</p>
      </BaseCard>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="mt-6 flex items-center justify-between">
      <p class="text-sm text-gray-500">Menampilkan {{ meta.from }} - {{ meta.to }} dari {{ meta.total }}</p>
      <div class="flex gap-2">
        <BaseButton @click="loadPage(currentPage - 1)" :disabled="!meta.prev_page_url" variant="outline" class="!px-3 !py-1 text-sm">Prev</BaseButton>
        <BaseButton @click="loadPage(currentPage + 1)" :disabled="!meta.next_page_url" variant="outline" class="!px-3 !py-1 text-sm">Next</BaseButton>
      </div>
    </div>
    
    <!-- Edit / Tambah Form Modal -->
    <BaseModal v-model="showFormModal" :title="isEdit ? 'Edit Aduan & Aspirasi' : 'Tambah Aduan & Aspirasi'" maxWidth="3xl">
      <form @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseInput v-model="form.nama" label="Nama Lengkap" required />
          <BaseInput v-model="form.nik" label="NIK" required />
          <BaseInput v-model="form.nomor_hp" label="Nomor HP" required />
          <BaseInput v-model="form.email" type="email" label="Email" required />
          <BaseInput v-model="form.pekerjaan" label="Pekerjaan" required />
          <BaseSelect v-model="form.kategori_aduan_id" :options="kategoriList" valueKey="id" labelKey="nama" label="Kategori Aduan" required />
          <BaseInput v-model="form.alamat" label="Alamat Lengkap" class="md:col-span-2" required />
          <BaseTextarea v-model="form.isi_aduan" label="Isi Aduan / Aspirasi" class="md:col-span-2" required rows="4" />
          <div class="md:col-span-2">
            <BaseFile @change="file => { form.file_berkas_aduan = file }" label="Berkas Dukung (PDF/JPG/PNG)" accept=".pdf,.jpg,.jpeg,.png" :required="!isEdit" />
            <p v-if="isEdit && form.file_berkas_aduan_existing" class="text-xs text-indigo-600 mt-1">
              File saat ini: <a :href="form.file_berkas_aduan_existing" target="_blank" class="underline">Lihat file lama</a>. Kosongkan input di atas jika tidak ingin mengubah berkas.
            </p>
          </div>
        </div>
      </form>
      <template #footer>
        <BaseButton @click="showFormModal = false" variant="outline">Batal</BaseButton>
        <BaseButton @click="submitForm" variant="primary" :disabled="formLoading">{{ formLoading ? 'Menyimpan...' : 'Simpan' }}</BaseButton>
      </template>
    </BaseModal>

    <!-- Detail Modal -->
    <BaseModal v-model="showDetailModal" title="Detail Aduan & Aspirasi" maxWidth="4xl">
      <div v-if="selectedItem" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Pelapor -->
        <div class="space-y-4">
          <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Informasi Pelapor</h3>
          <dl class="grid grid-cols-2 gap-4">
            <div><dt class="text-xs text-gray-500">Nama</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.nama }}</dd></div>
            <div><dt class="text-xs text-gray-500">NIK</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.nik }}</dd></div>
            <div class="col-span-2"><dt class="text-xs text-gray-500">Alamat</dt><dd class="text-sm text-gray-800">{{ selectedItem.alamat }}</dd></div>
            <div><dt class="text-xs text-gray-500">Pekerjaan</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.pekerjaan }}</dd></div>
            <div><dt class="text-xs text-gray-500">Kategori</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.kategori_aduan?.nama || '-' }}</dd></div>
            <div><dt class="text-xs text-gray-500">Nomor HP</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.nomor_hp }}</dd></div>
            <div><dt class="text-xs text-gray-500">Email</dt><dd class="text-sm text-gray-800">{{ selectedItem.email || '-' }}</dd></div>
          </dl>
          
          <div class="pt-2">
            <a :href="whatsappUrl" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606 .134-.133.298-.347.446-.52 .149-.174 .198-.298 .298-.497 .099-.198 .05-.371-.025-.52 -.075-.149-.669-1.612-.916-2.207 -.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01 -.198 0-.52.074-.792.372 -.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074 .149.198 2.096 3.2 5.077 4.487 .709.306 1.262.489 1.694.625 .712.227 1.36.195 1.871.118 .571-.085 1.758-.719 2.006-1.413 .248-.694.248-1.289.173-1.413 -.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982 .998-3.648 -.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Hubungi via WhatsApp
            </a>
          </div>
        </div>
        
        <!-- Isi Aduan -->
        <div class="space-y-4">
          <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Isi Aduan</h3>
          <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ selectedItem.isi_aduan }}</p>
          
          <div v-if="selectedItem.file_berkas_aduan" class="pt-2 border-t mt-4">
            <h3 class="text-sm font-bold text-gray-800 mb-2">Berkas Dukung</h3>
            <a :href="selectedItem.file_berkas_aduan" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              Lihat Berkas
            </a>
          </div>
        </div>
      </div>
      
      <template #footer>
        <BaseButton @click="showDetailModal = false" variant="outline">Tutup</BaseButton>
      </template>
    </BaseModal>
    
    <!-- Progress Modal -->
    <BaseModal v-model="showProgressModal" title="Progress Aduan & Aspirasi" maxWidth="2xl">
      <div v-if="selectedItem" class="space-y-6">
        <!-- Status Update -->
        <div class="space-y-3 border-b pb-6">
          <h3 class="text-sm font-bold text-gray-800">Ubah Status</h3>
          <div class="flex items-center gap-4">
            <div class="w-1/3">
              <StatusBadge :status="selectedItem.status" :label="selectedItem.status_label" size="lg" />
            </div>
            <div class="w-2/3 flex gap-2">
              <BaseSelect v-model="newStatus" :options="statusOptions" valueKey="value" labelKey="label" placeholder="Pilih Status" class="flex-1" />
              <BaseButton @click="updateStatus" :disabled="newStatus === selectedItem.status" variant="primary">Simpan Status</BaseButton>
            </div>
          </div>
        </div>

        <!-- Internal Notes -->
        <div class="space-y-3">
          <h3 class="text-sm font-bold text-gray-800">Catatan Internal / Progress</h3>
          <div class="space-y-3 mb-4 max-h-60 overflow-y-auto pr-2">
            <div v-for="note in selectedItem.notes" :key="note.id" class="p-3 bg-gray-50 border border-gray-100 rounded-lg">
              <p class="text-sm text-gray-700">{{ note.note }}</p>
              <p class="text-xs text-gray-400 mt-1">{{ note.admin?.name }} — {{ note.created_at }}</p>
            </div>
            <p v-if="!selectedItem.notes?.length" class="text-sm text-gray-500 text-center py-4 bg-gray-50 rounded-lg border border-dashed">Belum ada catatan.</p>
          </div>
          
          <div class="flex gap-2">
            <BaseInput v-model="newNote" placeholder="Ketik catatan progress baru..." class="flex-1" @keyup.enter="addNote" />
            <BaseButton @click="addNote" :disabled="!newNote.trim()">Tambah Catatan</BaseButton>
          </div>
        </div>
      </div>
      
      <template #footer>
        <BaseButton @click="showProgressModal = false" variant="outline">Tutup</BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge.vue';
import BaseInput from '../../components/BaseInput.vue';
import BaseSelect from '../../components/BaseSelect.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseModal from '../../components/BaseModal.vue';
import BaseTextarea from '../../components/BaseTextarea.vue';
import BaseFile from '../../components/BaseFile.vue';

const items = ref([]);
const meta = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null });
const search = ref('');
const filterStatus = ref('');
const currentPage = ref(1);

const showDetailModal = ref(false);
const showProgressModal = ref(false);
const showFormModal = ref(false);
const formLoading = ref(false);
const isEdit = ref(false);

const selectedItem = ref(null);
const newStatus = ref('baru');
const newNote = ref('');
const kategoriList = ref([]);

const form = ref({
  nama: '', nik: '', alamat: '', pekerjaan: '',
  nomor_hp: '', email: '', kategori_aduan_id: '',
  isi_aduan: '', file_berkas_aduan: null, file_berkas_aduan_existing: null
});

const statusOptions = [
  { value: 'baru', label: 'Baru' },
  { value: 'diproses', label: 'Diproses' },
  { value: 'disetujui', label: 'Disetujui' },
  { value: 'ditolak', label: 'Ditolak' },
  { value: 'selesai', label: 'Selesai' }
];

const whatsappUrl = computed(() => {
  if (!selectedItem.value) return '#';
  const phone = selectedItem.value.nomor_hp?.replace(/[^0-9]/g, '');
  const msg = encodeURIComponent(
    `Yth. ${selectedItem.value.nama},\n\nKami dari Sekretariat DPRD Kab. Kulon Progo menindaklanjuti aduan/aspirasi yang telah Anda sampaikan melalui sistem GESIT.\n\nMohon kesediaannya untuk dihubungi lebih lanjut.\n\nTerima kasih.`
  );
  const prefix = phone?.startsWith('62') ? '' : '62';
  return `https://wa.me/${prefix}${phone}?text=${msg}`;
});

function openDetailModal(item) {
  selectedItem.value = item;
  showDetailModal.value = true;
}

function openAddModal() {
  isEdit.value = false;
  selectedItem.value = null;
  form.value = {
    nama: '', nik: '', alamat: '', pekerjaan: '',
    nomor_hp: '', email: '', kategori_aduan_id: '',
    isi_aduan: '', file_berkas_aduan: null, file_berkas_aduan_existing: null
  };
  showFormModal.value = true;
}

function openEditModal(item) {
  isEdit.value = true;
  selectedItem.value = item;
  form.value = {
    nama: item.nama,
    nik: item.nik,
    alamat: item.alamat,
    pekerjaan: item.pekerjaan,
    nomor_hp: item.nomor_hp,
    email: item.email,
    kategori_aduan_id: item.kategori_aduan_id,
    isi_aduan: item.isi_aduan,
    file_berkas_aduan: null, // Biarkan null jika tidak ingin ganti file
    file_berkas_aduan_existing: item.file_berkas_aduan
  };
  showFormModal.value = true;
}

async function submitForm() {
  formLoading.value = true;
  try {
    const formData = new FormData();
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key]);
      }
    });

    if (isEdit.value) {
      formData.append('_method', 'PUT');
      await api.post(`/admin/aduan-aspirasi/${selectedItem.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      await api.post('/admin/aduan-aspirasi', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }
    showFormModal.value = false;
    loadData();
  } catch (e) {
    console.error(e);
    alert('Terjadi kesalahan saat menyimpan data.');
  } finally {
    formLoading.value = false;
  }
}

async function updateProgress(id) {
  try {
    // Kita panggil API untuk memastikan data catatan (notes) paling baru sebelum buka modal
    const res = await api.get(`/admin/aduan-aspirasi/${id}`);
    selectedItem.value = res.data.data;
    newStatus.value = selectedItem.value.status;
    showProgressModal.value = true;
  } catch (e) {
    console.error(e);
  }
}

async function updateStatus() {
  if (!selectedItem.value) return;
  try {
    const res = await api.patch(`/admin/aduan-aspirasi/${selectedItem.value.id}/status`, { status: newStatus.value });
    selectedItem.value = res.data.data;
    // Perbarui juga data di list utama agar reaktif
    const idx = items.value.findIndex(i => i.id === selectedItem.value.id);
    if (idx !== -1) items.value[idx] = res.data.data;
  } catch (e) { console.error(e); }
}

async function addNote() {
  if (!newNote.value.trim() || !selectedItem.value) return;
  try {
    const res = await api.post(`/admin/notes/aduan/${selectedItem.value.id}`, { note: newNote.value });
    // Masukkan ke array notes di selectedItem
    if (!selectedItem.value.notes) selectedItem.value.notes = [];
    selectedItem.value.notes.push(res.data.data);
    newNote.value = '';
  } catch (e) { console.error(e); }
}

async function loadData() {
  try {
    const params = { page: currentPage.value, per_page: 15 };
    if (search.value) params.search = search.value;
    if (filterStatus.value) params.status = filterStatus.value;
    const res = await api.get('/admin/aduan-aspirasi', { params });
    items.value = res.data.data;
    meta.value = res.data.meta;
  } catch (e) { console.error(e); }
}

watch([search, filterStatus], () => { currentPage.value = 1; loadData(); });

function loadPage(page) {
  if (page < 1 || page > meta.value.last_page) return;
  currentPage.value = page;
  loadData();
}

async function deleteItem(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;
  try {
    await api.delete(`/admin/aduan-aspirasi/${id}`);
    loadData();
  } catch (e) {
    console.error(e);
    alert('Gagal menghapus data.');
  }
}

async function exportData() {
  try {
    const params = {};
    if (filterStatus.value) params.status = filterStatus.value;
    const res = await api.get('/admin/aduan-aspirasi/export', { params });
    // For now, just open the data in a new tab as JSON (Excel export via backend later)
    const blob = new Blob([JSON.stringify(res.data.data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = `aduan-aspirasi-export.json`;
    a.click();
    URL.revokeObjectURL(url);
  } catch (e) { console.error(e); }
}

onMounted(() => {
  loadData();
  loadKategori();
});

async function loadKategori() {
  try {
    const res = await api.get('/kategori-aduan');
    kategoriList.value = res.data.data;
  } catch (e) { console.error(e); }
}
</script>
