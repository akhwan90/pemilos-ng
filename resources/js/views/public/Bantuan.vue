<template>
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <PublicHeader />

        <!-- Main Content -->
        <main class="flex-grow pt-20 pb-16">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="text-center mb-12 mt-8">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4">
                        Pusat <span class="text-indigo-600">Bantuan</span>
                    </h1>
                    <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                        Temukan jawaban atas pertanyaan umum terkait penggunaan aplikasi e-Pemilos.
                    </p>
                </div>

                <!-- FAQ Section -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-2xl overflow-hidden">
                    <div class="divide-y divide-gray-100">
                        <!-- FAQ Item 1 -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Bagaimana cara menggunakan token pemilihan?
                            </h3>
                            <p class="text-gray-600 pl-7 leading-relaxed">
                                Anda harus datang ke TPS (Tempat Pemungutan Suara) atau Bilik Suara di sekolah Anda pada hari pelaksanaan pemilihan yang telah dijadwalkan. Petugas KPPS akan memberikan Anda Token (PIN) unik yang kemudian digunakan untuk login di Bilik Suara dan melakukan pencoblosan kandidat.
                            </p>
                        </div>

                        <!-- FAQ Item 2 -->
                        <div class="p-6 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Mengapa saya tidak bisa melihat hasil pemilihan secara langsung?
                            </h3>
                            <p class="text-gray-600 pl-7 leading-relaxed">
                                Untuk menjaga kerahasiaan dan integritas pemilihan, hasil perolehan suara (Real Count) tidak akan dipublikasikan sebelum jadwal waktu "Pelaksanaan Pemilihan" resmi berakhir dan sekolah mempublikasikannya. Silakan tunggu hingga Panitia Pemilihan / Admin mengumumkan hasilnya.
                            </p>
                        </div>

                        <!-- FAQ Item 3 -->
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Bagaimana jika nama saya tidak terdaftar di DPT?
                            </h3>
                            <p class="text-gray-600 pl-7 leading-relaxed">
                                Jika nama Anda tidak terdaftar pada Data Pemilih Tetap (DPT), harap segera hubungi Admin Sekolah, Panitia Pemilihan, atau Guru di sekolah Anda selama "Masa Sanggah DPS". Hanya panitia dengan otorisasi Admin yang bisa memasukkan nama Anda ke dalam sistem sebelum masa pencoblosan dimulai.
                            </p>
                        </div>
                        
                        <!-- FAQ Item 4 -->
                        <div class="p-6 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Saya lupa password Admin Sekolah, apa yang harus dilakukan?
                            </h3>
                            <p class="text-gray-600 pl-7 leading-relaxed">
                                Untuk alasan keamanan tingkat tinggi, Admin Sekolah yang lupa kata sandinya harus menghubungi <strong>Super Admin</strong> dari Dinas atau pihak Instansi yang menaungi e-Pemilos secara langsung untuk melakukan reset password akun.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="mt-12 text-center bg-indigo-50 rounded-2xl p-8 border border-indigo-100">
                    <h2 class="text-xl font-bold text-indigo-900 mb-3">
                        Masih butuh bantuan?
                    </h2>
                    <p class="text-indigo-700 mb-6">
                        Tim teknis kami selalu siap membantu masalah Anda seputar kendala sistem aplikasi.
                    </p>
                    <a href="mailto:support@kulonprogokab.go.id" class="inline-flex items-center gap-2 bg-indigo-600 text-white font-semibold py-3 px-6 rounded-xl hover:bg-indigo-700 shadow-sm transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Hubungi Support
                    </a>
                </div>
            </div>
        </main>

        <PublicFooter />
    </div>
</template>

<script setup>
import PublicHeader from "../../components/public/PublicHeader.vue";
import PublicFooter from "../../components/public/PublicFooter.vue";
</script>
