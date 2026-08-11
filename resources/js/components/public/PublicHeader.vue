<template>
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between"
        >
            <router-link to="/" class="flex items-center gap-4">
                <img
                    src="/images/kpu_logo.png"
                    alt="Logo KPU"
                    class="h-10 w-auto object-contain fallback-bg"
                    @error="
                        (e) => {
                            e.target.style.display = 'none';
                        }
                    "
                />
                <div>
                    <h1 class="text-xl font-bold text-gray-800 leading-tight">
                        e-PEMILOS
                    </h1>
                    <p class="text-xs text-gray-500 hidden md:block">
                        Kabupaten Kulon Progo
                    </p>
                </div>
            </router-link>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex gap-6">
                <router-link
                    to="/"
                    :class="[
                        route.path === '/'
                            ? 'text-blue-600'
                            : 'text-gray-600 hover:text-blue-600',
                        'text-sm font-medium transition-colors',
                    ]"
                >
                    Beranda
                </router-link>
                <router-link
                    to="/sekolah"
                    :class="[
                        route.path.startsWith('/sekolah') ||
                        route.path.startsWith('/detail-sekolah')
                            ? 'text-blue-600'
                            : 'text-gray-600 hover:text-blue-600',
                        'text-sm font-medium transition-colors',
                    ]"
                >
                    Sekolah
                </router-link>
                <router-link
                    to="/arsip"
                    :class="[
                        route.path.startsWith('/arsip')
                            ? 'text-blue-600'
                            : 'text-gray-600 hover:text-blue-600',
                        'text-sm font-medium transition-colors',
                    ]"
                >
                    Arsip
                </router-link>
                <router-link
                    to="/bantuan"
                    :class="[
                        route.path.startsWith('/bantuan')
                            ? 'text-blue-600'
                            : 'text-gray-600 hover:text-blue-600',
                        'text-sm font-medium transition-colors',
                    ]"
                >
                    Bantuan
                </router-link>
            </nav>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button
                    type="button"
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="text-gray-600 hover:text-gray-900 focus:outline-none"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            v-if="!isMobileMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Panel -->
        <div
            v-show="isMobileMenuOpen"
            class="md:hidden border-t border-gray-100 bg-white"
        >
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <router-link
                    to="/"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50"
                    @click="isMobileMenuOpen = false"
                    >Beranda</router-link
                >
                <router-link
                    to="/sekolah"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50"
                    @click="isMobileMenuOpen = false"
                    >Sekolah</router-link
                >
                <router-link
                    to="/arsip"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50"
                    @click="isMobileMenuOpen = false"
                    >Arsip</router-link
                >
                <router-link
                    to="/bantuan"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50"
                    @click="isMobileMenuOpen = false"
                    >Bantuan</router-link
                >
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const isMobileMenuOpen = ref(false);
</script>
