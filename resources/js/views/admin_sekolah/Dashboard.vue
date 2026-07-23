<template>
    <div class="space-y-6 max-w-5xl mx-auto py-6">
        <!-- Hero / Banner Section -->
        <div
            class="bg-white rounded-2xl shadow-sm border border-indigo-100 overflow-hidden relative"
        >
            <div
                class="absolute inset-0 bg-gradient-to-r from-indigo-50 to-blue-50 opacity-50 pointer-events-none"
            ></div>

            <div class="p-8 md:p-10 relative z-10">
                <div class="flex items-center gap-4 mb-6">
                    <div class="bg-indigo-600 rounded-xl p-3 shadow-md">
                        <svg
                            class="w-8 h-8 text-white"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h1
                            class="text-3xl font-black text-gray-900 tracking-tight"
                        >
                            Dashboard Admin
                            <span class="text-indigo-600">{{
                                namaSekolah
                            }}</span>
                        </h1>
                        <p class="text-indigo-600 font-semibold mt-1">
                            Sistem e-Pemilos Kabupaten Kulon Progo
                        </p>
                    </div>
                </div>

                <div
                    class="prose prose-indigo max-w-none text-gray-600 text-justify leading-relaxed"
                >
                    <p>
                        <strong>e-Pemilos</strong> adalah sebuah aplikasi yang
                        diinisiasi dan didesain oleh
                        <strong
                            >Komisi Pemilihan Umum Kabupaten Kulon Progo</strong
                        >
                        sejak Tahun 2020 yang disesuaikan dengan format dan
                        tahapan penyelenggaraan Pemilu. Aplikasi ini dibuat oleh
                        <strong
                            >Dinas Komunikasi dan Informatika Kabupaten Kulon
                            Progo</strong
                        >, dan didukung oleh Badan Kesatuan Bangsa dan Politik
                        Kabupaten Kulon Progo, Dinas Pendidikan Kabupaten Kulon
                        Progo, Balai Pendidikan Menengah Kabupaten Kulon Progo,
                        dan Kantor Kementerian Agama Kabupaten Kulon Progo.
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistik Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden"
            >
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg
                            class="w-8 h-8 text-blue-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Siswa</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.jml_siswa }}
                        </p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg
                        v-if="stats.jml_siswa > 0"
                        class="w-6 h-6 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden"
            >
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-emerald-100 rounded-lg">
                        <svg
                            class="w-8 h-8 text-emerald-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">TPS</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.jml_tps }}
                        </p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg
                        v-if="stats.jml_tps > 0"
                        class="w-6 h-6 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden"
            >
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg
                            class="w-8 h-8 text-purple-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Calon</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.jml_kandidat }}
                        </p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg
                        v-if="stats.jml_kandidat > 0"
                        class="w-6 h-6 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden"
            >
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-cyan-100 rounded-lg">
                        <svg
                            class="w-8 h-8 text-cyan-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">DPT</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ stats.jml_dpt }}
                        </p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg
                        v-if="stats.jml_dpt > 0"
                        class="w-6 h-6 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6 text-red-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Jadwal Pelaksanaan Section -->
        <div class="mt-10">
            <h2
                class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-3"
            >
                <svg
                    class="w-6 h-6 text-indigo-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    ></path>
                </svg>
                Timeline Pelaksanaan Pemilos
            </h2>

            <div
                v-if="isLoading"
                class="py-10 text-center text-gray-500 animate-pulse"
            >
                Memeriksa jadwal pemilihan...
            </div>

            <div
                v-else-if="jadwal.length === 0"
                class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-yellow-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Belum ada pengaturan jadwal pemilihan dari Super
                            Admin untuk sekolah / jenjang Anda.
                        </p>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="relative mt-8"
            >
                <!-- Garis Vertikal Timeline -->
                <div class="absolute left-4 md:left-8 top-0 bottom-0 w-1 bg-gray-200 rounded-full z-8"></div>

                <div class="space-y-8">
                    <div
                        v-for="j in jadwal"
                        :key="j.id"
                        class="relative pl-12 md:pl-20"
                    >
                        <!-- Bulatan Status Timeline -->
                        <div
                            class="absolute left-2.5 md:left-[1.376rem] top-4 w-6 h-6 rounded-full shadow-sm z-10"
                            :class="{
                                'bg-green-500 ring-2 ring-green-300': j.status === 'aktif',
                                'bg-blue-500': j.status === 'akan_datang',
                                'bg-gray-400': j.status === 'selesai'
                            }"
                        ></div>

                        <div
                            class="bg-white rounded-xl border p-5 shadow-sm relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between"
                            :class="{
                                'border-green-400 ring-1 ring-green-400 bg-green-50':
                                    j.status === 'aktif',
                                'border-gray-200': j.status !== 'aktif',
                            }"
                        >
                            <!-- Kiri: Judul dan Status Badge -->
                            <div class="mb-4 md:mb-0">
                                <h3 class="font-bold text-gray-800 text-lg mb-2 capitalize">
                                    {{ formatJenis(j.jenis) }}
                                </h3>

                                <span
                                    v-if="j.status === 'aktif'"
                                    class="inline-block bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider"
                                >
                                    Sedang Berlangsung
                                </span>
                                <span
                                    v-else-if="j.status === 'selesai'"
                                    class="inline-block bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider"
                                >
                                    Selesai
                                </span>
                                <span
                                    v-else
                                    class="inline-block bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider"
                                >
                                    Akan Datang
                                </span>
                            </div>

                            <!-- Kanan: Waktu -->
                            <div class="space-y-3 flex-shrink-0 md:text-right">
                                <div class="flex items-center md:justify-end gap-3">
                                    <div class="md:hidden mt-0.5 bg-gray-100 p-1.5 rounded text-gray-500">
                                        <!-- Flag (Start) -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18M3 7l9-4 9 4-9 4-9-4z"></path></svg>
                                    </div>
                                    <div class="flex flex-col md:items-end">
                                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">
                                            Mulai
                                        </p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ formatDate(j.waktu_mulai) }}
                                        </p>
                                    </div>
                                    <div class="hidden md:block mt-0.5 bg-gray-100 p-1.5 rounded text-gray-500">
                                        <!-- Flag (Start) -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18M3 7l9-4 9 4-9 4-9-4z"></path></svg>
                                    </div>
                                </div>

                                <div class="flex items-center md:justify-end gap-3">
                                    <div class="md:hidden mt-0.5 bg-gray-100 p-1.5 rounded text-gray-500">
                                        <!-- Checkered Flag (Finish) -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18m0-14h18M3 11h18M7 7v8m4-8v8m4-8v8m4-8v8M7 3v4m4-4v4m4-4v4m4-4v4M7 11v4m4-4v4m4-4v4m4-4v4"></path></svg>
                                    </div>
                                    <div class="flex flex-col md:items-end">
                                        <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide">
                                            Selesai
                                        </p>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ formatDate(j.waktu_selesai) }}
                                        </p>
                                    </div>
                                    <div class="hidden md:block mt-0.5 bg-gray-100 p-1.5 rounded text-gray-500">
                                        <!-- Checkered Flag (Finish) -->
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18m0-14h18M3 11h18M7 7v8m4-8v8m4-8v8m4-8v8M7 3v4m4-4v4m4-4v4m4-4v4M7 11v4m4-4v4m4-4v4m4-4v4"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../services/api";
import { useToast } from "../../composables/useToast";

const toast = useToast();

const namaSekolah = ref("");
const jadwal = ref([]);
const stats = ref({
    jml_siswa: 0,
    jml_tps: 0,
    jml_kandidat: 0,
    jml_dpt: 0,
});
const isLoading = ref(true);

onMounted(() => {
    fetchDashboardInfo();
});

function formatDate(dateString) {
    if (!dateString) return "-";
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    };
    return new Date(dateString).toLocaleDateString("id-ID", options).replace(/\./g, ':');
}

function formatJenis(str) {
    if (!str) return '';
    return str.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

async function fetchDashboardInfo() {
    isLoading.value = true;
    try {
        const res = await api.get("/admin-sekolah/dashboard");
        namaSekolah.value = res.data.data.sekolah;
        jadwal.value = res.data.data.jadwal;
        if (res.data.data.stats) {
            stats.value = res.data.data.stats;
        }
    } catch (error) {
        toast.error("Gagal mengambil info dashboard");
    } finally {
        isLoading.value = false;
    }
}
</script>
