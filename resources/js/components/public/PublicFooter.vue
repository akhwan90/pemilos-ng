<template>
  <footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
      &copy; {{ currentYear }} KPU Kabupaten Kulon Progo. Hak cipta dilindungi undang-undang.
    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear();
</script>