<template>
  <span :class="[
    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
    badgeClass
  ]">
    {{ label || status }}
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  status: { type: String, default: 'baru' },
  label: { type: String, default: '' },
});

const badgeClass = computed(() => {
  const map = {
    baru: 'bg-blue-100 text-blue-800',
    diproses: 'bg-yellow-100 text-yellow-800',
    disetujui: 'bg-green-100 text-green-800',
    ditolak: 'bg-red-100 text-red-800',
    selesai: 'bg-gray-100 text-gray-800',
  };
  return map[props.status] || 'bg-gray-100 text-gray-800';
});
</script>
