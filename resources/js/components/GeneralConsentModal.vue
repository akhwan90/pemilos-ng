<template>
  <BaseModal :model-value="show" @update:model-value="$emit('update:show', $event)" title="Persetujuan Data Pribadi" max-width="md">
    <div class="text-sm text-gray-600 space-y-3">
      <p>
        Dengan melanjutkan, Anda menyetujui bahwa data pribadi yang Anda berikan dalam formulir ini akan dikumpulkan, disimpan, dan diproses oleh sistem GESIT.
      </p>
      <p>
        Data Anda akan digunakan secara eksklusif untuk keperluan tindak lanjut dari laporan, aduan, maupun permohonan yang Anda sampaikan, dan tidak akan disebarluaskan di luar ketentuan perundang-undangan yang berlaku terkait pelindungan data pribadi.
      </p>
    </div>
    
    <template #footer>
      <BaseButton variant="outline" @click="$emit('update:show', false)">Batal</BaseButton>
      <BaseButton variant="primary" @click="handleAgree" :loading="loading">Setuju & Lanjutkan</BaseButton>
    </template>
  </BaseModal>
</template>

<script setup>
import BaseModal from './BaseModal.vue';
import BaseButton from './BaseButton.vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:show', 'agree']);

function handleAgree() {
  emit('agree');
}
</script>