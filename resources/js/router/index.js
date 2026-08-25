import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    // ===== PUBLIC ROUTES =====
    {
        path: '/',
        name: 'landing',
        component: () => import('../views/public/LandingPage.vue'),
    },
    {
        path: '/sekolah',
        name: 'daftar-sekolah',
        component: () => import('../views/public/DaftarSekolah.vue'),
    },
    {
        path: '/arsip',
        name: 'arsip-sekolah',
        component: () => import('../views/public/ArsipSekolah.vue'),
    },
    {
        path: '/detail-sekolah',
        name: 'detail-sekolah',
        component: () => import('../views/public/DetailSekolah.vue'),
    },
    {
        path: '/bantuan',
        name: 'bantuan',
        component: () => import('../views/public/Bantuan.vue'),
    },

    // ===== ADMIN ROUTES =====
    {
        path: '/admin/login',
        name: 'admin-login',
        component: () => import('../views/admin/Login.vue'),
        meta: { layout: 'blank' },
    },
    {
        path: '/admin',
        component: () => import('../views/admin/AdminLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: '/admin/dashboard',
            },
            {
                path: 'dashboard',
                name: 'admin-dashboard',
                component: () => import('../views/admin/Dashboard.vue'),
            },
            {
                path: 'data-sekolah',
                name: 'admin-data-sekolah',
                component: () => import('../views/admin/DataSekolahList.vue'),
            },
            {
                path: 'data-siswa',
                name: 'admin-data-siswa',
                component: () => import('../views/admin/DataSiswaGlobal.vue'),
            },
            {
                path: 'setting-waktu',
                name: 'admin-setting-waktu',
                component: () => import('../views/admin/SettingWaktu.vue'),
            },
            {
                path: 'log-aktivitas',
                name: 'admin-log-aktivitas',
                component: () => import('../views/admin/LogAktivitas.vue'),
            },
            {
                path: 'log-approval',
                name: 'admin-log-approval',
                component: () => import('../views/admin/LogApproval.vue'),
            },
            {
                path: 'daftar-user',
                name: 'admin-daftar-user',
                component: () => import('../views/admin/DaftarUser.vue'),
            },
            {
                path: 'settings',
                name: 'admin-settings',
                component: () => import('../views/admin/Settings.vue'),
            },
        ],
    },
    {
        path: '/admin-sekolah',
        component: () => import('../views/admin/AdminLayout.vue'),
        meta: { requiresAuth: true, level: 2 }, // Khusus level 2 Admin Sekolah
        children: [
            {
                path: 'dashboard',
                name: 'admin-sekolah-dashboard',
                component: () => import('../views/admin_sekolah/Dashboard.vue'),
            },
            {
                path: 'approval-pindah',
                name: 'admin-sekolah-approval-pindah',
                component: () => import('../views/admin_sekolah/ApprovalPindah.vue'),
            },
            {
                path: 'identitas',
                name: 'admin-sekolah-identitas',
                component: () => import('../views/admin_sekolah/IdentitasSekolah.vue'),
            },
            {
                path: 'siswa',
                name: 'admin-sekolah-data-siswa',
                component: () => import('../views/admin_sekolah/DataSiswa.vue'),
            },
            {
                path: 'upload-siswa',
                name: 'admin-sekolah-upload-siswa',
                component: () => import('../views/admin_sekolah/UploadSiswa.vue'),
            },
            {
                path: 'tps',
                name: 'admin-sekolah-data-tps',
                component: () => import('../views/admin_sekolah/DataTps.vue'),
            },
            {
                path: 'calon',
                name: 'admin-sekolah-calon-kandidat',
                component: () => import('../views/admin_sekolah/DataKandidat.vue'),
            },
            {
                path: 'dpt',
                name: 'admin-sekolah-data-dpt',
                component: () => import('../views/admin_sekolah/DataDpt.vue'),
            },
            {
                path: 'dokumentasi',
                name: 'admin-sekolah-dokumentasi',
                component: () => import('../views/admin_sekolah/Dokumentasi.vue'),
            },
            {
                path: 'perangkat',
                name: 'admin-sekolah-perangkat',
                component: () => import('../views/admin_sekolah/Perangkat.vue'),
            },
            {
                path: 'selesai',
                name: 'admin-sekolah-selesai',
                component: () => import('../views/admin_sekolah/SelesaiPemilihan.vue'),
            },
            {
                path: 'laporan-d1',
                name: 'admin-sekolah-laporan-d1',
                component: () => import('../views/admin_sekolah/LaporanD1.vue'),
            },
        ],
    },
    {
        path: '/admin-tps',
        component: () => import('../views/admin/AdminLayout.vue'),
        meta: { requiresAuth: true, level: 3 }, // Khusus level 3 Admin TPS
        children: [
            {
                path: 'dashboard',
                name: 'admin-tps-dashboard',
                component: () => import('../views/admin_sekolah/Dashboard.vue'), // Menggunakan dashboard yang sama dengan sekolah
            },
            {
                path: 'dpt',
                name: 'admin-tps-data-dpt',
                component: () => import('../views/admin_sekolah/DataDpt.vue'), // Placeholder
            },
            {
                path: 'laporan-c1',
                name: 'admin-tps-laporan-c1',
                component: () => import('../views/admin_tps/LaporanC1.vue'),
            },
            {
                path: 'print-c1',
                name: 'admin-tps-print-c1',
                component: () => import('../views/admin_tps/PrintC1.vue'),
                meta: { layout: 'none' } // Opsional: jika layout system mendukungnya
            },
            {
                path: 'laporan-c2',
                name: 'admin-tps-laporan-c2',
                component: () => import('../views/admin_tps/LaporanC2.vue'),
            },
            {
                path: 'print-c2',
                name: 'admin-tps-print-c2',
                component: () => import('../views/admin_tps/PrintC2.vue'),
                meta: { layout: 'none' } 
            },
            {
                path: 'perangkat',
                name: 'admin-tps-perangkat',
                component: () => import('../views/admin_tps/PerangkatTps.vue'),
            },
            {
                path: 'selesai',
                name: 'admin-tps-selesai-pemilihan',
                component: () => import('../views/admin_tps/SelesaiPemilihan.vue'),
            },
        ]
    },
    {
        path: '/admin-sekolah/cetak-kartu-pemilih',
        name: 'admin-sekolah-cetak-kartu',
        component: () => import('../views/admin_tps/CetakKartuPemilih.vue'),
        meta: { requiresAuth: true, level: 2, layout: 'blank' },
    },
    {
        path: '/admin-tps/cetak-kartu-pemilih',
        name: 'admin-tps-cetak-kartu',
        component: () => import('../views/admin_tps/CetakKartuPemilih.vue'),
        meta: { requiresAuth: true, level: 3, layout: 'blank' },
    },
    {
        path: '/admin-tps/cetak-daftar-hadir',
        name: 'admin-tps-cetak-daftar-hadir',
        component: () => import('../views/admin_tps/CetakDaftarHadir.vue'),
        meta: { requiresAuth: true, level: 3, layout: 'blank' },
    },
    {
        path: '/tpssekolah',
        children: [
            {
                path: 'login',
                name: 'bilik-login',
                component: () => import('../views/bilik/LoginBilik.vue'),
            },
            {
                path: 'token',
                name: 'bilik-input-token',
                component: () => import('../views/bilik/InputToken.vue'),
            },
            {
                path: 'vote',
                name: 'bilik-kertas-suara',
                component: () => import('../views/bilik/KertasSuara.vue'),
            },
        ]
    },
    {
        path: '/tpsluarsekolah',
        name: 'tps-luar-sekolah-login',
        component: () => import('../views/bilik/LoginLuarSekolah.vue'),
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});

// Navigation guard for auth
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('admin_token');
    const user = localStorage.getItem('admin_user') ? JSON.parse(localStorage.getItem('admin_user')) : null;

    if (to.meta.requiresAuth && !token) {
        next('/admin/login');
    } else if (to.path === '/admin/login' && token) {
        if (user && parseInt(user.level) === 2) {
            next('/admin-sekolah/dashboard');
        } else if (user && parseInt(user.level) === 3) {
            next('/admin-tps/dashboard');
        } else {
            next('/admin/dashboard');
        }
    } else if (to.meta.requiresAuth && token) {
        // Cek permission level jika route mensyaratkan level tertentu (seperti meta: { level: 2 })
        if (to.meta.level && user && parseInt(user.level) !== to.meta.level) {
            // Redirect sesuai hak akses aslinya jika nyasar
            if (parseInt(user.level) === 1) next('/admin/dashboard');
            else if (parseInt(user.level) === 2) next('/admin-sekolah/dashboard');
            else if (parseInt(user.level) === 3) next('/admin-tps/dashboard');
            else next('/admin/login'); // fallback aman
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
