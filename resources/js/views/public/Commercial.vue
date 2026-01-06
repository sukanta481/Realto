<template>
    <PublicLayout>
        <PageHero 
            title="Commercial Properties"
            subtitle="Premium office spaces, retail outlets, and industrial properties for your business needs"
            backgroundImage="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
        />

        <!-- Property Categories -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Commercial Property Types</h2>
                    <p class="text-slate-600 max-w-2xl mx-auto">Find the perfect space for your business from our diverse collection</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                    <div v-for="category in categories" :key="category.id"
                         class="group relative overflow-hidden rounded-2xl cursor-pointer">
                        <div class="aspect-[4/3]">
                            <img :src="category.image" :alt="category.title" 
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:bg-blue-500 transition-colors duration-300">
                                    <component :is="category.icon" class="w-6 h-6 text-white" />
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">{{ category.title }}</h3>
                            <p class="text-slate-300 text-sm mb-4">{{ category.description }}</p>
                            <span class="inline-flex items-center gap-2 text-blue-400 font-semibold text-sm group-hover:text-blue-300 transition-colors">
                                {{ category.count }} Properties Available
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Commercial Properties -->
        <section class="py-16 lg:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-12">
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-2">Featured Commercial Spaces</h2>
                        <p class="text-slate-600">Handpicked premium commercial properties</p>
                    </div>
                    <div class="flex gap-3">
                        <button v-for="filter in propertyFilters" :key="filter"
                                @click="activeFilter = filter"
                                :class="['px-5 py-2 rounded-xl font-medium transition-all duration-300',
                                         activeFilter === filter 
                                             ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' 
                                             : 'bg-white text-slate-600 hover:bg-slate-100']">
                            {{ filter }}
                        </button>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div v-for="property in commercialProperties" :key="property.id"
                         class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                        <div class="relative overflow-hidden aspect-[4/3]">
                            <img :src="property.image" :alt="property.name"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg">
                                <span class="text-blue-600 font-bold">{{ property.price }}</span>
                            </div>
                            <div class="absolute top-4 right-4 px-3 py-1.5 bg-blue-500 text-white rounded-lg text-xs font-semibold">
                                {{ property.type }}
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">{{ property.name }}</h3>
                            <div class="flex items-center gap-1 text-slate-500 text-sm mb-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                {{ property.location }}
                            </div>
                            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-1.5 text-slate-600">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                    <span class="text-sm">{{ property.sqft }} Sq.Ft</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-600">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-sm">{{ property.floors }} Floor(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us for Commercial -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-6">
                            Why Choose Us for 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-cyan-500">Commercial Properties?</span>
                        </h2>
                        <p class="text-slate-600 mb-8">
                            We specialize in helping businesses find the perfect commercial space. Our expertise and network ensure you get the best deals.
                        </p>
                        <div class="space-y-6">
                            <div v-for="feature in commercialFeatures" :key="feature.title" 
                                 class="flex items-start gap-4 group">
                                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-blue-500 transition-colors duration-300">
                                    <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">{{ feature.title }}</h4>
                                    <p class="text-slate-600 text-sm">{{ feature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="rounded-2xl overflow-hidden shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                 alt="Commercial Office" 
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-xl p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-slate-900">500+</div>
                                    <div class="text-slate-600 text-sm">Businesses Served</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <TalkToAdvisor />
    </PublicLayout>
</template>

<script setup>
import { ref, h } from 'vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import PageHero from '@/components/public/sections/PageHero.vue';
import TalkToAdvisor from '@/components/public/sections/TalkToAdvisor.vue';

const activeFilter = ref('All');
const propertyFilters = ['All', 'Office', 'Retail', 'Industrial'];

// Icon components
const OfficeIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
        ]);
    }
};

const RetailIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' })
        ]);
    }
};

const WarehouseIcon = {
    render() {
        return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '1.5',
                d: 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z' })
        ]);
    }
};

const categories = [
    {
        id: 1,
        title: 'Office Spaces',
        description: 'Modern office spaces in prime business districts',
        count: 120,
        image: 'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        icon: OfficeIcon
    },
    {
        id: 2,
        title: 'Retail Shops',
        description: 'High-footfall retail locations for your business',
        count: 85,
        image: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        icon: RetailIcon
    },
    {
        id: 3,
        title: 'Industrial & Warehouses',
        description: 'Spacious industrial units and warehousing solutions',
        count: 45,
        image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        icon: WarehouseIcon
    }
];

const commercialProperties = [
    {
        id: 1,
        name: 'Premium Office Space',
        location: 'Cyber City, Gurugram',
        price: '₹85/sqft/mo',
        type: 'Office',
        sqft: '5,200',
        floors: 2,
        image: 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 2,
        name: 'MG Road Retail Shop',
        location: 'MG Road, Gurugram',
        price: '₹1.2 Lakh/mo',
        type: 'Retail',
        sqft: '1,800',
        floors: 1,
        image: 'https://images.unsplash.com/photo-1604719312566-8912e9227c6a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 3,
        name: 'Industrial Warehouse',
        location: 'IMT Manesar',
        price: '₹25/sqft/mo',
        type: 'Industrial',
        sqft: '15,000',
        floors: 1,
        image: 'https://images.unsplash.com/photo-1553413077-190dd305871c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 4,
        name: 'Coworking Space',
        location: 'Sector 44, Noida',
        price: '₹15,000/seat/mo',
        type: 'Office',
        sqft: '8,000',
        floors: 1,
        image: 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 5,
        name: 'Mall Space',
        location: 'Select City Walk, Delhi',
        price: '₹450/sqft/mo',
        type: 'Retail',
        sqft: '2,500',
        floors: 1,
        image: 'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 6,
        name: 'IT Park Office',
        location: 'Electronic City, Noida',
        price: '₹65/sqft/mo',
        type: 'Office',
        sqft: '12,000',
        floors: 3,
        image: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    }
];

const commercialFeatures = [
    {
        title: 'Prime Locations',
        description: 'Access to the best commercial addresses in NCR region'
    },
    {
        title: 'Flexible Terms',
        description: 'Customizable lease agreements to suit your business needs'
    },
    {
        title: 'Legal Assistance',
        description: 'Complete legal support for documentation and agreements'
    },
    {
        title: 'After-Sales Support',
        description: 'Dedicated support team for all your post-purchase needs'
    }
];
</script>
