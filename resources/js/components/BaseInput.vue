<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <input 
      :value="modelValue" 
      @input="$emit('update:modelValue', $event.target.value)"
      :type="type" 
      :required="required"
      :maxlength="maxlength"
      :step="step"
      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm"
      :placeholder="placeholder"
    />
  </div>
</template>

<script setup>
defineProps({
  modelValue: [String, Number],
  label: String,
  type: { type: String, default: 'text' },
  required: { type: Boolean, default: false },
  maxlength: [String, Number],
  placeholder: String,
  step: [String, Number],
});
defineEmits(['update:modelValue']);
</script>
