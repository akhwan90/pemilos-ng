<template>
  <div class="relative" ref="dropdownRef">
    <!-- Trigger Button / Element -->
    <div @click="toggle" class="inline-block">
      <slot name="trigger"></slot>
    </div>
    
    <!-- Dropdown Content -->
    <div 
      v-show="isOpen" 
      :class="[
        widthClass, 
        alignClass, 
        'absolute mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50'
      ]"
    >
      <div class="py-1" @click="handleItemClick">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  widthClass: { type: String, default: 'w-48' },
  alignClass: { type: String, default: 'left-0' }, // 'left-0' or 'right-0'
  closeOnItemClick: { type: Boolean, default: true }
});

const isOpen = ref(false);
const dropdownRef = ref(null);

const toggle = () => {
  isOpen.value = !isOpen.value;
};

const close = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false;
  }
};

const handleItemClick = () => {
  if (props.closeOnItemClick) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', close);
});

onUnmounted(() => {
  document.removeEventListener('click', close);
});
</script>
