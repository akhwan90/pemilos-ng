<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <textarea 
      :value="modelValue" 
      @input="$emit('update:modelValue', $event.target.value)"
      :required="required"
      :rows="rows"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
      :placeholder="placeholder"
    ></textarea>
  </div>
</template>

<script setup>
defineProps({
  modelValue: String,
  label: String,
  required: { type: Boolean, default: false },
  rows: { type: [String, Number], default: 4 },
  placeholder: String,
});
defineEmits(['update:modelValue']);
</script>
