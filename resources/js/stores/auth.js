import { defineStore } from 'pinia';
import { authApi } from '../api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        company: null,
        token: localStorage.getItem('auth_token') || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token && !!state.user,
        isAdmin: (state) => state.user?.role_name === 'admin',
        isManager: (state) => state.user?.role_name === 'manager',
        canManage: (state) => ['admin', 'manager'].includes(state.user?.role_name),
        userName: (state) => state.user?.name || 'User',
        companyName: (state) => state.company?.name || 'Company',
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;
            try {
                const response = await authApi.login(credentials);
                const { user, company, token } = response.data.data;

                this.user = user;
                this.company = company;
                this.token = token;
                localStorage.setItem('auth_token', token);

                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Login failed';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async register(data) {
            this.loading = true;
            this.error = null;
            try {
                const response = await authApi.register(data);
                const { user, company, token } = response.data.data;

                this.user = user;
                this.company = company;
                this.token = token;
                localStorage.setItem('auth_token', token);

                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Registration failed';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            if (!this.token) return;

            this.loading = true;
            try {
                const response = await authApi.getUser();
                this.user = response.data.data.user;
                this.company = response.data.data.company;
            } catch (error) {
                this.logout();
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await authApi.logout();
            } catch (error) {
                // Ignore errors on logout
            } finally {
                this.user = null;
                this.company = null;
                this.token = null;
                localStorage.removeItem('auth_token');
            }
        },

        hasPermission(permission) {
            if (!this.user?.permissions) return false;
            if (this.user.permissions.includes('*')) return true;
            return this.user.permissions.includes(permission);
        },
    },
});
