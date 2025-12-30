<template>
    <div id="app" class="min-h-screen bg-gray-50">
        <router-view />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();

onMounted(async () => {
    // Check if user is authenticated on app load
    if (authStore.token) {
        await authStore.fetchUser();
    }
});
</script>
