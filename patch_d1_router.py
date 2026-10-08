import re

with open('/home/akhwan/Documents/docker/pemilos-ng/resources/js/router/index.js', 'r') as f:
    content = f.read()

old_route = """    {
        path: '/admin-sekolah/cetak-pelaporan',
        name: 'admin-sekolah-cetak-pelaporan',
        component: () => import('../views/admin_sekolah/CetakPelaporan.vue'),
        meta: { requiresAuth: true, level: 2, layout: 'blank' },
    },"""

new_route = """    {
        path: '/admin-sekolah/cetak-pelaporan',
        name: 'admin-sekolah-cetak-pelaporan',
        component: () => import('../views/admin_sekolah/CetakPelaporan.vue'),
        meta: { requiresAuth: true, level: 2, layout: 'blank' },
    },
    {
        path: '/admin-sekolah/print-d1',
        name: 'admin-sekolah-print-d1',
        component: () => import('../views/admin_sekolah/PrintD1.vue'),
        meta: { requiresAuth: true, level: 2, layout: 'blank' },
    },"""

content = content.replace(old_route, new_route)

with open('/home/akhwan/Documents/docker/pemilos-ng/resources/js/router/index.js', 'w') as f:
    f.write(content)
