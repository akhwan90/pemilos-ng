<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <input 
      type="file" 
      :accept="accept" 
      @change="(e) => $emit('change', e.target.files[0])" 
      :required="required" 
      class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" 
    />
  </div>
</template>

<script setup>
defineProps({
  label: String,
  accept: String,
  required: { type: Boolean, default: false },
});
defineEmits(['change']);
</script>
