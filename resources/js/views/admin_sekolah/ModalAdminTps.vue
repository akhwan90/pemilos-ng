<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 transition-opacity">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
        <div>
          <h3 class="text-lg font-bold text-gray-800">Kelola Admin TPS</h3>
          <p class="text-sm text-gray-500">TPS: {{ tps?.nm_kelas }}</p>
        </div>
        <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6 overflow-y-auto flex-1 flex flex-col md:flex-row gap-6">
        <!-- Form Tambah/Edit -->
        <div class="w-full md:w-1/3">
          <h4 class="font-semibold text-gray-700 mb-3 text-sm border-b pb-2">
            {{ isEditing ? 'Ubah Password Admin' : 'Tambah Admin TPS' }}
          </h4>
          <form @submit.prevent="submitAdmin" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
              <input v-model="form.username" @input="form.username = form.username.replace(/\s+/g, '')" type="text" required :disabled="isEditing" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500 disabled:bg-gray-100 disabled:text-gray-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
              <div class="relative">
                <input v-model="form.password" @input="form.password = form.password.replace(/\s+/g, '')" :type="showPassword ? 'text' : 'password'" required class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-indigo-500 pr-10">
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700">
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
              </div>
            </div>
            <div class="flex gap-2">
              <BaseButton v-if="isEditing" @click="cancelEdit" type="button" variant="secondary" class="w-1/3 flex justify-center">
                Batal
              </BaseButton>
              <BaseButton type="submit" variant="primary" :loading="isSubmitting" :class="isEditing ? 'w-2/3' : 'w-full'">
                {{ isEditing ? 'Simpan' : 'Tambah Admin' }}
              </BaseButton>
            </div>
          </form>
        </div>

        <!-- List Admin -->
        <div class="w-full md:w-2/3">
          <h4 class="font-semibold text-gray-700 mb-3 text-sm border-b pb-2">Daftar Admin (Level 3)</h4>
          <div v-if="isLoading" class="py-4 text-center text-sm text-gray-500">Memuat data admin...</div>
          <div v-else-if="admins.length === 0" class="py-4 text-center text-sm text-gray-500 bg-gray-50 rounded border border-dashed border-gray-300">Belum ada admin untuk TPS ini.</div>

          <div v-else class="space-y-2 max-h-64 overflow-y-auto pr-2">
            <div v-for="admin in admins" :key="admin.id_admin" class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:border-indigo-300 transition-colors">
              <div>
                <p class="font-medium text-gray-800">{{ admin.username }}</p>
              </div>
              <div class="flex gap-1">
                <button @click="openEditPassword(admin)" class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-lg transition-colors" title="Ubah Password">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button @click="deleteAdmin(admin.id)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Admin">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import BaseButton from '../../components/BaseButton.vue';
import api from '../../services/api';
import { useToast } from '../../composables/useToast';

const props = defineProps({
  show: Boolean,
  tps: Object
});

const emit = defineEmits(['close']);
const toast = useToast();

const admins = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const isEditing = ref(false);
const currentAdminId = ref(null);
const showPassword = ref(false);

const form = ref({
  username: '',
  password: ''
});

watch(() => props.show, (newVal) => {
  if (newVal && props.tps) {
    fetchAdmins();
    cancelEdit();
  }
});

function close() {
  emit('close');
}

async function fetchAdmins() {
  if (!props.tps) return;
  isLoading.value = true;
  try {
    const res = await api.get(`/admin-sekolah/tps/${props.tps.kd_kelas}/admin`);
    admins.value = res.data.data;
  } catch (error) {
    toast.error('Gagal memuat daftar admin TPS');
  } finally {
    isLoading.value = false;
  }
}

async function submitAdmin() {
  isSubmitting.value = true;
  try {
    if (isEditing.value) {
      // Ubah Password
      await api.put(`/admin-sekolah/tps/${props.tps.kd_kelas}/admin/${currentAdminId.value}/password`, {
        password: form.value.password
      });
      toast.success('Password Admin TPS berhasil diubah');
    } else {
      // Tambah Admin
      await api.post(`/admin-sekolah/tps/${props.tps.kd_kelas}/admin`, form.value);
      toast.success('Admin TPS berhasil ditambahkan');
    }
    cancelEdit();
    fetchAdmins();
  } catch (error) {
    toast.error(error.response?.data?.message || 'Terjadi kesalahan saat menyimpan admin');
  } finally {
    isSubmitting.value = false;
  }
}

function openEditPassword(admin) {
  isEditing.value = true;
  currentAdminId.value = admin.id;
  form.value.username = admin.username;
  form.value.password = '';
}

function cancelEdit() {
  isEditing.value = false;
  currentAdminId.value = null;
  showPassword.value = false;
  form.value = { username: '', password: '' };
}

async function deleteAdmin(id_admin) {
  if (!confirm('Hapus admin TPS ini secara permanen?')) return;

  try {
    await api.delete(`/admin-sekolah/tps/${props.tps.kd_kelas}/admin/${id_admin}`);
    toast.success('Admin TPS berhasil dihapus');
    fetchAdmins();
  } catch (error) {
    toast.error('Gagal menghapus admin TPS');
  }
}
</script>
