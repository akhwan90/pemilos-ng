<template>
  <BaseModal v-model="isOpen" title="Setting Jadwal Pemilihan" max-width="xl">
    <div v-if="npsn" class="space-y-4">
      <p class="text-sm text-gray-600">Setting jadwal untuk sekolah dengan NPSN: <strong>{{ npsn }}</strong></p>
      
      <div class="p-4 bg-gray-50 rounded-lg text-center text-sm text-gray-500 border border-dashed border-gray-300">
        Modul API Jadwal sedang dalam pengembangan...
      </div>
    </div>
  </BaseModal>
</template>

<script setup>
import { computed } from 'vue';
import BaseModal from '../../../components/BaseModal.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  npsn: { type: [String, Number], default: null }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});
</script>
