<template>
    <section class="py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Our Services</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Comprehensive solutions for all your real estate needs</p>
            </div>

            <!-- Services Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                <div v-for="service in services" :key="service.id"
                     class="group bg-slate-50 rounded-2xl p-6 text-center transition-all duration-500 hover:bg-white hover:shadow-xl hover:-translate-y-2 relative overflow-hidden">
                    <!-- Background Decoration -->
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <!-- Icon -->
                    <div class="relative w-20 h-20 mx-auto mb-6 bg-white rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-blue-600 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3">
                        <component :is="service.icon" 
                                   class="w-10 h-10 text-blue-600 group-hover:text-white transition-colors duration-500" />
                    </div>

                    <!-- Content -->
                    <h3 class="text-xl font-semibold text-slate-900 mb-3 group-hover:text-blue-600 transition-colors">{{ service.title }}</h3>
                    <p class="text-slate-600 text-sm mb-5 leading-relaxed">{{ service.description }}</p>
                    
                    <!-- Learn More Link -->
                    <a href="#" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-700 transition-colors group/link">
                        Learn More
                        <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { h } from 'vue';

// Icon components
const SearchIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'
            })
        ]);
    }
};

const BookingIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
            })
        ]);
    }
};

const LoanIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            })
        ]);
    }
};

const KeyIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'
            })
        ]);
    }
};

const services = [
    {
        id: 1,
        title: 'Property Search',
        description: 'Find the perfect property from our vast collection of residential and commercial listings.',
        icon: SearchIcon
    },
    {
        id: 2,
        title: 'Booking',
        description: 'Schedule property visits and secure your booking with our hassle-free process.',
        icon: BookingIcon
    },
    {
        id: 3,
        title: 'Home Loan',
        description: 'Get the best home loan rates from leading banks with our assistance.',
        icon: LoanIcon
    },
    {
        id: 4,
        title: 'Possession',
        description: 'Complete guidance until you receive the keys to your new home.',
        icon: KeyIcon
    },
];
</script>
