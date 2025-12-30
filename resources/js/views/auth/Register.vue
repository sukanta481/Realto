<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-500 to-primary-700 p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                    <span class="text-primary-500 font-bold text-3xl">R</span>
                </div>
                <h1 class="text-2xl font-bold text-white">Realto CRM</h1>
                <p class="text-primary-100">Start Your Journey</p>
            </div>

            <!-- Register Card -->
            <div class="card">
                <div class="card-body">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Create Account</h2>

                    <form @submit.prevent="handleRegister" class="space-y-4">
                        <div>
                            <label class="form-label">Company Name</label>
                            <input 
                                v-model="form.company_name" 
                                type="text" 
                                class="form-input" 
                                placeholder="Your Company"
                                required
                            />
                        </div>

                        <div>
                            <label class="form-label">Your Name</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                class="form-input" 
                                placeholder="John Doe"
                                required
                            />
                        </div>

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
                            <label class="form-label">Phone</label>
                            <input 
                                v-model="form.phone" 
                                type="tel" 
                                class="form-input" 
                                placeholder="9876543210"
                                required
                            />
                        </div>

                        <div>
                            <label class="form-label">City</label>
                            <input 
                                v-model="form.city" 
                                type="text" 
                                class="form-input" 
                                placeholder="Mumbai"
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

                        <div>
                            <label class="form-label">Confirm Password</label>
                            <input 
                                v-model="form.password_confirmation" 
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
                            <span v-if="loading">Creating account...</span>
                            <span v-else>Create Account</span>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <router-link to="/login" class="text-primary-600 font-medium hover:underline">
                                Login
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
    company_name: '',
    name: '',
    email: '',
    phone: '',
    city: '',
    password: '',
    password_confirmation: '',
});

const loading = ref(false);
const error = ref('');

const handleRegister = async () => {
    if (form.password !== form.password_confirmation) {
        error.value = 'Passwords do not match';
        return;
    }

    loading.value = true;
    error.value = '';

    try {
        await authStore.register(form);
        router.push('/');
    } catch (err) {
        error.value = err.response?.data?.message || 'Registration failed. Please try again.';
    } finally {
        loading.value = false;
    }
};
</script>
