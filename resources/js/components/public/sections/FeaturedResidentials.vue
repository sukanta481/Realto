<template>
    <section class="py-16 lg:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-12">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-2">Featured Residentials</h2>
                    <p class="text-slate-600">Handpicked properties for you</p>
                </div>
                <a href="#" class="inline-flex items-center gap-2 text-blue-600 font-semibold hover:text-blue-700 transition-colors group">
                    View All Properties
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
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
                        <!-- Overlay on Hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <!-- Price Badge -->
                        <div class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg">
                            <span class="text-blue-600 font-bold">{{ property.price }}</span>
                        </div>
                        <!-- Status Badge -->
                        <div v-if="property.status" 
                             :class="['absolute top-4 right-4 px-3 py-1.5 rounded-lg text-xs font-semibold', 
                                      property.status === 'New' ? 'bg-green-500 text-white' : 'bg-orange-500 text-white']">
                            {{ property.status }}
                        </div>
                        <!-- Quick Actions (visible on hover) -->
                        <div class="absolute bottom-4 left-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            <button class="flex-1 px-4 py-2.5 bg-white text-slate-800 font-semibold rounded-xl hover:bg-blue-600 hover:text-white transition-colors">
                                View Details
                            </button>
                            <button class="w-11 h-11 bg-white/90 backdrop-blur-sm rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors group/heart">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5">
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
        </div>
    </section>
</template>

<script setup>
const properties = [
    {
        id: 1,
        name: 'Prestige Lakeside',
        location: 'Sector 56, Gurugram',
        price: '₹1.45 Crore Onwards',
        beds: 3,
        baths: 2,
        sqft: '1,850',
        status: 'New',
        image: 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 2,
        name: 'DTC Whitefern',
        location: 'DLF Phase 3',
        price: '₹95 Lakh Onwards',
        beds: 2,
        baths: 2,
        sqft: '1,200',
        status: null,
        image: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
    {
        id: 3,
        name: 'DTC Grand Hills',
        location: 'Golf Course Road',
        price: '₹2.15 Crore Onwards',
        beds: 4,
        baths: 3,
        sqft: '2,400',
        status: 'Hot',
        image: 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    },
];
</script>
