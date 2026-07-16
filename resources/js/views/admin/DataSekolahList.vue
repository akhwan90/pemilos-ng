<template>
  <div class="p-6">
        <table class="w-full text-sm text-left">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 w-16 text-center">No</th>
              <th class="px-4 py-3 w-24">Aksi</th>
              <th class="px-4 py-3">Nama Sekolah</th>
              <th class="px-4 py-3">NPSN</th>
              <th class="px-4 py-3 text-center">Kandidat</th>
              <th class="px-4 py-3 text-center">TPS</th>
              <th class="px-4 py-3 text-center">Siswa</th>
              <th class="px-4 py-3 text-center">DPT</th>
              <th class="px-4 py-3 w-64">Progress Pemilihan</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading" class="bg-white">
              <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                Memuat data...
              </td>
            </tr>
            <tr v-else-if="items.length === 0" class="bg-white">
              <td colspan="9" class="px-4 py-8 text-center text-gray-500">Tidak ada data ditemukan.</td>
            </tr>
            <tr v-else v-for="(item, index) in items" :key="item.npsn" class="bg-white border-b hover:bg-gray-50">
              <td class="px-4 py-3 text-center">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
              <td class="px-4 py-3">
                <div class="flex gap-1">
                  <BaseButton v-if="auth.user?.level === 1" @click="$router.push('/admin/data-sekolah/edit/' + item.npsn)" variant="warning" class="px-2 py-1 !min-h-0 text-xs">
                    Edit
                  </BaseButton>
                  <BaseButton @click="$router.push('/admin/data-sekolah/' + item.npsn)" variant="success" class="px-2 py-1 !min-h-0 text-xs">
                    Detail
                  </BaseButton>
                </div>
              </td>
              <td class="px-4 py-3 font-medium text-gray-900">{{ item.nama_sekolah }}</td>
              <td class="px-4 py-3">{{ item.npsn }}</td>
              <td class="px-4 py-3 text-center">{{ item.jml_kandidat }}</td>
              <td class="px-4 py-3 text-center text-xs">
                <span class="font-bold text-gray-800">{{ item.jml_tps_generate_token }}</span> / <span class="text-gray-500">{{ item.jml_tps }}</span>
              </td>
              <td class="px-4 py-3 text-center">{{ item.jml_siswa }}</td>
              <td class="px-4 py-3 text-center">
                <span :class="{'bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded font-bold': item.is_over_capacity}">
                  {{ item.jml_dpt }} ({{ item.persentase_dpt }}%)
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex justify-between text-xs mb-1">
                  <span class="font-medium text-gray-700">{{ item.jml_memilih }} Suara Masuk</span>
                  <span class="font-bold text-emerald-600">{{ item.persentase_memilih }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden flex">
                  <!-- Progress bar modern (menggantikan linear-gradient table di CI3) -->
                  <div class="bg-emerald-500 h-2.5" :style="'width: ' + item.persentase_memilih + '%'"></div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
  </div>
</template>
