<template>
    <div class="min-h-screen bg-slate-50">
        <!-- Desktop Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/80 transform transition-transform duration-300 lg:translate-x-0"
            :class="{ '-translate-x-full': !sidebarOpen }"
        >
            <!-- Logo -->
            <div class="flex items-center h-20 px-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-lg" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        R
                    </div>
                    <div>
                        <span class="font-bold text-xl text-slate-900">Realto</span>
                        <p class="text-xs text-slate-400">Real Estate CRM</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1.5">
                <router-link 
                    to="/" 
                    class="sidebar-link relative"
                    :class="{ 'active': $route.name === 'dashboard' }"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </router-link>

                <router-link 
                    to="/leads" 
                    class="sidebar-link relative"
                    :class="{ 'active': $route.path.startsWith('/leads') }"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Leads</span>
                </router-link>

                <router-link 
                    to="/properties" 
                    class="sidebar-link relative"
                    :class="{ 'active': $route.path.startsWith('/properties') }"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Properties</span>
                </router-link>

                <router-link 
                    to="/deals" 
                    class="sidebar-link relative"
                    :class="{ 'active': $route.path.startsWith('/deals') }"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Deals</span>
                </router-link>

                <router-link 
                    to="/follow-ups" 
                    class="sidebar-link relative"
                    :class="{ 'active': $route.path.startsWith('/follow-ups') }"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Follow-ups</span>
                </router-link>
            </nav>

            <!-- User section -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-100">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50">
                    <div class="avatar w-10 h-10 text-sm">
                        {{ userInitials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ authStore.userName }}</p>
                        <p class="text-xs text-slate-500 truncate capitalize">{{ authStore.user?.role || 'Admin' }}</p>
                    </div>
                    <button @click="handleLogout" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-72">
            <!-- Top bar -->
            <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-slate-200/80">
                <div class="flex items-center justify-between h-16 px-4 lg:px-8">
                    <!-- Mobile menu button -->
                    <button 
                        @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Search -->
                    <div class="hidden md:flex flex-1 max-w-md mx-6">
                        <div class="relative w-full">
                            <input 
                                type="text" 
                                placeholder="Search leads, properties..." 
                                class="form-input pl-11"
                            />
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Right section -->
                    <div class="flex items-center gap-2">
                        <!-- Add button -->
                        <button class="btn-primary">
                            <svg class="w-5 h-5 lg:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="hidden lg:inline">Add Lead</span>
                        </button>

                        <!-- Notifications -->
                        <button class="p-2.5 rounded-xl text-slate-500 hover:bg-slate-100 relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4 lg:p-8">
                <router-view />
            </main>
        </div>

        <!-- Mobile bottom nav -->
        <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-slate-200/80 lg:hidden safe-area-bottom">
            <div class="flex justify-around py-2">
                <router-link to="/" class="flex flex-col items-center px-4 py-2 text-slate-400" :class="{ 'text-indigo-600': $route.name === 'dashboard' }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-xs mt-1 font-medium">Home</span>
                </router-link>
                <router-link to="/leads" class="flex flex-col items-center px-4 py-2 text-slate-400" :class="{ 'text-indigo-600': $route.path.startsWith('/leads') }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-xs mt-1 font-medium">Leads</span>
                </router-link>
                <button class="flex flex-col items-center -mt-5">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white shadow-lg" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </button>
                <router-link to="/properties" class="flex flex-col items-center px-4 py-2 text-slate-400" :class="{ 'text-indigo-600': $route.path.startsWith('/properties') }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-xs mt-1 font-medium">Properties</span>
                </router-link>
                <router-link to="/deals" class="flex flex-col items-center px-4 py-2 text-slate-400" :class="{ 'text-indigo-600': $route.path.startsWith('/deals') }">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs mt-1 font-medium">Deals</span>
                </router-link>
            </div>
        </nav>

        <!-- Mobile sidebar overlay -->
        <div 
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden"
        ></div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();
const router = useRouter();

const sidebarOpen = ref(false);

const userInitials = computed(() => {
    const name = authStore.user?.name || '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
});

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
