import re

with open('/home/akhwan/Documents/docker/pemilos-ng/resources/js/views/admin_sekolah/LaporanD1.vue', 'r') as f:
    content = f.read()

old_btn = """<!-- <button v-if="hasilData" type="button" @click="cetakC1" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Buka Mode Cetak (Print)
                    </button> -->"""

new_btn = """<button v-if="hasilData" type="button" @click="cetakD1" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Buka Mode Cetak (Print D1)
                    </button>"""

content = content.replace(old_btn, new_btn)

# add cetakD1 function
script_old = """const fetchHasil = async () => {"""
script_new = """const cetakD1 = () => {
    window.open('/admin-sekolah/print-d1', '_blank');
}

const fetchHasil = async () => {"""

if 'const cetakD1 =' not in content:
    content = content.replace(script_old, script_new)

with open('/home/akhwan/Documents/docker/pemilos-ng/resources/js/views/admin_sekolah/LaporanD1.vue', 'w') as f:
    f.write(content)
