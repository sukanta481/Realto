<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700 p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                    <span class="text-primary-500 font-bold text-3xl">R</span>
                </div>
                <h1 class="text-2xl font-bold text-white">Realto CRM</h1>
                <p class="text-primary-100">Real Estate Made Simple</p>
            </div>

            <!-- Login Card -->
            <div class="card">
                <div class="card-body">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Welcome Back</h2>

                    <form @submit.prevent="handleLogin" class="space-y-4">
                        <div>
                            <label class="form-label">Email</label>
                            <input 
                                v-model="form.email" 
                                type="email" 
                                class="form-input" 
                                placeholder="you@example.com"
                                required
                            />
                        </div>

                        <div>
                            <label class="form-label">Password</label>
                            <input 
                                v-model="form.password" 
                                type="password" 
                                class="form-input" 
                                placeholder="••••••••"
                                required
                            />
                        </div>

                        <div v-if="error" class="p-3 bg-danger-50 border border-danger-200 rounded-lg">
                            <p class="text-sm text-danger-600">{{ error }}</p>
                        </div>

                        <button 
                            type="submit" 
                            class="btn-primary w-full touch-target"
                            :disabled="loading"
                        >
                            <span v-if="loading">Logging in...</span>
                            <span v-else>Login</span>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Don't have an account?
                            <router-link to="/register" class="text-primary-600 font-medium hover:underline">
                                Register
                            </router-link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = reactive({
    email: '',
    password: '',
});

const loading = ref(false);
const error = ref('');

const handleLogin = async () => {
    loading.value = true;
    error.value = '';

    try {
        await authStore.login(form);
        router.push('/');
    } catch (err) {
        error.value = err.response?.data?.message || 'Login failed. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>
