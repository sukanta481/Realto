<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="isOpen" 
                class="fixed inset-0 z-50 overflow-y-auto"
                @keydown.esc="close"
            >
                <!-- Backdrop -->
                <div 
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                    @click="close"
                ></div>

                <!-- Search Modal -->
                <div class="flex min-h-full items-start justify-center p-4 pt-[15vh]">
                    <Transition
                        enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0 scale-95 -translate-y-4"
                        enter-to-class="opacity-100 scale-100 translate-y-0"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100 scale-100 translate-y-0"
                        leave-to-class="opacity-0 scale-95 -translate-y-4"
                    >
                        <div 
                            v-if="isOpen"
                            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden"
                            @click.stop
                        >
                            <!-- Search Input -->
                            <div class="flex items-center px-4 border-b border-slate-100">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input 
                                    ref="searchInput"
                                    v-model="query"
                                    type="text" 
                                    placeholder="Search leads, properties, clients, deals..."
                                    class="flex-1 px-4 py-4 text-lg border-0 focus:outline-none focus:ring-0"
                                    @input="debouncedSearch"
                                />
                                <div class="flex items-center gap-2">
                                    <span v-if="loading" class="text-slate-400">
                                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    <kbd class="hidden sm:inline-flex items-center px-2 py-1 text-xs font-medium text-slate-400 bg-slate-100 rounded">
                                        ESC
                                    </kbd>
                                </div>
                            </div>

                            <!-- Filter Tabs -->
                            <div class="flex items-center gap-1 px-4 py-2 border-b border-slate-100 overflow-x-auto">
                                <button 
                                    v-for="tab in tabs" 
                                    :key="tab.value"
                                    @click="activeTab = tab.value"
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg whitespace-nowrap transition-colors"
                                    :class="activeTab === tab.value 
                                        ? 'bg-primary-100 text-primary-700' 
                                        : 'text-slate-500 hover:bg-slate-100'"
                                >
                                    {{ tab.label }}
                                </button>
                            </div>

                            <!-- Results -->
                            <div class="max-h-[50vh] overflow-y-auto">
                                <!-- Loading State -->
                                <div v-if="loading && !results.length" class="py-12 text-center">
                                    <div class="w-8 h-8 border-2 border-primary-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                                    <p class="mt-2 text-sm text-slate-500">Searching...</p>
                                </div>

                                <!-- Empty State -->
                                <div v-else-if="query.length >= 2 && !loading && !results.length" class="py-12 text-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-slate-500">No results found for "{{ query }}"</p>
                                    <p class="text-sm text-slate-400 mt-1">Try a different search term</p>
                                </div>

                                <!-- Initial State -->
                                <div v-else-if="query.length < 2 && !results.length" class="py-12 text-center">
                                    <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-slate-500">Type to search across all data</p>
                                    <p class="text-sm text-slate-400 mt-1">Leads, properties, clients, and deals</p>
                                </div>

                                <!-- Results List -->
                                <div v-else class="divide-y divide-slate-100">
                                    <button
                                        v-for="(result, index) in filteredResults"
                                        :key="`${result.type}-${result.id}`"
                                        @click="goToResult(result)"
                                        class="w-full flex items-center gap-4 px-4 py-3 text-left hover:bg-slate-50 transition-colors"
                                        :class="{ 'bg-slate-50': selectedIndex === index }"
                                    >
                                        <!-- Icon -->
                                        <div 
                                            class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                            :class="getIconBg(result.type)"
                                        >
                                            <component :is="getIcon(result.type)" class="w-5 h-5" :class="getIconColor(result.type)" />
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="font-medium text-slate-900 truncate">{{ result.title }}</p>
                                                <span 
                                                    class="px-2 py-0.5 text-xs font-medium rounded-full"
                                                    :style="{ backgroundColor: result.status_color + '20', color: result.status_color }"
                                                >
                                                    {{ result.status }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-slate-500 truncate">
                                                {{ result.subtitle }}
                                                <span v-if="result.description"> · {{ result.description }}</span>
                                            </p>
                                        </div>

                                        <!-- Type Badge -->
                                        <span class="text-xs font-medium text-slate-400 uppercase flex-shrink-0">
                                            {{ result.type }}
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500">
                                <div class="flex items-center gap-4">
                                    <span class="flex items-center gap-1">
                                        <kbd class="px-1.5 py-0.5 bg-white rounded border">↑</kbd>
                                        <kbd class="px-1.5 py-0.5 bg-white rounded border">↓</kbd>
                                        to navigate
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <kbd class="px-1.5 py-0.5 bg-white rounded border">↵</kbd>
                                        to select
                                    </span>
                                </div>
                                <span v-if="results.length">
                                    {{ filteredResults.length }} result{{ filteredResults.length !== 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted, onUnmounted, h } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const router = useRouter();
const searchInput = ref(null);
const query = ref('');
const results = ref([]);
const loading = ref(false);
const selectedIndex = ref(0);
const activeTab = ref('all');

const tabs = [
    { label: 'All', value: 'all' },
    { label: 'Leads', value: 'lead' },
    { label: 'Properties', value: 'property' },
    { label: 'Clients', value: 'client' },
    { label: 'Deals', value: 'deal' },
];

const isOpen = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const filteredResults = computed(() => {
    if (activeTab.value === 'all') return results.value;
    return results.value.filter(r => r.type === activeTab.value);
});

// Debounced search
let searchTimeout = null;
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    if (query.value.length < 2) {
        results.value = [];
        return;
    }
    searchTimeout = setTimeout(search, 300);
};

const search = async () => {
    if (query.value.length < 2) return;
    
    loading.value = true;
    try {
        const response = await api.get('/search', { params: { q: query.value, limit: 20 } });
        results.value = response.data.data;
        selectedIndex.value = 0;
    } catch (error) {
        console.error('Search failed:', error);
        results.value = [];
    } finally {
        loading.value = false;
    }
};

const close = () => {
    isOpen.value = false;
    query.value = '';
    results.value = [];
    activeTab.value = 'all';
};

const goToResult = (result) => {
    router.push(result.url);
    close();
};

// Focus input when opened
watch(isOpen, async (val) => {
    if (val) {
        await nextTick();
        searchInput.value?.focus();
    }
});

// Keyboard navigation
const handleKeydown = (e) => {
    if (!isOpen.value) {
        // Open with Ctrl+K or Cmd+K
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            isOpen.value = true;
        }
        return;
    }

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex.value = Math.min(selectedIndex.value + 1, filteredResults.value.length - 1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex.value = Math.max(selectedIndex.value - 1, 0);
    } else if (e.key === 'Enter' && filteredResults.value[selectedIndex.value]) {
        e.preventDefault();
        goToResult(filteredResults.value[selectedIndex.value]);
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});

// Icon helpers
const getIconBg = (type) => ({
    'lead': 'bg-blue-100',
    'property': 'bg-emerald-100',
    'client': 'bg-purple-100',
    'deal': 'bg-amber-100',
}[type] || 'bg-slate-100');

const getIconColor = (type) => ({
    'lead': 'text-blue-600',
    'property': 'text-emerald-600',
    'client': 'text-purple-600',
    'deal': 'text-amber-600',
}[type] || 'text-slate-600');

const getIcon = (type) => {
    const icons = {
        lead: {
            render() {
                return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
                ]);
            }
        },
        property: {
            render() {
                return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
                ]);
            }
        },
        client: {
            render() {
                return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
                ]);
            }
        },
        deal: {
            render() {
                return h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' })
                ]);
            }
        },
    };
    return icons[type] || icons.lead;
};
</script>
