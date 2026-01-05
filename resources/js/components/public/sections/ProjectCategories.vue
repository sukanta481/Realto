<template>
    <section class="py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Project Categories</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Find properties that match your exact requirements</p>
            </div>

            <!-- Categories Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-6">
                <div v-for="category in categories" :key="category.name"
                     class="group cursor-pointer">
                    <div class="bg-slate-50 rounded-2xl p-6 text-center transition-all duration-300 hover:bg-gradient-to-br hover:from-blue-50 hover:to-cyan-50 hover:shadow-lg hover:-translate-y-1 border border-transparent hover:border-blue-100">
                        <!-- Icon Container -->
                        <div class="w-16 h-16 mx-auto mb-4 bg-white rounded-2xl flex items-center justify-center shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-blue-600 group-hover:scale-110">
                            <component :is="category.icon" 
                                       class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors duration-300" />
                        </div>
                        <h3 class="text-slate-800 font-semibold group-hover:text-blue-600 transition-colors">{{ category.name }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { h } from 'vue';

// Icon components as render functions
const BungalowIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
            })
        ]);
    }
};

const VillaIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'
            })
        ]);
    }
};

const ApartmentIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
            })
        ]);
    }
};

const OfficeIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
            })
        ]);
    }
};

const WarehouseIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
            })
        ]);
    }
};

const ShopIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 
                'stroke-linecap': 'round', 
                'stroke-linejoin': 'round', 
                'stroke-width': '1.5',
                d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'
            })
        ]);
    }
};

const categories = [
    { name: 'Bungalow', icon: BungalowIcon },
    { name: 'Villa', icon: VillaIcon },
    { name: 'Apartment', icon: ApartmentIcon },
    { name: 'Office', icon: OfficeIcon },
    { name: 'Warehouse', icon: WarehouseIcon },
    { name: 'Shops', icon: ShopIcon },
];
</script>
