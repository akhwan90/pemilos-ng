import { defineStore } from 'pinia';
import api from '../services/api';
import { ref } from 'vue';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(JSON.parse(localStorage.getItem('admin_user') || 'null'));
    const token = ref(localStorage.getItem('admin_token') || '');

    async function login(username, password) {
        const res = await api.post('/admin/login', { username, password });
        const { user: userData, token: tokenData } = res.data.data;

        user.value = userData;
        token.value = tokenData;
        localStorage.setItem('admin_user', JSON.stringify(userData));
        localStorage.setItem('admin_token', tokenData);

        return res.data;
    }

    async function logout() {
        try {
            await api.post('/admin/logout');
        } catch (e) {
            // ignore
        }
        user.value = null;
        token.value = '';
        localStorage.removeItem('admin_user');
        localStorage.removeItem('admin_token');
    }

    async function fetchMe() {
        try {
            const res = await api.get('/admin/me');
            user.value = res.data.data;
            localStorage.setItem('admin_user', JSON.stringify(res.data.data));
            return true;
        } catch (e) {
            return false;
        }
    }

    function isAuthenticated() {
        return !!token.value;
    }

    return { user, token, login, logout, fetchMe, isAuthenticated };
});
