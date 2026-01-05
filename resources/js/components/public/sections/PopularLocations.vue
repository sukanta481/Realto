<template>
    <section class="py-16 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Popular Locations</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Explore properties in the most sought-after locations across Delhi NCR</p>
            </div>

            <!-- Locations Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-6">
                <div v-for="location in locations" :key="location.name" 
                     class="group cursor-pointer">
                    <div class="relative rounded-2xl overflow-hidden aspect-square bg-slate-100">
                        <img :src="location.image" 
                             :alt="location.name"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                        <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/20 transition-colors duration-300"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-semibold text-lg group-hover:text-blue-300 transition-colors">{{ location.name }}</h3>
                            <p class="text-slate-300 text-sm">{{ location.properties }} Properties</p>
                        </div>
                        <!-- Hover Arrow -->
                        <div class="absolute top-4 right-4 w-8 h-8 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
const locations = [
    { 
        name: 'Delhi', 
        properties: '1,200+',
        image: 'https://images.unsplash.com/photo-1587474260584-136574528ed5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    { 
        name: 'Gurugram', 
        properties: '850+',
        image: 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    { 
        name: 'Faridabad', 
        properties: '420+',
        image: 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    { 
        name: 'DLF Gurgaon', 
        properties: '380+',
        image: 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    { 
        name: 'Golf Links', 
        properties: '150+',
        image: 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
    { 
        name: 'Noida', 
        properties: '920+',
        image: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
    },
];
</script>
