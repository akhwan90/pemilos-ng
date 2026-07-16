<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <img src="/images/logo_gesit.png" alt="Logo GESIT" class="w-10 h-10 object-contain" />
          <div>
            <h1 class="text-xl font-bold text-gray-800">GESIT</h1>
            <p class="text-xs text-gray-500 hidden md:block">Government Electronic Service and Information Transformation</p>
          </div>
        </div>
        <a href="/admin/login" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          Admin
        </a>
      </div>
    </header>

    <!-- Hero -->
    <section class="max-w-6xl mx-auto px-4 py-16 text-center">
      <div class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full text-sm font-medium mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Sistem Informasi Pelayanan Publik
      </div>
      <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
        Selamat Datang di <span class="text-indigo-600">GESIT</span>
      </h2>
      <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-12">
        Government Electronic Service and Information Transformation — 
        Kemudahan akses layanan informasi publik di lingkungan Sekretariat DPRD.
      </p>
    </section>

    <!-- Menu Cards -->
    <section class="max-w-6xl mx-auto px-4 pb-16">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Aduan & Aspirasi -->
        <router-link to="/aduan-aspirasi" class="block h-full">
          <BaseCard class="h-full hover:shadow-md hover:border-indigo-300 transition-all duration-200 group cursor-pointer" no-padding>
            <div class="p-6">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-red-200 transition-colors">
                  <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-800 group-hover:text-indigo-600">Aduan & Aspirasi</h3>
                  <p class="text-sm text-gray-500 mt-1">Sampaikan keluhan, kritik, dan aspirasi kepada pemerintah daerah.</p>
                  <span class="inline-flex items-center text-sm font-medium text-indigo-600 mt-3 group-hover:gap-2 transition-all">
                    Ajukan Sekarang
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </BaseCard>
        </router-link>

        <!-- Card 2: Tamu Setwan -->
        <router-link to="/tamu-setwan" class="block h-full">
          <BaseCard class="h-full hover:shadow-md hover:border-indigo-300 transition-all duration-200 group cursor-pointer" no-padding>
            <div class="p-6">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">Tamu Setwan</h3>
                  <p class="text-sm text-gray-500 mt-1">Daftarkan kunjungan kerja resmi ke Sekretariat Dewan (Setwan).</p>
                  <span class="inline-flex items-center text-sm font-medium text-blue-600 mt-3 group-hover:gap-2 transition-all">
                    Daftar Sekarang
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </BaseCard>
        </router-link>

        <!-- Card 3: Tamu DPRD -->
        <router-link to="/tamu-dprd" class="block h-full">
          <BaseCard class="h-full hover:shadow-md hover:border-indigo-300 transition-all duration-200 group cursor-pointer" no-padding>
            <div class="p-6">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-200 transition-colors">
                  <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-800 group-hover:text-emerald-600">Tamu DPRD</h3>
                  <p class="text-sm text-gray-500 mt-1">Daftarkan kunjungan kerja spesifik ke Alat Kelengkapan Dewan (Alkap).</p>
                  <span class="inline-flex items-center text-sm font-medium text-emerald-600 mt-3 group-hover:gap-2 transition-all">
                    Daftar Sekarang
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </BaseCard>
        </router-link>

        <!-- Card 4: Permohonan Audiensi -->
        <router-link to="/permohonan-audiensi" class="block h-full">
          <BaseCard class="h-full hover:shadow-md hover:border-indigo-300 transition-all duration-200 group cursor-pointer" no-padding>
            <div class="p-6">
              <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 transition-colors">
                  <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-800 group-hover:text-amber-600">Permohonan Audiensi</h3>
                  <p class="text-sm text-gray-500 mt-1">Ajukan permohonan pertemuan formal (audiensi) dengan dewan atau pejabat.</p>
                  <span class="inline-flex items-center text-sm font-medium text-amber-600 mt-3 group-hover:gap-2 transition-all">
                    Ajukan Sekarang
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                </div>
              </div>
            </div>
          </BaseCard>
        </router-link>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8">
      <div class="max-w-6xl mx-auto px-4 text-center text-sm text-gray-500">
        <p>&copy; 2026 GESIT - Government Electronic Service and Information Transformation</p>
        <p class="mt-1">Sekretariat DPRD Kabupaten Kulon Progo</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import BaseCard from '../../components/BaseCard.vue';
</script>
