<template>
    <PublicLayout>
        <PageHero 
            title="Residential Properties"
            subtitle="Discover your dream home from our curated collection of premium apartments, villas, and bungalows"
            backgroundImage="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
        />

        <!-- Search & Filters Section -->
        <section class="py-8 bg-white sticky top-16 lg:top-20 z-40 shadow-sm border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="handleSearch" class="flex flex-wrap gap-4">
                    <!-- Location Filter -->
                    <div class="flex-1 min-w-[200px]">
                        <select v-model="filters.location" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">All Locations</option>
                            <option value="delhi">Delhi</option>
                            <option value="gurugram">Gurugram</option>
                            <option value="noida">Noida</option>
                            <option value="faridabad">Faridabad</option>
                            <option value="ghaziabad">Ghaziabad</option>
                        </select>
                    </div>

                    <!-- Property Type Filter -->
                    <div class="flex-1 min-w-[200px]">
                        <select v-model="filters.propertyType" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">All Property Types</option>
                            <option value="apartment">Apartment</option>
                            <option value="villa">Villa</option>
                            <option value="bungalow">Bungalow</option>
                            <option value="penthouse">Penthouse</option>
                            <option value="studio">Studio</option>
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="flex-1 min-w-[200px]">
                        <select v-model="filters.priceRange" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">Any Price</option>
                            <option value="0-50">Under ₹50 Lakh</option>
                            <option value="50-100">₹50 Lakh - ₹1 Crore</option>
                            <option value="100-200">₹1 - ₹2 Crore</option>
                            <option value="200+">Above ₹2 Crore</option>
                        </select>
                    </div>

                    <!-- BHK Filter -->
                    <div class="flex-1 min-w-[150px]">
                        <select v-model="filters.bhk" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="">Any BHK</option>
                            <option value="1">1 BHK</option>
                            <option value="2">2 BHK</option>
                            <option value="3">3 BHK</option>
                            <option value="4">4+ BHK</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105 transition-all duration-300 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>
                </form>
            </div>
        </section>

        <!-- Properties Grid Section -->
        <section class="py-16 lg:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Results Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                    <p class="text-slate-600">
                        Showing <span class="font-semibold text-slate-900">{{ properties.length }}</span> properties
                    </p>
                    <div class="flex items-center gap-4">
                        <select v-model="sortBy" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="newest">Newest First</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                </div>

                <!-- Properties Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div v-for="property in properties" :key="property.id"
                         class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                        <!-- Image Container -->
                        <div class="relative overflow-hidden aspect-[4/3]">
                            <img :src="property.image" 
                                 :alt="property.name"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <!-- Price Badge -->
                            <div class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg">
                                <span class="text-blue-600 font-bold">{{ property.price }}</span>
                            </div>
                            <!-- Status Badge -->
                            <div v-if="property.status" 
                                 :class="['absolute top-4 right-4 px-3 py-1.5 rounded-lg text-xs font-semibold', 
                                          property.status === 'New' ? 'bg-green-500 text-white' : 
                                          property.status === 'Hot' ? 'bg-orange-500 text-white' : 
                                          'bg-blue-500 text-white']">
                                {{ property.status }}
                            </div>
                            <!-- Quick Actions -->
                            <div class="absolute bottom-4 left-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                <button class="flex-1 px-4 py-2.5 bg-white text-slate-800 font-semibold rounded-xl hover:bg-blue-600 hover:text-white transition-colors">
                                    View Details
                                </button>
                                <button class="w-11 h-11 bg-white/90 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 text-xs text-blue-600 font-semibold mb-2">
                                <span class="px-2 py-0.5 bg-blue-50 rounded">{{ property.type }}</span>
                                <span class="px-2 py-0.5 bg-slate-100 rounded text-slate-600">{{ property.bhk }} BHK</span>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors">{{ property.name }}</h3>
                            <div class="flex items-center gap-1 text-slate-500 text-sm mb-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ property.location }}
                            </div>
                            
                            <!-- Features -->
                            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-1.5 text-slate-600">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                    <span class="text-sm">{{ property.beds }} Beds</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-600">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                    </svg>
                                    <span class="text-sm">{{ property.baths }} Baths</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-600">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                    <span class="text-sm">{{ property.sqft }} Sq.Ft</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Load More Button -->
                <div class="text-center mt-12">
                    <button class="px-8 py-3.5 bg-white text-slate-800 font-semibold rounded-xl border-2 border-slate-200 hover:border-blue-500 hover:text-blue-600 transition-all duration-300">
                        Load More Properties
                    </button>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <TalkToAdvisor />
    </PublicLayout>
</template>

<script setup>
import { ref } from 'vue';
import PublicLayout from '@/components/public/PublicLayout.vue';
import PageHero from '@/components/public/sections/PageHero.vue';
import TalkToAdvisor from '@/components/public/sections/TalkToAdvisor.vue';

const filters = ref({
    location: '',
    propertyType: '',
    priceRange: '',
    bhk: ''
});

const sortBy = ref('newest');

const handleSearch = () => {
    console.log('Searching with filters:', filters.value);
};

const properties = [
    {
        id: 1,
        name: 'Prestige Lakeside Habitat',
        location: 'Sector 56, Gurugram',
        price: '₹1.45 Crore Onwards',
        type: 'Apartment',
        bhk: 3,
        beds: 3,
        baths: 2,
        sqft: '1,850',
        status: 'New',
        image: 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 2,
        name: 'DLF Whitefern Residences',
        location: 'DLF Phase 3, Gurugram',
        price: '₹95 Lakh Onwards',
        type: 'Apartment',
        bhk: 2,
        beds: 2,
        baths: 2,
        sqft: '1,200',
        status: null,
        image: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 3,
        name: 'Grand Hills Villa',
        location: 'Golf Course Road',
        price: '₹2.15 Crore Onwards',
        type: 'Villa',
        bhk: 4,
        beds: 4,
        baths: 3,
        sqft: '2,400',
        status: 'Hot',
        image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 4,
        name: 'Sunrise Valley Apartments',
        location: 'Sector 82, Noida',
        price: '₹78 Lakh Onwards',
        type: 'Apartment',
        bhk: 2,
        beds: 2,
        baths: 2,
        sqft: '1,100',
        status: 'New',
        image: 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 5,
        name: 'Royal Orchid Penthouse',
        location: 'Greater Kailash, Delhi',
        price: '₹3.50 Crore Onwards',
        type: 'Penthouse',
        bhk: 4,
        beds: 4,
        baths: 4,
        sqft: '3,200',
        status: 'Premium',
        image: 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 6,
        name: 'Emerald Heights',
        location: 'Sector 65, Faridabad',
        price: '₹55 Lakh Onwards',
        type: 'Apartment',
        bhk: 3,
        beds: 3,
        baths: 2,
        sqft: '1,450',
        status: null,
        image: 'https://images.unsplash.com/photo-1600585154526-990dced4db0d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    }
];
</script>
