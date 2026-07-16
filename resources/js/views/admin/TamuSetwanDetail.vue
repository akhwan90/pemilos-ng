<template>
  <div>
    <div class="flex items-center gap-3 mb-6">
      <router-link to="/admin/tamu-setwan" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></router-link>
      <h1 class="text-2xl font-bold text-gray-800">Detail Tamu Setwan</h1>
    </div>
    <div v-if="loading" class="text-center py-12"><svg class="animate-spin h-8 w-8 mx-auto text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" /><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" /></svg><p class="text-gray-500 mt-2">Memuat data...</p></div>
    <template v-if="!loading && item">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pemohon</h2>
            <dl class="grid grid-cols-2 gap-4">
              <div><dt class="text-xs text-gray-500">Nama</dt><dd class="text-sm font-medium text-gray-800">{{ item.nama }}</dd></div>
              <div><dt class="text-xs text-gray-500">Instansi</dt><dd class="text-sm font-medium text-gray-800">{{ item.instansi }}</dd></div>
              <div class="col-span-2"><dt class="text-xs text-gray-500">Alamat Instansi</dt><dd class="text-sm text-gray-800">{{ item.alamat_instansi }}</dd></div>
              <div><dt class="text-xs text-gray-500">Hari Berkunjung</dt><dd class="text-sm font-medium text-gray-800">{{ item.hari_berkunjung }}</dd></div>
              <div><dt class="text-xs text-gray-500">Jam Berkunjung</dt><dd class="text-sm font-medium text-gray-800">{{ item.jam_berkunjung }}</dd></div>
              <div><dt class="text-xs text-gray-500">Jumlah Peserta</dt><dd class="text-sm font-medium text-gray-800">{{ item.jumlah_peserta }} orang</dd></div>
              <div><dt class="text-xs text-gray-500">Ketua Rombongan</dt><dd class="text-sm font-medium text-gray-800">{{ item.nama_jabatan_ketua_rombongan }}</dd></div>
              <div><dt class="text-xs text-gray-500">Nomor HP Narahubung</dt><dd class="text-sm font-medium text-gray-800">{{ item.nomor_hp_narahubung }}</dd></div>
              <div><dt class="text-xs text-gray-500">Email</dt><dd class="text-sm text-gray-800">{{ item.email }}</dd></div>
              <div class="col-span-2"><dt class="text-xs text-gray-500">Tujuan Kunjungan</dt><dd class="text-sm text-gray-800">{{ item.tujuan_kunjungan }}</dd></div>
            </dl>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">File</h2>
            <div class="space-y-2">
              <a v-if="item.file_surat_kunjungan" :href="item.file_surat_kunjungan" target="_blank" class="block px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100">Download Surat Kunjungan</a>
              <a v-if="item.file_spt" :href="item.file_spt" target="_blank" class="block px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100">Download SPT</a>
              <a v-if="item.file_bukti_menginap" :href="item.file_bukti_menginap" target="_blank" class="block px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100">Download Bukti Menginap</a>
            </div>
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
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Status</h2>
            <div class="mb-4"><StatusBadge :status="item.status" :label="item.status_label" /></div>
            <select v-model="newStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white mb-3"><option value="baru">Baru</option><option value="diproses">Diproses</option><option value="disetujui">Disetujui</option><option value="ditolak">Ditolak</option><option value="selesai">Selesai</option></select>
            <button @click="updateStatus" :disabled="newStatus === item.status" class="w-full px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 disabled:opacity-50">Simpan</button>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Hubungi via WhatsApp</h2>
            <a :href="whatsappUrl" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
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
  const msg = encodeURIComponent(`Yth. ${item.value.nama},\n\nKami dari Sekretariat DPRD Kab. Kulon Progo menindaklanjuti pendaftaran kunjungan ke Setwan melalui sistem GESIT.\n\nMohon kesediaannya untuk konfirmasi jadwal.\n\nTerima kasih.`);
  const prefix = phone?.startsWith('62') ? '' : '62';
  return `https://wa.me/${prefix}${phone}?text=${msg}`;
});

onMounted(async () => {
  try { const res = await api.get(`/admin/tamu-setwan/${route.params.id}`); item.value = res.data.data; newStatus.value = item.value.status; } catch (e) { console.error(e); } finally { loading.value = false; }
});

async function updateStatus() {
  try { const res = await api.patch(`/admin/tamu-setwan/${route.params.id}/status`, { status: newStatus.value }); item.value = res.data.data; } catch (e) { console.error(e); }
}

async function addNote() {
  if (!newNote.value.trim()) return;
  try { const res = await api.post(`/admin/notes/setwan/${route.params.id}`, { note: newNote.value }); item.value.notes.push(res.data.data); newNote.value = ''; } catch (e) { console.error(e); }
}
</script>
