<template>
    <section class="relative min-h-[350px] lg:min-h-[400px] flex items-center overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <div :class="['absolute inset-0', overlayClass]"></div>
            <img :src="backgroundImage" 
                 :alt="title" 
                 class="w-full h-full object-cover">
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-16 lg:py-20">
            <div class="max-w-3xl">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm text-slate-300 mb-6 animate-fade-in-up">
                    <router-link to="/" class="hover:text-white transition-colors">Home</router-link>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-white">{{ title }}</span>
                </nav>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4 animate-fade-in-up animation-delay-100">
                    {{ title }}
                </h1>
                <p v-if="subtitle" class="text-lg lg:text-xl text-slate-300 animate-fade-in-up animation-delay-200">
                    {{ subtitle }}
                </p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
    </section>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    backgroundImage: {
        type: String,
        default: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'
    },
    overlayClass: {
        type: String,
        default: 'bg-gradient-to-r from-slate-900/85 via-slate-900/70 to-slate-900/50'
    }
});
</script>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
}

.animation-delay-100 {
    animation-delay: 0.1s;
    opacity: 0;
}

.animation-delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
}
</style>
