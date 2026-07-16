import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    // ===== PUBLIC ROUTES =====
    {
        path: '/',
        name: 'landing',
        component: () => import('../views/public/LandingPage.vue'),
    },
    {
        path: '/aduan-aspirasi',
        name: 'aduan-aspirasi',
        component: () => import('../views/public/AduanAspirasiForm.vue'),
    },
    {
        path: '/tamu-setwan',
        name: 'tamu-setwan',
        component: () => import('../views/public/TamuSetwanForm.vue'),
    },
    {
        path: '/tamu-dprd',
        name: 'tamu-dprd',
        component: () => import('../views/public/TamuDprdForm.vue'),
    },
    {
        path: '/permohonan-audiensi',
        name: 'permohonan-audiensi',
        component: () => import('../views/public/PermohonanAudiensiForm.vue'),
    },
    {
        path: '/sukses',
        name: 'success',
        component: () => import('../views/public/SuccessPage.vue'),
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
                component: () => import('../views/admin_sekolah/Placeholder.vue'),
            }
        ],
    },
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

    if (to.meta.requiresAuth && !token) {
        next('/admin/login');
    } else if (to.path === '/admin/login' && token) {
        next('/admin/dashboard');
    } else {
        next();
    }
});

export default router;
