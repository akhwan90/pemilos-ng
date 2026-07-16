<template>
  <BaseModal v-model="isOpen" title="Manajemen User (Level 2)" max-width="xl">
    <div v-if="npsn" class="space-y-4">
      <p class="text-sm text-gray-600">Menampilkan data user untuk sekolah dengan NPSN: <strong>{{ npsn }}</strong></p>
      
      <!-- Konten sesungguhnya nanti akan kita buat memanggil API / form create user -->
      <div class="p-4 bg-gray-50 rounded-lg text-center text-sm text-gray-500 border border-dashed border-gray-300">
        Modul API User sedang dalam pengembangan...
      </div>
    </div>
  </BaseModal>
</template>

<script setup>
import { computed } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  npsn: { type: String, default: null }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});
</script>
