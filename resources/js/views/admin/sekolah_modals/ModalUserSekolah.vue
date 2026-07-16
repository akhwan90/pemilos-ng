<template>
  <BaseModal v-model="isOpen" title="Manajemen User Sekolah (Level 2)" max-width="3xl">
    <div v-if="npsn" class="space-y-6">
      
      <!-- Form Tambah User -->
      <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Tambah User Baru</h4>
        <form @submit.prevent="submitForm" class="flex flex-col sm:flex-row gap-3 items-end">
          <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Level Akun</label>
            <select v-model="form.level" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
              <option value="2">Admin Sekolah (Level 2)</option>
              <option value="3">Admin TPS (Level 3)</option>
            </select>
          </div>
          <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Username (Min 6 Karakter)</label>
            <input v-model="form.username" type="text" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan username unik">
          </div>
          <div class="flex-1 w-full">
            <label class="block text-xs font-medium text-gray-600 mb-1">Password (Min 6 Karakter)</label>
            <input v-model="form.password" type="password" required minlength="6" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan password">
          </div>
          <div class="w-full sm:w-auto">
            <BaseButton type="submit" variant="primary" :loading="isSubmitting" class="w-full sm:w-auto h-[38px]">
              Simpan
            </BaseButton>
          </div>
        </form>
      </div>

      <!-- Tabel List User -->
      <div>
        <h4 class="text-sm font-semibold text-gray-700 mb-2">Daftar Akun yang Terdaftar</h4>
        <div class="overflow-hidden border border-gray-200 rounded-lg">
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
              <tr>
                <th class="px-4 py-3 w-12 text-center">No</th>
                <th class="px-4 py-3">Username</th>
                <th class="px-4 py-3">Level</th>
                <th class="px-4 py-3 w-40 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading" class="bg-white">
                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat data...</td>
              </tr>
              <tr v-else-if="users.length === 0" class="bg-white">
                <td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada user yang terdaftar untuk sekolah ini.</td>
              </tr>
              <tr v-else v-for="(user, index) in users" :key="user.id" class="bg-white border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-center">{{ index + 1 }}</td>
                <td class="px-4 py-3 font-medium">{{ user.username }}</td>
                <td class="px-4 py-3">
                  <span v-if="user.level == 2" class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                    Admin Sekolah
                  </span>
                  <span v-else-if="user.level == 3" class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                    Admin TPS
                  </span>
                  <span v-else class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                    Level {{ user.level }}
                  </span>
                </td>
                <td class="px-4 py-3 text-center">
                  <div class="flex gap-2 justify-center">
                    <button @click="promptResetPassword(user)" class="text-xs text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-2 py-1 rounded">Reset Pass</button>
                    <button @click="deleteUser(user.id)" class="text-xs text-red-600 hover:text-red-900 bg-red-50 px-2 py-1 rounded">Hapus</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </BaseModal>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';
import BaseButton from '../../../components/BaseButton.vue';
import api from '../../../services/api';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  npsn: { type: [String, Number], default: null }
});

const emit = defineEmits(['update:modelValue']);

// Simple toast mock (since composable doesn't exist yet)
const toast = {
  success: (msg) => alert('SUKSES: ' + msg),
  error: (msg) => alert('ERROR: ' + msg)
};

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const users = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);

const form = ref({
  username: '',
  password: '',
  level: '2'
});

// Watch when modal opens and npsn is available
watch(() => isOpen.value, (newVal) => {
  if (newVal && props.npsn) {
    fetchUsers();
    form.value.username = '';
    form.value.password = '';
    form.value.level = '2';
  }
});

async function fetchUsers() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin/data-sekolah/${props.npsn}/users`);
    users.value = res.data.data;
  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
  }
}

async function submitForm() {
  isSubmitting.value = true;
  try {
    await api.post(`/admin/data-sekolah/${props.npsn}/users`, form.value);
    toast.success('User berhasil ditambahkan');
    form.value.username = '';
    form.value.password = '';
    fetchUsers(); // Refresh tabel
  } catch (error) {
    if (error.response?.data?.message) {
      toast.error(error.response.data.message);
    } else {
      toast.error('Gagal menyimpan user');
    }
  } finally {
    isSubmitting.value = false;
  }
}

async function promptResetPassword(user) {
  const newPass = prompt(`Masukkan password baru untuk user ${user.username} (Min 6 Karakter):`);
  if (newPass === null || newPass.trim() === '') return;
  if (newPass.length < 6) {
    alert('Password minimal 6 karakter');
    return;
  }

  try {
    await api.put(`/admin/data-sekolah/${props.npsn}/users/${user.id}`, { password: newPass });
    toast.success('Password berhasil direset');
  } catch (error) {
    toast.error('Gagal reset password');
  }
}

async function deleteUser(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return;
  
  try {
    await api.delete(`/admin/data-sekolah/${props.npsn}/users/${id}`);
    toast.success('User berhasil dihapus');
    fetchUsers();
  } catch (error) {
    toast.error('Gagal menghapus user');
  }
}
</script>
