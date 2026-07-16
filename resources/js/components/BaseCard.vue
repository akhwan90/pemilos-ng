<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div v-if="$slots.header || title" class="p-6 border-b border-gray-100">
      <slot name="header">
        <h2 class="text-lg font-semibold text-gray-800">{{ title }}</h2>
      </slot>
    </div>
    <div :class="[noPadding ? '' : 'p-6']">
      <slot />
    </div>
    <div v-if="$slots.footer" class="p-6 border-t border-gray-100">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  noPadding: { type: Boolean, default: false },
});
</script>
