<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <select 
      :value="modelValue" 
      @change="$emit('update:modelValue', $event.target.value)"
      :required="required"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm bg-white"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="option[valueKey]" :value="option[valueKey]">
        {{ option[labelKey] }}
      </option>
    </select>
  </div>
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  label: String,
  options: { type: Array, required: true },
  valueKey: { type: String, default: 'id' },
  labelKey: { type: String, default: 'nama' },
  required: { type: Boolean, default: false },
  placeholder: { type: String, default: '-- Pilih --' },
});
defineEmits(['update:modelValue']);
</script>
