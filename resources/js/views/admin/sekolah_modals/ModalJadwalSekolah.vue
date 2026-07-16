<template>
  <BaseModal v-model="isOpen" title="Setting Jadwal Pemilihan" max-width="3xl">
    <div v-if="npsn" class="space-y-4">
      <div v-if="isLoading" class="p-8 text-center text-gray-500">
        Memuat data jadwal...
      </div>
      <form v-else @submit.prevent="submitForm">
        <div class="overflow-hidden border border-gray-200 rounded-lg">
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
              <tr>
                <th class="px-4 py-3 w-12 text-center">No</th>
                <th class="px-4 py-3">Jenis Kegiatan</th>
                <th class="px-4 py-3 w-48 text-center">Waktu Mulai</th>
                <th class="px-4 py-3 w-48 text-center">Waktu Selesai</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in settings" :key="item.jenis" class="bg-white border-b hover:bg-gray-50">
                <td class="px-4 py-3 text-center">{{ index + 1 }}</td>
                <td class="px-4 py-3 font-medium capitalize">{{ formatJenis(item.jenis) }}</td>
                <td class="px-4 py-3">
                  <input type="date" v-model="item.waktu_mulai" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
                </td>
                <td class="px-4 py-3">
                  <input type="date" v-model="item.waktu_selesai" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="mt-4 flex justify-end">
          <BaseButton type="submit" variant="primary" :loading="isSubmitting">
            Simpan Jadwal
          </BaseButton>
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
  npsn: { type: [String, Number], default: null }
});

const emit = defineEmits(['update:modelValue']);
const toast = useToast();

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const settings = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);

// Format helper text
function formatJenis(str) {
  return str.replace(/_/g, ' ');
}

watch(() => isOpen.value, (newVal) => {
  if (newVal && props.npsn) {
    fetchJadwal();
  }
});

async function fetchJadwal() {
  isLoading.value = true;
  try {
    const res = await api.get(`/admin/data-sekolah/${props.npsn}/jadwal`);
    settings.value = res.data.data;
  } catch (error) {
    toast.error('Gagal mengambil data jadwal');
  } finally {
    isLoading.value = false;
  }
}

async function submitForm() {
  isSubmitting.value = true;
  try {
    await api.post(`/admin/data-sekolah/${props.npsn}/jadwal`, { settings: settings.value });
    toast.success('Jadwal berhasil disimpan');
    isOpen.value = false; // Tutup modal setelah sukses
  } catch (error) {
    toast.error('Gagal menyimpan jadwal');
  } finally {
    isSubmitting.value = false;
  }
}
</script>
