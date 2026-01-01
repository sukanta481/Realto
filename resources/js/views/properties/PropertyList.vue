<template>
    <div class="space-y-8 pb-20 lg:pb-0 animate-fade-in">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Properties</h1>
                <p class="text-slate-500 mt-1">Your property inventory</p>
            </div>
            <button class="btn-primary" @click="showPropertyModal = true">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Property
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 stagger-children">
            <div class="card p-5 text-center group hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-indigo-600">{{ stats.available }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Available</p>
            </div>
            <div class="card p-5 text-center group hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-emerald-600">{{ stats.sold }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Sold</p>
            </div>
            <div class="card p-5 text-center group hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-amber-600">{{ stats.rented }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Rented</p>
            </div>
            <div class="card p-5 text-center group hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-slate-600">{{ stats.hold }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">On Hold</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card p-5">
            <div class="flex flex-wrap gap-4">
                <div class="relative flex-1 min-w-[200px]">
                    <input 
                        v-model="filters.search"
                        type="text"
                        placeholder="Search properties..."
                        class="form-input pl-11"
                    />
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <select v-model="filters.listing_type" class="form-select min-w-[140px]">
                    <option value="">All Types</option>
                    <option value="sale">For Sale</option>
                    <option value="rent">For Rent</option>
                </select>
                <select v-model="filters.status" class="form-select min-w-[140px]">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="hold">On Hold</option>
                    <option value="sold">Sold</option>
                    <option value="rented">Rented</option>
                </select>
            </div>
        </div>

        <!-- Property Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 stagger-children">
            <div 
                v-for="property in properties" 
                :key="property.id"
                @click="$router.push(`/properties/${property.id}`)"
                class="card overflow-hidden cursor-pointer group"
            >
                <!-- Image -->
                <div class="aspect-[4/3] bg-gradient-to-br from-slate-100 to-slate-200 relative overflow-hidden">
                    <img 
                        v-if="property.images?.[0]"
                        :src="property.images[0]"
                        :alt="property.title"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <!-- Badges -->
                    <div class="absolute top-3 left-3 right-3 flex justify-between">
                        <span class="badge bg-white/90 backdrop-blur-sm text-slate-700 font-semibold shadow-sm">
                            {{ property.listing_type === 'sale' ? 'For Sale' : 'For Rent' }}
                        </span>
                        <span 
                            class="badge shadow-sm"
                            :class="{
                                'bg-emerald-500 text-white': property.status === 'available',
                                'bg-slate-500 text-white': property.status === 'hold',
                                'bg-indigo-500 text-white': property.status === 'sold',
                                'bg-amber-500 text-white': property.status === 'rented',
                            }"
                        >
                            {{ property.status }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-5">
                    <h3 class="font-semibold text-slate-900 mb-1.5 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                        {{ property.title }}
                    </h3>
                    <p class="text-sm text-slate-500 mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ property.locality }}, {{ property.city }}
                    </p>
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <p class="text-xl font-bold text-indigo-600">{{ formatPrice(property.price) }}</p>
                        <p class="text-sm text-slate-500">{{ property.bhk || '' }} {{ formatArea(property) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && properties.length === 0" class="card empty-state">
            <div class="empty-state-icon">
                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">No properties yet</h3>
            <p class="text-slate-500 mb-6">Start by adding your first property</p>
            <button class="btn-primary" @click="showPropertyModal = true">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Property
            </button>
        </div>
    </div>

    <!-- Property Form Modal -->
    <PropertyFormModal 
        v-model="showPropertyModal"
        @saved="handlePropertySaved"
    />
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { propertiesApi } from '../../api';
import PropertyFormModal from '../../components/common/PropertyFormModal.vue';

const properties = ref([]);
const stats = ref({ available: 0, sold: 0, rented: 0, hold: 0 });
const loading = ref(true);
const showPropertyModal = ref(false);

const filters = reactive({
    search: '',
    listing_type: '',
    status: '',
});

const fetchProperties = async () => {
    loading.value = true;
    try {
        const response = await propertiesApi.getAll(filters);
        properties.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch properties:', error);
    } finally {
        loading.value = false;
    }
};

const fetchStats = async () => {
    try {
        const response = await propertiesApi.getStats();
        stats.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch stats:', error);
    }
};

const formatPrice = (price) => {
    if (!price) return 'Price on Request';
    if (price >= 10000000) return '₹' + (price / 10000000).toFixed(2) + ' Cr';
    if (price >= 100000) return '₹' + (price / 100000).toFixed(2) + ' L';
    return '₹' + price.toLocaleString('en-IN');
};

const formatArea = (property) => {
    const area = property.super_built_up_area || property.built_up_area || property.carpet_area;
    if (!area) return '';
    return area.toLocaleString('en-IN') + ' ' + (property.area_unit || 'sqft');
};

let debounceTimer;
watch(filters, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchProperties, 300);
});

const handlePropertySaved = () => {
    fetchProperties();
    fetchStats();
};

onMounted(() => {
    fetchProperties();
    fetchStats();
});
</script>
