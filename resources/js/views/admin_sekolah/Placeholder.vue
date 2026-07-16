<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">{{ title }}</h2>
        <p class="text-sm text-gray-500">Halaman ini sedang dalam tahap pengembangan.</p>
      </div>
    </div>
    <BaseCard class="p-12 text-center text-gray-500 bg-gray-50 border-dashed">
      <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
      </svg>
      Under Construction
    </BaseCard>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import BaseCard from '../../components/BaseCard.vue';

const route = useRoute();
const title = computed(() => {
  return route.name.replace('admin-sekolah-', '').replace(/-/g, ' ').toUpperCase();
});
</script>
