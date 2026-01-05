<template>
    <section class="relative min-h-[600px] lg:min-h-[700px] flex items-center overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/80 via-slate-900/60 to-slate-900/40"></div>
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80" 
                 alt="City skyline" 
                 class="w-full h-full object-cover">
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-20">
            <div class="max-w-2xl">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6 animate-fade-in-up">
                    Your Dream Home is 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">Right Here</span>
                </h1>
                <p class="text-lg text-slate-300 mb-10 animate-fade-in-up animation-delay-200">
                    Discover the perfect property that matches your lifestyle. We help you find your dream home with ease and confidence.
                </p>

                <!-- Search Box -->
                <div class="bg-white rounded-2xl shadow-2xl p-4 sm:p-6 animate-fade-in-up animation-delay-400">
                    <form @submit.prevent="handleSearch" class="flex flex-col lg:flex-row gap-4">
                        <!-- Location Select -->
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Location</label>
                            <div class="relative">
                                <select v-model="searchForm.location" 
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all cursor-pointer hover:bg-slate-100">
                                    <option value="">Select Location</option>
                                    <option value="delhi">Delhi</option>
                                    <option value="gurugram">Gurugram</option>
                                    <option value="noida">Noida</option>
                                    <option value="faridabad">Faridabad</option>
                                    <option value="ghaziabad">Ghaziabad</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Property Type Select -->
                        <div class="flex-1">
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Property Type</label>
                            <div class="relative">
                                <select v-model="searchForm.propertyType" 
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all cursor-pointer hover:bg-slate-100">
                                    <option value="">Property Type</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="villa">Villa</option>
                                    <option value="bungalow">Bungalow</option>
                                    <option value="office">Office</option>
                                    <option value="shop">Shop</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="w-full lg:w-auto px-8 py-3.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Stats -->
                <div class="flex flex-wrap gap-6 mt-8 animate-fade-in-up animation-delay-600">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-slate-300 text-sm">2000+ Properties Listed</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                        <span class="text-slate-300 text-sm">500+ Happy Customers</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue';

const searchForm = ref({
    location: '',
    propertyType: ''
});

const handleSearch = () => {
    console.log('Searching:', searchForm.value);
};
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

.animation-delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
}

.animation-delay-400 {
    animation-delay: 0.4s;
    opacity: 0;
}

.animation-delay-600 {
    animation-delay: 0.6s;
    opacity: 0;
}
</style>
