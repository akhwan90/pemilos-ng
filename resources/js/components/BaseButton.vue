<template>
	<button
		:type="type"
		:disabled="disabled || loading"
		:class="[
			'px-4 py-2 font-medium rounded-lg transition-colors flex items-center justify-center gap-2',
			variant === 'primary' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : '',
			variant === 'secondary' ? 'bg-amber-600 text-white hover:bg-amber-700' : '',
			variant === 'success' ? 'bg-green-500 text-white hover:bg-green-600' : '',
			variant === 'outline' ? 'border border-gray-300 hover:bg-gray-50' : '',
			variant === 'danger' ? 'bg-red-500 text-white hover:bg-red-600' : '',
			disabled || loading ? 'opacity-50 cursor-not-allowed' : '',
			block ? 'w-full' : '',
		]"
		@click="$emit('click', $event)"
	>
		<svg v-if="loading" class="animate-spin h-5 w-5" viewBox="0 0 24 24">
			<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
			<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
		</svg>
		<slot />
	</button>
</template>

<script setup>
defineProps({
	type: { type: String, default: 'button' },
	disabled: { type: Boolean, default: false },
	loading: { type: Boolean, default: false },
	variant: { type: String, default: 'primary' },
	block: { type: Boolean, default: false },
});
defineEmits(['click']);
</script>
