<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 max-w-sm pointer-events-none">
      <TransitionGroup 
        enter-active-class="transition duration-300 ease-out" 
        enter-from-class="transform translate-x-full opacity-0" 
        enter-to-class="transform translate-x-0 opacity-100" 
        leave-active-class="transition duration-200 ease-in" 
        leave-from-class="transform translate-x-0 opacity-100" 
        leave-to-class="transform translate-x-full opacity-0"
      >
        <div v-for="toast in toasts" :key="toast.id" :class="[
          'p-4 rounded-lg shadow-lg border whitespace-pre-line pointer-events-auto',
          toast.type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700'
        ]">
          <div class="flex justify-between items-start gap-4">
            <div>{{ toast.message }}</div>
            <button @click="removeToast(toast.id)" class="text-gray-400 hover:text-gray-600">
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
import { useToast } from '../composables/useToast';

const { toasts, removeToast } = useToast();
</script>
