<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ pageTitle }} Content Editor</h1>
                <p class="text-slate-500 mt-1">Edit and manage content for the {{ pageKey }} page</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="resetForm" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </button>
                <button @click="saveContent" :disabled="saving" class="btn-primary">
                    <svg v-if="saving" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ saving ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </div>

        <!-- Success Message -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div v-if="showSuccess" class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-700">Content saved successfully!</p>
            </div>
        </transition>

        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Content Sections -->
        <div v-else class="space-y-6">
            <div v-for="section in sections" :key="section.key" class="card">
                <div class="card-header flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ section.label }}</h3>
                            <p class="text-xs text-slate-500">Section: {{ section.key }}</p>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" v-model="formData[section.key].is_active" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors">
                            <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                        <span class="text-sm text-slate-600">Active</span>
                    </label>
                </div>
                <div class="card-body space-y-5">
                    <!-- Title -->
                    <div>
                        <label class="form-label">Title</label>
                        <input 
                            type="text" 
                            v-model="formData[section.key].title" 
                            class="form-input"
                            :placeholder="`Enter ${section.label.toLowerCase()} title`"
                        >
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label class="form-label">Subtitle</label>
                        <input 
                            type="text" 
                            v-model="formData[section.key].subtitle" 
                            class="form-input"
                            :placeholder="`Enter ${section.label.toLowerCase()} subtitle`"
                        >
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="form-label">Description</label>
                        <textarea 
                            v-model="formData[section.key].description" 
                            rows="4"
                            class="form-input resize-none"
                            :placeholder="`Enter ${section.label.toLowerCase()} description`"
                        ></textarea>
                    </div>

                    <!-- Image URL -->
                    <div>
                        <label class="form-label">Image URL</label>
                        <div class="flex gap-3">
                            <input 
                                type="text" 
                                v-model="formData[section.key].image_url" 
                                class="form-input flex-1"
                                placeholder="Enter image URL or upload"
                            >
                            <label class="btn-secondary cursor-pointer">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Upload
                                <input type="file" @change="e => handleImageUpload(e, section.key)" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <div v-if="formData[section.key].image_url" class="mt-3">
                            <img :src="formData[section.key].image_url" alt="Preview" class="h-32 rounded-lg object-cover">
                        </div>
                    </div>

                    <!-- Button Text & URL (conditional) -->
                    <div v-if="section.hasButton" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Button Text</label>
                            <input 
                                type="text" 
                                v-model="formData[section.key].button_text" 
                                class="form-input"
                                placeholder="e.g., Learn More"
                            >
                        </div>
                        <div>
                            <label class="form-label">Button URL</label>
                            <input 
                                type="text" 
                                v-model="formData[section.key].button_url" 
                                class="form-input"
                                placeholder="e.g., /contact"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/api';

const route = useRoute();

const loading = ref(true);
const saving = ref(false);
const showSuccess = ref(false);
const formData = ref({});

// Page configurations
const pageConfigs = {
    home: {
        title: 'Home Page',
        sections: [
            { key: 'hero', label: 'Hero Section', hasButton: true },
            { key: 'stats', label: 'Statistics Section', hasButton: false },
            { key: 'why_us', label: 'Why Choose Us', hasButton: true },
            { key: 'services', label: 'Services Section', hasButton: true },
            { key: 'cta', label: 'Call to Action', hasButton: true },
        ]
    },
    about: {
        title: 'About Page',
        sections: [
            { key: 'hero', label: 'Hero Section', hasButton: false },
            { key: 'story', label: 'Our Story', hasButton: false },
            { key: 'mission', label: 'Mission', hasButton: false },
            { key: 'vision', label: 'Vision', hasButton: false },
        ]
    },
    services: {
        title: 'Services Page',
        sections: [
            { key: 'hero', label: 'Hero Section', hasButton: false },
            { key: 'services', label: 'Services List', hasButton: false },
            { key: 'process', label: 'Our Process', hasButton: false },
        ]
    },
    contact: {
        title: 'Contact Page',
        sections: [
            { key: 'hero', label: 'Hero Section', hasButton: false },
            { key: 'info', label: 'Contact Info', hasButton: false },
            { key: 'hours', label: 'Business Hours', hasButton: false },
        ]
    },
    testimonials: {
        title: 'Testimonials',
        sections: [
            { key: 'hero', label: 'Section Header', hasButton: false },
        ]
    }
};

const pageKey = computed(() => route.params.pageKey || 'home');
const pageConfig = computed(() => pageConfigs[pageKey.value] || pageConfigs.home);
const pageTitle = computed(() => pageConfig.value.title);
const sections = computed(() => pageConfig.value.sections);

// Initialize form data structure
const initFormData = () => {
    const data = {};
    sections.value.forEach(section => {
        data[section.key] = {
            title: '',
            subtitle: '',
            description: '',
            image_url: '',
            button_text: '',
            button_url: '',
            is_active: true,
            content: {}
        };
    });
    return data;
};

// Load content from API
const loadContent = async () => {
    loading.value = true;
    formData.value = initFormData();
    
    try {
        const response = await api.get(`/content/${pageKey.value}`);
        if (response.data.success) {
            // Merge API data with form structure
            Object.entries(response.data.data).forEach(([key, value]) => {
                if (formData.value[key]) {
                    formData.value[key] = { ...formData.value[key], ...value };
                }
            });
        }
    } catch (error) {
        console.error('Error loading content:', error);
    } finally {
        loading.value = false;
    }
};

// Save content
const saveContent = async () => {
    saving.value = true;
    
    try {
        await api.post(`/content/${pageKey.value}/bulk`, {
            sections: formData.value
        });
        
        showSuccess.value = true;
        setTimeout(() => {
            showSuccess.value = false;
        }, 3000);
    } catch (error) {
        console.error('Error saving content:', error);
        alert('Failed to save content. Please try again.');
    } finally {
        saving.value = false;
    }
};

// Reset form
const resetForm = () => {
    if (confirm('Are you sure you want to reset all changes?')) {
        loadContent();
    }
};

// Handle image upload
const handleImageUpload = async (event, sectionKey) => {
    const file = event.target.files[0];
    if (!file) return;
    
    const formDataUpload = new FormData();
    formDataUpload.append('image', file);
    
    try {
        const response = await api.post('/content/upload-image', formDataUpload, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.success) {
            formData.value[sectionKey].image_url = response.data.url;
        }
    } catch (error) {
        console.error('Error uploading image:', error);
        alert('Failed to upload image. Please try again.');
    }
};

// Watch for route changes
watch(() => route.params.pageKey, () => {
    loadContent();
});

onMounted(() => {
    loadContent();
});
</script>
