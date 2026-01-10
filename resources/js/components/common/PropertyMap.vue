<template>
    <div class="property-map-container">
        <!-- Map Container -->
        <div 
            ref="mapContainer" 
            class="w-full rounded-xl overflow-hidden shadow-lg"
            :style="{ height: height }"
        ></div>
        
        <!-- Selected Property Card -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <div 
                v-if="selectedProperty"
                class="absolute bottom-4 left-4 right-4 bg-white rounded-xl shadow-xl p-4 z-[1000]"
            >
                <button 
                    @click="selectedProperty = null"
                    class="absolute top-2 right-2 p-1 text-slate-400 hover:text-slate-600"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="flex gap-4">
                    <div class="w-24 h-20 bg-slate-100 rounded-lg flex-shrink-0 overflow-hidden">
                        <img 
                            v-if="getPropertyImage(selectedProperty)"
                            :src="getPropertyImage(selectedProperty)"
                            :alt="selectedProperty.title"
                            class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-slate-900 truncate">{{ selectedProperty.title }}</h4>
                        <p class="text-sm text-slate-500 truncate">{{ selectedProperty.locality }}, {{ selectedProperty.city }}</p>
                        <p class="text-lg font-bold text-indigo-600 mt-1">{{ formatPrice(selectedProperty.price) }}</p>
                        <div class="flex gap-3 text-xs text-slate-500 mt-1">
                            <span v-if="selectedProperty.bhk">{{ selectedProperty.bhk }} BHK</span>
                            <span v-if="selectedProperty.carpet_area">{{ selectedProperty.carpet_area }} {{ selectedProperty.area_unit }}</span>
                        </div>
                    </div>
                </div>
                <button 
                    @click="viewProperty(selectedProperty)"
                    class="w-full mt-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    View Details
                </button>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
    properties: {
        type: Array,
        default: () => []
    },
    height: {
        type: String,
        default: '400px'
    },
    center: {
        type: Array,
        default: () => [28.6139, 77.2090] // Delhi default
    },
    zoom: {
        type: Number,
        default: 11
    }
});

const router = useRouter();
const mapContainer = ref(null);
const map = ref(null);
const markers = ref([]);
const selectedProperty = ref(null);

const formatPrice = (price) => {
    if (!price) return 'Price on Request';
    if (price >= 10000000) return '₹' + (price / 10000000).toFixed(2) + ' Cr';
    if (price >= 100000) return '₹' + (price / 100000).toFixed(2) + ' L';
    return '₹' + price.toLocaleString();
};

const getPropertyImage = (property) => {
    if (property.property_images?.length) {
        const coverImage = property.property_images.find(img => img.is_cover) || property.property_images[0];
        return coverImage?.url || coverImage?.image_url;
    }
    return null;
};

const viewProperty = (property) => {
    router.push(`/app/properties/${property.id}`);
};

const initMap = () => {
    if (!window.L || !mapContainer.value) return;
    
    // Initialize map
    map.value = L.map(mapContainer.value).setView(props.center, props.zoom);
    
    // Add tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map.value);
    
    // Add markers
    updateMarkers();
};

const updateMarkers = () => {
    if (!map.value) return;
    
    // Clear existing markers
    markers.value.forEach(marker => marker.remove());
    markers.value = [];
    
    // Add new markers
    const bounds = [];
    
    props.properties.forEach(property => {
        if (property.latitude && property.longitude) {
            const lat = parseFloat(property.latitude);
            const lng = parseFloat(property.longitude);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'property-marker',
                        html: `
                            <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg border-2 border-white cursor-pointer transform hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        `,
                        iconSize: [40, 40],
                        iconAnchor: [20, 40]
                    })
                }).addTo(map.value);
                
                marker.on('click', () => {
                    selectedProperty.value = property;
                });
                
                markers.value.push(marker);
                bounds.push([lat, lng]);
            }
        }
    });
    
    // Fit bounds if we have markers
    if (bounds.length > 0) {
        map.value.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
    }
};

// Load Leaflet CSS and JS
const loadLeaflet = () => {
    return new Promise((resolve) => {
        if (window.L) {
            resolve();
            return;
        }
        
        // Load CSS
        const css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(css);
        
        // Load JS
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.onload = resolve;
        document.head.appendChild(script);
    });
};

watch(() => props.properties, () => {
    updateMarkers();
}, { deep: true });

onMounted(async () => {
    await loadLeaflet();
    initMap();
});

onUnmounted(() => {
    if (map.value) {
        map.value.remove();
    }
});
</script>

<style>
.property-map-container {
    position: relative;
}

.property-marker {
    background: transparent !important;
    border: none !important;
}
</style>
