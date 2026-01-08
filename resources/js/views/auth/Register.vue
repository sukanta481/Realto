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
                            <div class="relative">
                                <input 
                                    v-model="form.password" 
                                    :type="showPassword ? 'text' : 'password'" 
                                    class="form-input pr-12" 
                                    placeholder="••••••••"
                                    required
                                />
                                <button 
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                >
                                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Confirm Password</label>
                            <div class="relative">
                                <input 
                                    v-model="form.password_confirmation" 
                                    :type="showConfirmPassword ? 'text' : 'password'" 
                                    class="form-input pr-12" 
                                    placeholder="••••••••"
                                    required
                                />
                                <button 
                                    type="button"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                                >
                                    <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="error" class="p-3 bg-danger-50 border border-danger-200 rounded-lg">
                            <p class="text-sm text-danger-600 font-medium">{{ error }}</p>
                            <ul v-if="validationErrors.length" class="mt-2 text-sm text-danger-600 list-disc list-inside">
                                <li v-for="(err, idx) in validationErrors" :key="idx">{{ err }}</li>
                            </ul>
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
const validationErrors = ref([]);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const handleRegister = async () => {
    if (form.password !== form.password_confirmation) {
        error.value = 'Passwords do not match';
        validationErrors.value = [];
        return;
    }

    loading.value = true;
    error.value = '';
    validationErrors.value = [];

    try {
        await authStore.register(form);
        router.push('/app');
    } catch (err) {
        const response = err.response?.data;
        
        // Handle validation errors
        if (response?.errors) {
            error.value = 'Please fix the following errors:';
            validationErrors.value = Object.values(response.errors).flat();
        } else {
            error.value = response?.message || 'Registration failed. Please try again.';
            validationErrors.value = [];
        }
    } finally {
        loading.value = false;
    }
};
</script>
