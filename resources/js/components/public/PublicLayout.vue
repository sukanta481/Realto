<template>
    <div class="min-h-screen flex flex-col bg-white">
        <PublicHeader />
        <main class="flex-grow pt-16 lg:pt-20">
            <slot />
        </main>
        <PublicFooter />
    </div>
</template>

<script setup>
import PublicHeader from './PublicHeader.vue';
import PublicFooter from './PublicFooter.vue';
</script>
