<template>
    <div class="print-container bg-white text-black p-8 font-serif w-full max-w-4xl mx-auto">
        <!-- Skeleton Loader -->
        <div v-if="loading" class="text-center py-20 print:hidden">
            <p>Memuat data cetakan...</p>
        </div>

        <div v-else class="content-wrapper">
            <div class="border border-black p-2 w-max ml-auto text-center mb-6 font-bold uppercase">
                MODEL C2-PPO
            </div>

            <h4 class="text-center font-bold mb-6">
                PERNYATAAN KEBERATAN SAKSI ATAU CATATAN KEJADIAN KHUSUS PEMUNGUTAN<br />
                SUARA DAN PENGHITUNGAN SUARA PEMILIHAN OSIS TAHUN {{ tahun }}
            </h4>

            <p class="mb-4 font-bold">
                Nama TPS : {{ tpsName }}
            </p>

            <p class="mb-4">
                Pernyataan keberatan oleh saksi/catatan kejadian khusus *) sebagai berikut:
            </p>
            
            <div class="mb-8 min-h-[150px]">
                <template v-if="!c2Data.ada_kejadian || c2Data.kejadian.length === 0">
                    <p class="italic">NIHIL</p>
                </template>
                <template v-else>
                    <ol class="list-decimal pl-5 space-y-3">
                        <li v-for="(item, index) in c2Data.kejadian" :key="index">
                            <strong>Waktu:</strong> {{ item.waktu || '-' }} | <strong>Pelapor:</strong> {{ item.pelapor || '-' }}<br>
                            {{ item.uraian }}
                        </li>
                    </ol>
                </template>
            </div>

            <table class="w-full text-center border-none mt-10">
                <tbody>
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-right pb-4">Kulon Progo, {{ currentDate }}</td>
                    </tr>
                    <tr>
                        <td class="w-10"></td>
                        <td class="w-[35%] align-top text-center">
                            YANG MENGAJUKAN KEBERATAN<br />
                            SAKSI<br />
                            <br /><br /><br /><br /><br /><br /><br />
                            (<span v-if="c2Data.kejadian.length > 0 && c2Data.kejadian[0].pelapor">{{ c2Data.kejadian[0].pelapor.toUpperCase() }}</span>
                            <span v-else-if="perangkatSaksi1">{{ perangkatSaksi1.toUpperCase() }}</span>
                            <span v-else>........................................</span>)
                        </td>
                        <td class="w-10"></td>
                        <td class="w-[35%] align-top text-center">
                            KELOMPOK PENYELENGGARA PEMUNGUTAN SUARA<br />
                            KETUA<br />
                            <br /><br /><br /><br /><br /><br /><br />
                            (<span v-if="perangkatKetua">{{ perangkatKetua.toUpperCase() }}</span>
                            <span v-else>........................................</span>)
                        </td>
                        <td class="w-10"></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-16 text-sm">
                <b>Keterangan: </b>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>*) Coret yang tidak perlu.</li>
                    <li>Apabila terdapat Kejadian Khusus, dicatat dan ditandatangani oleh Ketua KPPS.</li>
                    <li>Apabila terdapat pernyataan Keberatan Saksi, dicatat oleh Saksi dan ditandatangani bersama oleh Saksi dan Ketua KPPS.</li>
                    <li>Apabila tidak terdapat Kejadian Khusus atau pernyataan Keberatan Saksi, dicatat dengan kalimat “NIHIL” dan ditandatangani oleh Ketua KPPS.</li>
                </ul>
            </div>
            
            <div class="mt-10 text-xs text-gray-500 text-right font-sans print:block hidden">
                Dicetak oleh sistem pada: {{ currentDateTime }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../../services/api';
import moment from 'moment';

const loading = ref(true);
const tpsName = ref('');
const perangkatKetua = ref('');
const perangkatSaksi1 = ref('');
const tahun = ref(moment().format('YYYY'));
const c2Data = ref({ ada_kejadian: false, kejadian: [] });
const currentDate = ref(moment().locale('id').format('DD MMMM YYYY'));
const currentDateTime = ref(moment().locale('id').format('DD MMMM YYYY, HH:mm:ss'));

onMounted(() => {
    document.body.style.backgroundColor = 'white';
    fetchData();
});

async function fetchData() {
    try {
        // Cukup 1 kali request ke /admin-tps/c2 yang sudah berisi info lengkap
        const res = await api.get('/admin-tps/c2');
        
        if (res.data.success) {
            const data = res.data.data;
            
            // Assign Info TPS
            if (data.tps_info) {
                tpsName.value = data.tps_info.nama_kelas;
                tahun.value = data.tps_info.tahun;
            }
            
            // Assign Perangkat TPS
            if (data.perangkat_tps) {
                if (data.perangkat_tps.ketua && data.perangkat_tps.ketua.nama) {
                    perangkatKetua.value = data.perangkat_tps.ketua.nama;
                }
                if (data.perangkat_tps.saksi && data.perangkat_tps.saksi.length > 0 && data.perangkat_tps.saksi[0].nama) {
                    perangkatSaksi1.value = data.perangkat_tps.saksi[0].nama;
                }
            }
            
            // Assign Data C2
            if (data.c2_config) {
                c2Data.value = data.c2_config;
            }
        }

        setTimeout(() => {
            window.print();
        }, 1000);
    } catch (error) {
        console.error('Error memuat data cetak C2:', error);
    } finally {
        loading.value = false;
    }
}
</script>

<style>
@media print {
    @page { margin: 1cm; }
    /* Override sudah diatur global di app.css, ini untuk spesifik komponen bila perlu */
    .print-container { overflow: visible !important; height: auto !important; }
}
</style>
