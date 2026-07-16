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
                path: 'aduan-aspirasi',
                name: 'admin-aduan-aspirasi',
                component: () => import('../views/admin/AduanAspirasiList.vue'),
            },
            {
                path: 'aduan-aspirasi/:id',
                name: 'admin-aduan-aspirasi-detail',
                component: () => import('../views/admin/AduanAspirasiDetail.vue'),
            },
            {
                path: 'tamu-setwan',
                name: 'admin-tamu-setwan',
                component: () => import('../views/admin/TamuSetwanList.vue'),
            },
            {
                path: 'tamu-setwan/stats',
                name: 'admin-tamu-setwan-stats',
                component: () => import('../views/admin/TamuSetwanStats.vue'),
            },
            {
                path: 'tamu-setwan/:id',
                name: 'admin-tamu-setwan-detail',
                component: () => import('../views/admin/TamuSetwanDetail.vue'),
            },
            {
                path: 'tamu-dprd',
                name: 'admin-tamu-dprd',
                component: () => import('../views/admin/TamuDprdList.vue'),
            },
            {
                path: 'tamu-dprd/stats',
                name: 'admin-tamu-dprd-stats',
                component: () => import('../views/admin/TamuDprdStats.vue'),
            },
            {
                path: 'tamu-dprd/:id',
                name: 'admin-tamu-dprd-detail',
                component: () => import('../views/admin/TamuDprdDetail.vue'),
            },
            {
                path: 'permohonan-audiensi',
                name: 'admin-permohonan-audiensi',
                component: () => import('../views/admin/PermohonanAudiensiList.vue'),
            },
            {
                path: 'permohonan-audiensi/stats',
                name: 'admin-permohonan-audiensi-stats',
                component: () => import('../views/admin/PermohonanAudiensiStats.vue'),
            },
            {
                path: 'permohonan-audiensi/:id',
                name: 'admin-permohonan-audiensi-detail',
                component: () => import('../views/admin/PermohonanAudiensiDetail.vue'),
            },
            {
                path: 'settings',
                name: 'admin-settings',
                component: () => import('../views/admin/Settings.vue'),
            },
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
