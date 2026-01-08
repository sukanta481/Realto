<template>
    <header :class="['fixed top-0 left-0 right-0 z-50 transition-all duration-300', isScrolled ? 'bg-white shadow-lg' : 'bg-white/95 backdrop-blur-md']">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <!-- Logo -->
                <router-link to="/" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:shadow-blue-500/50 transition-all duration-300 group-hover:scale-105">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                        Realto
                    </span>
                </router-link>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center gap-1">
                    <router-link v-for="item in navItems" :key="item.name" 
                       :to="item.href"
                       class="nav-link px-4 py-2 text-gray-600 font-medium rounded-lg hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 relative group">
                        {{ item.name }}
                        <span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-1/2 group-hover:left-1/4 transition-all duration-300"></span>
                    </router-link>
                </nav>

                <!-- Desktop Actions -->
                <div class="hidden lg:flex items-center gap-3">
                    <button @click="handleAuthClick" 
                            class="flex items-center gap-2 px-4 py-2 text-gray-600 font-medium hover:text-blue-600 transition-colors">
                        <svg v-if="isAuthenticated" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ isAuthenticated ? 'Dashboard' : 'Login' }}
                    </button>
                    <router-link to="/contact" 
                                 class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300">
                        Contact Us
                    </router-link>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="isMenuOpen = !isMenuOpen" 
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg v-if="!isMenuOpen" class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg v-else class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2">
                <div v-if="isMenuOpen" class="lg:hidden py-4 border-t border-gray-100">
                    <nav class="flex flex-col gap-1">
                        <router-link v-for="item in navItems" :key="item.name" 
                           :to="item.href"
                           class="px-4 py-3 text-gray-600 font-medium rounded-lg hover:text-blue-600 hover:bg-blue-50 transition-all">
                            {{ item.name }}
                        </router-link>
                        <div class="flex flex-col gap-2 mt-4 pt-4 border-t border-gray-100">
                            <button @click="handleAuthClick" class="px-4 py-3 text-center text-gray-600 font-medium rounded-lg hover:bg-gray-100 transition-all">
                                {{ isAuthenticated ? 'Dashboard' : 'Login' }}
                            </button>
                            <router-link to="/contact" class="px-4 py-3 text-center bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg">
                                Contact Us
                            </router-link>
                        </div>
                    </nav>
                </div>
            </transition>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const isScrolled = ref(false);
const isMenuOpen = ref(false);

const navItems = [
    { name: 'Residential', href: '/residential' },
    { name: 'Commercial', href: '/commercial' },
    { name: 'Services', href: '/services' },
    { name: 'About', href: '/about' },
];

const isAuthenticated = computed(() => !!authStore.token);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

const handleAuthClick = () => {
    if (isAuthenticated.value) {
        router.push('/app');
    } else {
        router.push('/login');
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>
