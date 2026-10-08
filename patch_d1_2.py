import re

with open('/home/akhwan/Documents/docker/pemilos-ng/resources/js/views/admin_sekolah/LaporanD1.vue', 'r') as f:
    content = f.read()

table_4_pattern = r'<!-- Rekap Akhir -->.*?</table>'

table_4_new = """<!-- Rekap Akhir -->
                    <div class="mb-8">
                        <h3 class="font-bold text-gray-800 bg-gray-50 p-3 rounded-t-lg border border-gray-200">IV. DATA SUARA SAH DAN TIDAK SAH</h3>
                        <div class="border border-t-0 border-gray-200 rounded-b-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-200" style="width: 40%">Uraian</th>
                                        <th v-for="(tps, index) in hasilData.hasil" :key="index" class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-200">
                                            {{ tps.nama_tps.substring(0, 5) }}
                                        </th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-800 uppercase tracking-wider bg-indigo-50">
                                            Jumlah Akhir
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700 border-r border-gray-200">A. Jumlah Suara Sah (Total dari seluruh Paslon)</td>
                                        <td v-for="(tps, index) in hasilData.hasil" :key="index" class="px-4 py-3 text-center font-bold border-r border-gray-200">
                                            {{ tps.hasil.statistik.suara_sah }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-indigo-700 bg-indigo-50">
                                            {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.statistik.suara_sah || 0), 0) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700 border-r border-gray-200">B. Jumlah Suara Tidak Sah</td>
                                        <td v-for="(tps, index) in hasilData.hasil" :key="index" class="px-4 py-3 text-center font-bold border-r border-gray-200">
                                            {{ tps.hasil.statistik.suara_tidak_sah }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-indigo-700 bg-indigo-50">
                                            {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.statistik.suara_tidak_sah || 0), 0) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-bold text-gray-900 bg-gray-50 border-r border-gray-200">C. TOTAL KESELURUHAN (A + B)</td>
                                        <td v-for="(tps, index) in hasilData.hasil" :key="index" class="px-4 py-3 text-center font-bold bg-gray-50 border-r border-gray-200">
                                            {{ parseInt(tps.hasil.statistik.suara_sah) + parseInt(tps.hasil.statistik.suara_tidak_sah) }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-indigo-700 bg-indigo-100 border-t border-gray-200">
                                            {{ hasilData.hasil.reduce((sum, t) => sum + parseInt(t.hasil.statistik.suara_sah || 0) + parseInt(t.hasil.statistik.suara_tidak_sah || 0), 0) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>"""

new_content = re.sub(table_4_pattern, table_4_new, content, flags=re.DOTALL)

with open('/home/akhwan/Documents/docker/pemilos-ng/resources/js/views/admin_sekolah/LaporanD1.vue', 'w') as f:
    f.write(new_content)

print("Patch Table 4 Executed.")
