<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Tamu DPRD</h1>
      <div class="flex items-center gap-2">
        <BaseInput v-model="search" placeholder="Cari..." />
        <BaseSelect v-model="filterStatus" :options="statusOptions" valueKey="value" labelKey="label" placeholder="Semua Status" />
        
        <BaseButton @click="openAddModal" variant="success">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
          Tambah
        </BaseButton>
        <BaseButton @click="goToStats" variant="outline" class="mr-2">
          <svg class="w-4 h-4 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
          Statistik
        </BaseButton>
        <BaseButton @click="exportData" variant="primary">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
          Ekspor Excel
        </BaseButton>
      </div>
    </div>

    <!-- Full Width List View -->
    <div class="space-y-4">
      <BaseCard v-for="(item, i) in items" :key="item.id" class="hover:shadow-md transition-shadow">
        <div class="flex flex-col md:flex-row gap-6">
          
          <!-- Info Kunjungan & Status -->
          <div class="md:w-1/4 shrink-0 space-y-3">
            <div>
              <p class="text-xs text-gray-500 mb-1">Tanggal Kunjungan</p>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <p class="text-sm font-medium text-gray-800">{{ formatTanggal(item.tanggal_berkunjung) }}</p>
              </div>
            </div>
            
            <div>
              <p class="text-xs text-gray-500 mb-1">Jam Kunjungan</p>
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-sm font-medium text-gray-800">{{ item.jam_berkunjung }}</p>
              </div>
            </div>
            
            <div class="pt-2">
              <StatusBadge :status="item.status" :label="item.status_label" />
            </div>
          </div>

          <!-- Instansi & Tujuan -->
          <div class="md:w-2/4 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 flex-1 flex flex-col">
            <div class="mb-2">
              <p class="text-sm font-bold text-gray-800">{{ item.instansi }} <span class="font-normal text-gray-500 ml-2">{{ item.nama_alkap }}</span></p>
            </div>
            <p class="text-sm text-gray-700 whitespace-pre-wrap line-clamp-3 mb-2">{{ item.tujuan_kunjungan }}</p>
            <p class="text-xs text-gray-500">Narahubung: {{ item.nama }} ({{ item.nomor_hp_narahubung }})</p>
            
            <div class="mt-auto pt-4 flex flex-col gap-2">
              <div class="flex gap-2 flex-wrap">
                <button @click="openUploadBerkasModal(item, 'surat_kunjungan')" class="inline-flex items-center justify-center gap-2 px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-md text-xs font-medium hover:bg-indigo-100 transition-colors">
                  <svg v-if="item.file_surat_kunjungan" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  Surat Kunjungan
                </button>
                <button @click="openUploadBerkasModal(item, 'spt')" class="inline-flex items-center justify-center gap-2 px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-md text-xs font-medium hover:bg-indigo-100 transition-colors">
                  <svg v-if="item.file_spt" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  File SPT
                </button>
                <button @click="openUploadBerkasModal(item, 'bukti_menginap')" class="inline-flex items-center justify-center gap-2 px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-md text-xs font-medium hover:bg-indigo-100 transition-colors">
                  <svg v-if="item.file_bukti_menginap" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  Bukti Menginap
                </button>
              </div>
              
              <div class="flex gap-2 flex-wrap">
                <button @click="openUploadBerkasModal(item, 'daftar_hadir_ttd')" class="inline-flex items-center justify-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-md text-xs font-medium hover:bg-emerald-100 transition-colors">
                  <svg v-if="item.file_daftar_hadir_ttd" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  Daftar Hadir TTD
                </button>
                <button @click="openUploadBerkasModal(item, 'foto_kunjungan')" class="inline-flex items-center justify-center gap-2 px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-md text-xs font-medium hover:bg-emerald-100 transition-colors">
                  <svg v-if="item.file_foto_kunjungan" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                  Foto Kunjungan
                </button>
              </div>
            </div>
          </div>

          <!-- Aksi -->
          <div class="md:w-1/4 shrink-0 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6 flex flex-col gap-2 justify-center">
            <button @click="openDetailModal(item)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
              Detail
            </button>
            
            <button @click="openEditModal(item)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
              Edit
            </button>
            
            <button @click="deleteItem(item.id)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
              Hapus
            </button>
            
            <button @click="openGenerateModal(item)" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              Generate
            </button>
          </div>
          
        </div>
      </BaseCard>

      <!-- Empty State -->
      <BaseCard v-if="!items.length" class="text-center py-12 border-dashed">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
        <p class="text-gray-500">Belum ada data.</p>
      </BaseCard>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="mt-6 flex items-center justify-between">
      <p class="text-sm text-gray-500">Menampilkan {{ meta.from }} - {{ meta.to }} dari {{ meta.total }}</p>
      <div class="flex gap-2">
        <BaseButton @click="loadPage(currentPage - 1)" :disabled="!meta.prev_page_url" variant="outline" class="!px-3 !py-1 text-sm">Prev</BaseButton>
        <BaseButton @click="loadPage(currentPage + 1)" :disabled="!meta.next_page_url" variant="outline" class="!px-3 !py-1 text-sm">Next</BaseButton>
      </div>
    </div>
    
    <!-- Edit / Tambah Form Modal -->
    <BaseModal v-model="showFormModal" :title="isEdit ? 'Edit Tamu DPRD' : 'Tambah Tamu DPRD'" maxWidth="4xl">
      <form @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <BaseInput v-model="form.nama" label="Nama Lengkap Narahubung" required />
          <BaseInput v-model="form.nomor_hp_narahubung" label="Nomor HP Narahubung" required />
          <BaseInput v-model="form.email" type="email" label="Email Instansi/Narahubung" required />
          <BaseInput v-model="form.instansi" label="Asal Instansi/Daerah" required />
          <BaseInput v-model="form.alamat_instansi" label="Alamat Instansi" class="md:col-span-2" required />
          
          <BaseSelect v-model="form.nama_alkap" :options="alkapOptions" valueKey="value" labelKey="label" label="Tujuan Alat Kelengkapan DPRD" required />
          <BaseInput v-model="form.jumlah_peserta" type="number" label="Jumlah Peserta" required />
          <BaseInput v-model="form.nama_jabatan_ketua_rombongan" label="Nama & Jabatan Pimpinan Rombongan" required />
          <div class="grid grid-cols-2 gap-4">
            <BaseInput v-model="form.tanggal_berkunjung" type="date" label="Tanggal Kunjungan" required />
            <BaseSelect v-model="form.jam_berkunjung" :options="jamOptions" valueKey="value" labelKey="label" label="Waktu" required />
          </div>
          <BaseSelect v-model="form.hari_berkunjung" :options="hariOptions" valueKey="value" labelKey="label" label="Hari Kunjungan" required />
          
          <BaseInput v-model="form.nomor_surat_ppid" label="Nomor Surat PPID" class="md:col-span-2" />

          <BaseTextarea v-model="form.tujuan_kunjungan" label="Maksud dan Tujuan Kunjungan" class="md:col-span-2" required rows="3" />
          
          <BaseTextarea v-model="form.materi" label="Materi Kunjungan" class="md:col-span-2" rows="3" />
          
          <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <BaseFile @change="file => { form.file_surat_kunjungan = file }" label="Surat Kunjungan (PDF)" accept=".pdf" :required="!isEdit" />
              <p v-if="isEdit && form.file_surat_kunjungan_existing" class="text-xs mt-1 text-indigo-600"><a :href="form.file_surat_kunjungan_existing" target="_blank" class="underline">File lama</a></p>
            </div>
            <div>
              <BaseFile @change="file => { form.file_spt = file }" label="Surat Perintah Tugas (PDF)" accept=".pdf" :required="!isEdit" />
              <p v-if="isEdit && form.file_spt_existing" class="text-xs mt-1 text-indigo-600"><a :href="form.file_spt_existing" target="_blank" class="underline">File lama</a></p>
            </div>
            <div>
              <BaseFile @change="file => { form.file_bukti_menginap = file }" label="Bukti Menginap (Opsional)" accept=".pdf,.jpg,.png" />
              <p v-if="isEdit && form.file_bukti_menginap_existing" class="text-xs mt-1 text-indigo-600"><a :href="form.file_bukti_menginap_existing" target="_blank" class="underline">File lama</a></p>
            </div>
          </div>
        </div>
      </form>
      <template #footer>
        <BaseButton @click="showFormModal = false" variant="outline">Batal</BaseButton>
        <BaseButton @click="submitForm" variant="primary" :disabled="formLoading">{{ formLoading ? 'Menyimpan...' : 'Simpan' }}</BaseButton>
      </template>
    </BaseModal>

    <!-- Detail Modal -->
    <BaseModal v-model="showDetailModal" title="Detail Kunjungan Tamu DPRD" maxWidth="4xl">
      <div v-if="selectedItem" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informasi Kunjungan -->
        <div class="space-y-4">
          <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Informasi Kunjungan</h3>
          <dl class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><dt class="text-xs text-gray-500">Instansi</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.instansi }}</dd></div>
            <div class="col-span-2"><dt class="text-xs text-gray-500">Tujuan Kunjungan (Alkap)</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.nama_alkap }}</dd></div>
            <div class="col-span-2"><dt class="text-xs text-gray-500">Alamat Instansi</dt><dd class="text-sm text-gray-800">{{ selectedItem.alamat_instansi }}</dd></div>
            <div><dt class="text-xs text-gray-500">Waktu Kunjungan</dt><dd class="text-sm text-gray-800">{{ formatTanggal(selectedItem.tanggal_berkunjung) }} Pukul {{ selectedItem.jam_berkunjung }}</dd></div>
            <div><dt class="text-xs text-gray-500">Peserta</dt><dd class="text-sm text-gray-800">{{ selectedItem.jumlah_peserta }} Orang</dd></div>
            <div class="col-span-2"><dt class="text-xs text-gray-500">Pimpinan Rombongan</dt><dd class="text-sm text-gray-800">{{ selectedItem.nama_jabatan_ketua_rombongan }}</dd></div>
          </dl>
        </div>
        
        <div class="space-y-4">
          <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Informasi Narahubung</h3>
          <dl class="grid grid-cols-1 gap-4">
            <div><dt class="text-xs text-gray-500">Nama Lengkap</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.nama }}</dd></div>
            <div><dt class="text-xs text-gray-500">Nomor HP</dt><dd class="text-sm font-medium text-gray-800">{{ selectedItem.nomor_hp_narahubung }}</dd></div>
            <div><dt class="text-xs text-gray-500">Email</dt><dd class="text-sm text-gray-800">{{ selectedItem.email }}</dd></div>
          </dl>
          
          <div class="pt-2">
            <a :href="whatsappUrl" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606 .134-.133.298-.347.446-.52 .149-.174 .198-.298 .298-.497 .099-.198 .05-.371-.025-.52 -.075-.149-.669-1.612-.916-2.207 -.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01 -.198 0-.52.074-.792.372 -.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074 .149.198 2.096 3.2 5.077 4.487 .709.306 1.262.489 1.694.625 .712.227 1.36.195 1.871.118 .571-.085 1.758-.719 2.006-1.413 .248-.694.248-1.289.173-1.413 -.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982 .998-3.648 -.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Hubungi via WhatsApp
            </a>
          </div>
        </div>
        
        <div class="col-span-1 md:col-span-2 space-y-4">
          <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Maksud dan Tujuan</h3>
          <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ selectedItem.tujuan_kunjungan }}</p>
          
          <div class="pt-2 border-t mt-4 flex gap-4">
            <a v-if="selectedItem.file_surat_kunjungan" :href="selectedItem.file_surat_kunjungan" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              Surat Kunjungan
            </a>
            <a v-if="selectedItem.file_spt" :href="selectedItem.file_spt" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-medium hover:bg-indigo-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              File SPT
            </a>
            <a v-if="selectedItem.file_bukti_menginap" :href="selectedItem.file_bukti_menginap" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 rounded-lg text-sm font-medium hover:bg-green-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
              Bukti Menginap
            </a>
          </div>
          <div class="pt-2 border-t mt-2 flex gap-4">
            <a v-if="selectedItem.file_daftar_hadir_ttd" :href="selectedItem.file_daftar_hadir_ttd" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              Daftar Hadir TTD
            </a>
            <a v-if="selectedItem.file_foto_kunjungan" :href="selectedItem.file_foto_kunjungan" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium hover:bg-emerald-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
              Foto Kunjungan
            </a>
          </div>
        </div>
      </div>
      
      <template #footer>
        <BaseButton @click="showDetailModal = false" variant="outline">Tutup</BaseButton>
      </template>
    </BaseModal>
    
    <!-- Edit / Tambah Form Modal -->
    <BaseModal v-model="showGenerateModal" title="Generate Dokumen" maxWidth="xl">
      <div v-if="selectedItem" class="space-y-4">
        <p class="text-sm text-gray-600 mb-4">Pilih jenis dokumen yang ingin di-generate untuk kunjungan <strong>{{ selectedItem.instansi }}</strong>.</p>
        
        <!-- Document List -->
        <div class="space-y-3">
          <!-- Dokumen: Daftar Hadir & Pernyataan -->
          <div class="p-3 border rounded-lg" :class="generateDocType === 'daftar_hadir' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300'" @click="generateDocType = 'daftar_hadir'">
            <div class="flex items-start justify-between cursor-pointer">
              <div class="flex items-center gap-3">
                <input type="radio" v-model="generateDocType" value="daftar_hadir" class="text-indigo-600 focus:ring-indigo-500" />
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Daftar Hadir dan Pernyataan</h4>
                  <p class="text-xs text-gray-500 mt-1">Format standard daftar hadir dan lampiran pernyataan.</p>
                </div>
              </div>
            </div>
            
            <div v-if="selectedItem.file_daftar_hadir" class="mt-3 ml-7 p-2 bg-green-50 rounded text-sm text-green-700 flex justify-between items-center border border-green-100">
              <span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Tersedia</span>
              <a :href="selectedItem.file_daftar_hadir" target="_blank" class="px-2 py-1 bg-white border border-green-200 rounded text-xs hover:bg-green-100" @click.stop>Lihat/Download</a>
            </div>
            <div v-else class="mt-3 ml-7 text-xs text-gray-400 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Belum ada file (Silakan klik Generate)
            </div>
          </div>

          <!-- Dokumen: PPID -->
          <div class="p-3 border rounded-lg" :class="generateDocType === 'ppid' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-indigo-300'" @click="generateDocType = 'ppid'">
            <div class="flex items-start justify-between cursor-pointer">
              <div class="flex items-center gap-3">
                <input type="radio" v-model="generateDocType" value="ppid" class="text-indigo-600 focus:ring-indigo-500" />
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Dokumen PPID</h4>
                  <p class="text-xs text-gray-500 mt-1">Dokumen pendukung permohonan informasi publik (PPID).</p>
                </div>
              </div>
            </div>
            
            <div v-if="selectedItem.file_dokumen_ppid" class="mt-3 ml-7 p-2 bg-green-50 rounded text-sm text-green-700 flex justify-between items-center border border-green-100">
              <span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Tersedia</span>
              <a :href="selectedItem.file_dokumen_ppid" target="_blank" class="px-2 py-1 bg-white border border-green-200 rounded text-xs hover:bg-green-100" @click.stop>Lihat/Download</a>
            </div>
            <div v-else class="mt-3 ml-7 text-xs text-gray-400 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Belum ada file (Silakan klik Generate)
            </div>
          </div>
        </div>
        
        <div v-if="generatedUrl" class="mt-4 p-4 bg-green-50 rounded-lg flex items-center justify-between border border-green-100">
          <div class="flex items-center gap-2 text-green-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-medium">Dokumen baru berhasil di-generate!</span>
          </div>
          <a :href="generatedUrl" target="_blank" class="px-3 py-1.5 bg-white text-green-700 text-xs font-bold rounded shadow-sm hover:bg-green-50">Buka Hasil</a>
        </div>
      </div>
      
      <template #footer>
        <BaseButton @click="showGenerateModal = false" variant="outline">Tutup</BaseButton>
        <BaseButton @click="generateDocument" variant="primary" :disabled="generateLoading || !generateDocType">
          <span class="flex items-center gap-2">
            <svg v-if="generateLoading" class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            {{ generateLoading ? 'Memproses...' : (generateDocType === 'daftar_hadir' && selectedItem?.file_daftar_hadir || generateDocType === 'ppid' && selectedItem?.file_dokumen_ppid ? 'Generate Ulang' : 'Generate File') }}
          </span>
        </BaseButton>
      </template>
    </BaseModal>

    <!-- Upload Berkas Akhir Modal -->
    <BaseModal v-model="showUploadModal" :title="getUploadModalTitle(uploadDocType)" maxWidth="md">
      <div v-if="selectedItem" class="space-y-4">
        <div v-if="getDocUrl(uploadDocType)" class="p-4 bg-emerald-50 rounded-lg flex flex-col sm:flex-row sm:items-center justify-between border border-emerald-100 gap-3">
          <div class="flex items-center gap-2 text-emerald-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-medium">Berkas sudah tersedia.</span>
          </div>
          <div class="flex gap-2">
            <a :href="getDocUrl(uploadDocType)" target="_blank" class="px-3 py-1.5 bg-white text-emerald-700 text-xs font-bold rounded shadow-sm hover:bg-emerald-50">Buka</a>
            <button @click="hapusBerkas" class="px-3 py-1.5 bg-white text-red-600 text-xs font-bold rounded shadow-sm hover:bg-red-50" :disabled="hapusLoading">
              {{ hapusLoading ? 'Menghapus...' : 'Hapus' }}
            </button>
          </div>
        </div>
        
        <p class="text-sm text-gray-600">Pilih file untuk diunggah (Maksimal 5MB, format: PDF/JPG/PNG). <span v-if="getDocUrl(uploadDocType)">Mengunggah file baru akan menimpa file yang sudah ada.</span></p>
        
        <BaseFile @change="file => { uploadFile = file }" :label="getUploadModalTitle(uploadDocType)" accept=".pdf,.jpg,.jpeg,.png" required />
        
        <div v-if="uploadSuccessMessage" class="mt-2 text-xs text-green-600 font-medium">
          {{ uploadSuccessMessage }}
        </div>
      </div>
      
      <template #footer>
        <BaseButton @click="showUploadModal = false" variant="outline">Tutup</BaseButton>
        <BaseButton @click="submitUpload" variant="primary" :disabled="uploadLoading || !uploadFile">
          <span class="flex items-center gap-2">
            <svg v-if="uploadLoading" class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
            {{ uploadLoading ? 'Mengunggah...' : 'Upload Berkas' }}
          </span>
        </BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../services/api';
import StatusBadge from '../../components/StatusBadge.vue';
import BaseInput from '../../components/BaseInput.vue';
import BaseSelect from '../../components/BaseSelect.vue';
import BaseButton from '../../components/BaseButton.vue';
import BaseCard from '../../components/BaseCard.vue';
import BaseModal from '../../components/BaseModal.vue';
import BaseTextarea from '../../components/BaseTextarea.vue';
import BaseFile from '../../components/BaseFile.vue';

const router = useRouter();

// Helper function untuk format tanggal ke format Indonesia
function formatTanggal(tanggal) {
  if (!tanggal) return '-';
  const date = new Date(tanggal);
  return date.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
}

const items = ref([]);
const meta = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0, prev_page_url: null, next_page_url: null });
const search = ref('');
const filterStatus = ref('');
const currentPage = ref(1);

const showDetailModal = ref(false);
const showProgressModal = ref(false);
const showFormModal = ref(false);
const showGenerateModal = ref(false);
const showUploadModal = ref(false);
const formLoading = ref(false);
const generateLoading = ref(false);
const uploadLoading = ref(false);
const hapusLoading = ref(false);
const isEdit = ref(false);

const selectedItem = ref(null);
const generateDocType = ref('');
const generatedUrl = ref(null);
const uploadDocType = ref('');
const uploadFile = ref(null);
const uploadSuccessMessage = ref('');

const form = ref({
  nama: '', instansi: '', hari_berkunjung: '', jam_berkunjung: '',
  tanggal_berkunjung: '', nama_alkap: '', jumlah_peserta: '',
  nama_jabatan_ketua_rombongan: '', nomor_hp_narahubung: '',
  email: '', alamat_instansi: '', tujuan_kunjungan: '',
  file_surat_kunjungan: null, file_spt: null, file_bukti_menginap: null,
  file_surat_kunjungan_existing: null, file_spt_existing: null, file_bukti_menginap_existing: null
});

const statusOptions = [
  { value: 'baru', label: 'Baru' },
  { value: 'diproses', label: 'Diproses' },
  { value: 'disetujui', label: 'Disetujui' },
  { value: 'ditolak', label: 'Ditolak' },
  { value: 'selesai', label: 'Selesai' }
];

const hariOptions = [
  { value: 'Senin', label: 'Senin' },
  { value: 'Selasa', label: 'Selasa' },
  { value: 'Rabu', label: 'Rabu' },
  { value: 'Kamis', label: 'Kamis' },
  { value: 'Jumat', label: 'Jumat' },
];

const jamOptions = [
  { value: '08:00', label: '08.00' },
  { value: '08:30', label: '08.30' },
  { value: '09:00', label: '09.00' },
  { value: '09:30', label: '09.30' },
  { value: '10:00', label: '10.00' },
  { value: '10:30', label: '10.30' },
  { value: '11:00', label: '11.00' },
  { value: '11:30', label: '11.30' },
  { value: '12:00', label: '12.00' },
  { value: '12:30', label: '12.30' },
  { value: '13:00', label: '13.00' },
  { value: '13:30', label: '13.30' },
  { value: '14:00', label: '14.00' },
];

const alkapOptions = [
  { value: 'Pimpinan DPRD', label: 'Pimpinan DPRD' },
  { value: 'Komisi I', label: 'Komisi I' },
  { value: 'Komisi II', label: 'Komisi II' },
  { value: 'Komisi III', label: 'Komisi III' },
  { value: 'Komisi IV', label: 'Komisi IV' },
  { value: 'Bamus', label: 'Bamus' },
  { value: 'Banggar', label: 'Banggar' },
  { value: 'Bapemperda', label: 'Bapemperda' },
  { value: 'BK', label: 'BK' },
  { value: 'Pansus', label: 'Pansus' },
  { value: 'Fraksi', label: 'Fraksi' },
];

const whatsappUrl = computed(() => {
  if (!selectedItem.value) return '#';
  const phone = selectedItem.value.nomor_hp_narahubung?.replace(/[^0-9]/g, '');
  const msg = encodeURIComponent(
    `Yth. ${selectedItem.value.nama} (${selectedItem.value.instansi}),\n\nKami dari Sekretariat DPRD Kab. Kulon Progo menindaklanjuti rencana kunjungan Anda ke ${selectedItem.value.nama_alkap} pada ${selectedItem.value.tanggal_berkunjung}.\n\nMohon kesediaannya untuk dihubungi lebih lanjut.\n\nTerima kasih.`
  );
  const prefix = phone?.startsWith('62') ? '' : '62';
  return `https://wa.me/${prefix}${phone}?text=${msg}`;
});

function getDocUrl(type) {
  if (!selectedItem.value) return null;
  return selectedItem.value[`file_${type}`] || null;
}

function getUploadModalTitle(type) {
  switch (type) {
    case 'surat_kunjungan': return 'Surat Kunjungan (PDF/JPG)';
    case 'spt': return 'Surat Perintah Tugas (PDF/JPG)';
    case 'bukti_menginap': return 'Bukti Menginap (PDF/JPG/PNG)';
    case 'daftar_hadir_ttd': return 'Daftar Hadir Tertanda Tangan (PDF/JPG)';
    case 'foto_kunjungan': return 'Foto Kunjungan (JPG/PNG)';
    default: return 'Upload Berkas';
  }
}

function openDetailModal(item) {
  selectedItem.value = item;
  showDetailModal.value = true;
}

function openGenerateModal(item) {
  selectedItem.value = item;
  generateDocType.value = '';
  generatedUrl.value = null;
  showGenerateModal.value = true;
}

function openUploadBerkasModal(item, type) {
  selectedItem.value = item;
  uploadDocType.value = type;
  uploadFile.value = null;
  uploadSuccessMessage.value = '';
  showUploadModal.value = true;
}

function openAddModal() {
  isEdit.value = false;
  selectedItem.value = null;
  form.value = {
    nama: '', instansi: '', hari_berkunjung: '', jam_berkunjung: '',
    tanggal_berkunjung: '', nama_alkap: '', jumlah_peserta: '',
    nama_jabatan_ketua_rombongan: '', nomor_hp_narahubung: '',
    email: '', alamat_instansi: '', tujuan_kunjungan: '',
    nomor_surat_ppid: '', materi: '',
    file_surat_kunjungan: null, file_spt: null, file_bukti_menginap: null,
    file_surat_kunjungan_existing: null, file_spt_existing: null, file_bukti_menginap_existing: null
  };
  showFormModal.value = true;
}

function openEditModal(item) {
  isEdit.value = true;
  selectedItem.value = item;
  form.value = {
    nama: item.nama,
    instansi: item.instansi,
    hari_berkunjung: item.hari_berkunjung,
    jam_berkunjung: item.jam_berkunjung.substring(0, 5), // potong detik jika ada
    tanggal_berkunjung: item.tanggal_berkunjung,
    nama_alkap: item.nama_alkap,
    jumlah_peserta: item.jumlah_peserta,
    nama_jabatan_ketua_rombongan: item.nama_jabatan_ketua_rombongan,
    nomor_hp_narahubung: item.nomor_hp_narahubung,
    email: item.email,
    alamat_instansi: item.alamat_instansi,
    tujuan_kunjungan: item.tujuan_kunjungan,
    nomor_surat_ppid: item.nomor_surat_ppid || '',
    materi: item.materi || '',
    file_surat_kunjungan: null,
    file_spt: null,
    file_bukti_menginap: null,
    file_surat_kunjungan_existing: item.file_surat_kunjungan,
    file_spt_existing: item.file_spt,
    file_bukti_menginap_existing: item.file_bukti_menginap
  };
  showFormModal.value = true;
}

async function submitForm() {
  formLoading.value = true;
  try {
    const formData = new FormData();
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key]);
      }
    });

    if (isEdit.value) {
      formData.append('_method', 'PUT');
      await api.post(`/admin/tamu-dprd/${selectedItem.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    } else {
      await api.post('/admin/tamu-dprd', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
    }
    showFormModal.value = false;
    loadData();
  } catch (e) {
    console.error(e);
    alert('Terjadi kesalahan saat menyimpan data.');
  } finally {
    formLoading.value = false;
  }
}

async function deleteItem(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;
  try {
    await api.delete(`/admin/tamu-dprd/${id}`);
    loadData();
  } catch (e) {
    console.error(e);
    alert('Gagal menghapus data.');
  }
}

async function generateDocument() {
  if (!selectedItem.value || !generateDocType.value) return;
  generateLoading.value = true;
  generatedUrl.value = null;
  
  try {
    const res = await api.post(`/admin/tamu-dprd/${selectedItem.value.id}/generate`, {
      jenis_dokumen: generateDocType.value
    });
    
    if (res.data.success && res.data.url) {
      generatedUrl.value = res.data.url;
      
      // Update selectedItem based on updated backend response data
      if (res.data.data) {
        selectedItem.value = { ...selectedItem.value, ...res.data.data };
      } else {
        // Fallback jika tidak ada data dari backend
        if (generateDocType.value === 'daftar_hadir') {
          selectedItem.value.file_daftar_hadir = res.data.url;
        } else if (generateDocType.value === 'ppid') {
          selectedItem.value.file_dokumen_ppid = res.data.url;
        }
      }
      
      // Update the main array
      const idx = items.value.findIndex(i => i.id === selectedItem.value.id);
      if (idx !== -1) {
        items.value[idx] = { ...selectedItem.value };
      }
      
      // Buka pdf di tab baru
      // Karena ini Vue Router dengan history mode, kadang URL tanpa domain dianggap route
      // Pastikan URL-nya adalah absolute (http...)
      const absoluteUrl = res.data.url.startsWith('http') 
          ? res.data.url 
          : window.location.origin + res.data.url;
      window.open(absoluteUrl, '_blank');
    }
  } catch (e) {
    console.error(e);
    alert('Terjadi kesalahan saat men-generate dokumen.');
  } finally {
    generateLoading.value = false;
  }
}

async function submitUpload() {
  if (!selectedItem.value || !uploadFile.value) return;
  uploadLoading.value = true;
  uploadSuccessMessage.value = '';
  
  try {
    const formData = new FormData();
    formData.append('jenis_dokumen', uploadDocType.value);
    formData.append('file', uploadFile.value);

    const res = await api.post(`/admin/tamu-dprd/${selectedItem.value.id}/upload-berkas`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    if (res.data.success) {
      uploadSuccessMessage.value = res.data.message;
      
      // Sinkronkan data hasil respons terbaru dengan item terpilih dan list utama
      selectedItem.value = res.data.data;
      const idx = items.value.findIndex(i => i.id === selectedItem.value.id);
      if (idx !== -1) {
        items.value[idx] = res.data.data;
      }
      
      // Hapus file yang ada di memori temp input
      uploadFile.value = null;
    }
  } catch (e) {
    console.error(e);
    alert(e.response?.data?.message || 'Terjadi kesalahan saat mengunggah berkas.');
  } finally {
    uploadLoading.value = false;
  }
}

async function hapusBerkas() {
  if (!confirm('Apakah Anda yakin ingin menghapus berkas ini?')) return;
  hapusLoading.value = true;
  uploadSuccessMessage.value = '';

  try {
    const res = await api.delete(`/admin/tamu-dprd/${selectedItem.value.id}/hapus-berkas`, {
      data: { jenis_dokumen: uploadDocType.value }
    });

    if (res.data.success) {
      selectedItem.value = res.data.data;
      const idx = items.value.findIndex(i => i.id === selectedItem.value.id);
      if (idx !== -1) {
        items.value[idx] = res.data.data;
      }
      uploadSuccessMessage.value = 'Berkas berhasil dihapus.';
      uploadFile.value = null;
    }
  } catch (e) {
    console.error(e);
    alert('Terjadi kesalahan saat menghapus berkas.');
  } finally {
    hapusLoading.value = false;
  }
}

async function loadData() {
  try {
    const params = { page: currentPage.value, per_page: 15 };
    if (search.value) params.search = search.value;
    if (filterStatus.value) params.status = filterStatus.value;
    const res = await api.get('/admin/tamu-dprd', { params });
    items.value = res.data.data;
    meta.value = res.data.meta;
  } catch (e) { console.error(e); }
}

watch([search, filterStatus], () => { currentPage.value = 1; loadData(); });

function loadPage(page) {
  if (page < 1 || page > meta.value.last_page) return;
  currentPage.value = page;
  loadData();
}

function goToStats() {
  router.push('/admin/tamu-dprd/stats');
}

async function exportData() {
  try {
    const params = {};
    if (filterStatus.value) params.status = filterStatus.value;
    const res = await api.get('/admin/tamu-dprd/export', { params });
    const blob = new Blob([JSON.stringify(res.data.data, null, 2)], { type: 'application/json' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `tamu-dprd-export.json`;
    a.click();
    URL.revokeObjectURL(url);
  } catch (e) { console.error(e); }
}

onMounted(loadData);
</script>
