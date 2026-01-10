<template>
    <div class="space-y-6 pb-20 lg:pb-0">
        <!-- Back + Header -->
        <div class="flex items-start gap-4">
            <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded-lg mt-1">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ property?.title }}</h1>
                <p class="text-gray-500">{{ property?.locality }}, {{ property?.city }}</p>
            </div>
            <button 
                @click="showEditModal = true" 
                class="btn-secondary flex items-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </button>
            <span 
                class="badge"
                :class="{
                    'bg-success-500 text-white': property?.status === 'available',
                    'bg-gray-500 text-white': property?.status === 'hold',
                    'bg-primary-500 text-white': property?.status === 'sold',
                    'bg-warning-500 text-white': property?.status === 'rented',
                }"
            >
                {{ property?.status }}
            </span>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Image Gallery -->
                <div class="card overflow-hidden">
                    <div class="aspect-video bg-gray-200">
                        <img 
                            v-if="propertyImages.length > 0"
                            :src="propertyImages[selectedImage]"
                            :alt="property?.title"
                            class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div v-if="propertyImages.length > 1" class="p-3 flex gap-2 overflow-x-auto">
                        <button
                            v-for="(img, index) in propertyImages"
                            :key="index"
                            @click="selectedImage = index"
                            class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0"
                            :class="{ 'ring-2 ring-primary-500': selectedImage === index }"
                        >
                            <img :src="img" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Details -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Property Details</h3>
                    </div>
                    <div class="card-body grid sm:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Type</p>
                            <p class="font-medium text-gray-900">{{ property?.property_type?.name || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">BHK</p>
                            <p class="font-medium text-gray-900">{{ property?.bhk || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Carpet Area</p>
                            <p class="font-medium text-gray-900">{{ property?.carpet_area }} {{ property?.area_unit }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Built-up Area</p>
                            <p class="font-medium text-gray-900">{{ property?.built_up_area || '-' }} {{ property?.area_unit }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Floor</p>
                            <p class="font-medium text-gray-900">{{ property?.floor || '-' }} of {{ property?.total_floors || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Facing</p>
                            <p class="font-medium text-gray-900">{{ property?.facing || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Furnishing</p>
                            <p class="font-medium text-gray-900 capitalize">{{ property?.furnishing || 'Unfurnished' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Age</p>
                            <p class="font-medium text-gray-900">{{ property?.age_of_property ? property.age_of_property + ' years' : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Availability</p>
                            <p class="font-medium text-gray-900 capitalize">{{ property?.availability || 'Ready' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Description</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-gray-600">{{ property?.description || 'No description available' }}</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Price Card -->
                <div class="card">
                    <div class="card-body text-center">
                        <p class="text-3xl font-bold text-primary-600">{{ formatPrice(property?.price) }}</p>
                        <p class="text-sm text-gray-500">
                            {{ property?.listing_type === 'sale' ? 'Sale Price' : 'Monthly Rent' }}
                            <span v-if="property?.price_negotiable" class="text-success-600"> • Negotiable</span>
                        </p>
                        <div class="mt-4 space-y-3">
                            <button class="btn-primary w-full">Share Property</button>
                            <button 
                                v-if="property?.status === 'available'"
                                class="btn-outline w-full"
                            >
                                Mark as {{ property?.listing_type === 'sale' ? 'Sold' : 'Rented' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Owner Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Owner Details</h3>
                    </div>
                    <div class="card-body">
                        <p class="font-medium text-gray-900">{{ property?.owner_name || 'Not specified' }}</p>
                        <a 
                            v-if="property?.owner_phone"
                            :href="`tel:${property.owner_phone}`"
                            class="text-primary-600 hover:underline"
                        >
                            {{ property.owner_phone }}
                        </a>
                    </div>
                </div>

                <!-- Matching Leads -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Matching Leads</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="divide-y divide-gray-100">
                            <div 
                                v-for="lead in matchingLeads" 
                                :key="lead.id"
                                @click="$router.push(`/leads/${lead.id}`)"
                                class="p-3 hover:bg-gray-50 cursor-pointer"
                            >
                                <p class="font-medium text-gray-900">{{ lead.name }}</p>
                                <p class="text-sm text-gray-500">{{ lead.budget_range }}</p>
                            </div>
                            <div v-if="!matchingLeads?.length" class="p-4 text-center text-gray-500 text-sm">
                                No matching leads
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <PropertyFormModal
        v-model="showEditModal"
        :property="property"
        @saved="handlePropertySaved"
    />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { propertiesApi } from '../../api';
import PropertyFormModal from '../../components/common/PropertyFormModal.vue';

const route = useRoute();
const property = ref(null);
const matchingLeads = ref([]);
const selectedImage = ref(0);
const loading = ref(true);
const showEditModal = ref(false);

// Compute images from property_images or images array
const propertyImages = computed(() => {
    if (property.value?.property_images?.length) {
        return property.value.property_images.map(img => img.url || img.image_url);
    }
    if (property.value?.images?.length) {
        return property.value.images;
    }
    return [];
});

const formatPrice = (price) => {
    if (!price) return 'Price on Request';
    if (price >= 10000000) return '₹' + (price / 10000000).toFixed(2) + ' Cr';
    if (price >= 100000) return '₹' + (price / 100000).toFixed(2) + ' L';
    return '₹' + price.toLocaleString();
};

const fetchProperty = async () => {
    loading.value = true;
    try {
        const response = await propertiesApi.getOne(route.params.id);
        property.value = response.data.data.property;
        matchingLeads.value = response.data.data.matching_leads || [];
    } catch (error) {
        console.error('Failed to fetch property:', error);
    } finally {
        loading.value = false;
    }
};

const handlePropertySaved = () => {
    fetchProperty();
    showEditModal.value = false;
};

onMounted(fetchProperty);
</script>
