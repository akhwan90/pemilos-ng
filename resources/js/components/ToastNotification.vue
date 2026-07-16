<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[60] flex flex-col gap-2 max-w-sm">
      <TransitionGroup 
        enter-active-class="transition duration-300 ease-out" 
        enter-from-class="transform translate-x-full opacity-0" 
        enter-to-class="transform translate-x-0 opacity-100" 
        leave-active-class="transition duration-200 ease-in" 
        leave-from-class="transform translate-x-0 opacity-100" 
        leave-to-class="transform translate-x-full opacity-0"
      >
        <div v-for="toast in toasts" :key="toast.id" :class="[
          'p-4 rounded-lg shadow-lg border whitespace-pre-line',
          toast.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700'
        ]">
          <div class="flex justify-between items-start">
            <div>{{ toast.message }}</div>
            <button @click="removeToast(toast.id)" class="ml-4 text-gray-400 hover:text-gray-600">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue';

const toasts = ref([]);
let nextId = 1;

function addToast(message, type = 'error', duration = 5000) {
  const id = nextId++;
  toasts.value.push({ id, message, type });
  
  if (duration > 0) {
    setTimeout(() => {
      removeToast(id);
    }, duration);
  }
}

function removeToast(id) {
  const index = toasts.value.findIndex(t => t.id === id);
  if (index !== -1) {
    toasts.value.splice(index, 1);
  }
}

defineExpose({
  addToast,
  removeToast
});
</script>
