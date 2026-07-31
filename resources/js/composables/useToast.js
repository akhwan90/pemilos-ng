import { ref } from 'vue';

// Global state untuk toast
const toasts = ref([]);
let nextId = 1;

export function useToast() {
	function addToast(message, type = 'success', duration = 5000) {
		const id = nextId++;
		toasts.value.push({ id, message, type });

		if (duration > 0) {
			setTimeout(() => {
				removeToast(id);
			}, duration);
		}
	}

	function removeToast(id) {
		const index = toasts.value.findIndex((t) => t.id === id);
		if (index !== -1) {
			toasts.value.splice(index, 1);
		}
	}

	function success(message, duration = 5000) {
		addToast(message, 'success', duration);
	}

	function error(message, duration = 5000) {
		addToast(message, 'error', duration);
	}

	return {
		toasts,
		addToast,
		removeToast,
		success,
		error,
	};
}
