<template>
    <div class="space-y-4">
        <!-- Drop Zone -->
        <div
            @dragenter.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @dragover.prevent
            @drop.prevent="handleDrop"
            @click="openFilePicker"
            class="relative border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all"
            :class="{
                'border-primary-400 bg-primary-50': isDragging,
                'border-slate-300 hover:border-primary-400 hover:bg-slate-50': !isDragging
            }"
        >
            <input
                ref="fileInput"
                type="file"
                :accept="acceptTypes"
                :multiple="multiple"
                class="hidden"
                @change="handleFileSelect"
            />

            <div v-if="!uploading" class="space-y-2">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-slate-700 font-medium">
                        <span class="text-primary-600">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-sm text-slate-500 mt-1">{{ hint }}</p>
                </div>
            </div>

            <!-- Uploading State -->
            <div v-else class="space-y-3">
                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6 text-primary-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-slate-700 font-medium">Uploading...</p>
                    <div class="w-48 h-2 bg-slate-200 rounded-full mx-auto mt-2 overflow-hidden">
                        <div 
                            class="h-full bg-primary-500 rounded-full transition-all duration-300"
                            :style="{ width: `${uploadProgress}%` }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Grid -->
        <div v-if="images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <div
                v-for="(image, index) in images"
                :key="image.id || index"
                class="relative group aspect-square rounded-xl overflow-hidden bg-slate-100"
                :class="{ 'ring-2 ring-primary-500 ring-offset-2': image.is_primary }"
            >
                <img
                    :src="image.url || image.preview"
                    :alt="image.alt_text || image.filename"
                    class="w-full h-full object-cover"
                />

                <!-- Overlay Actions -->
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <!-- Set as Primary -->
                    <button
                        v-if="!image.is_primary"
                        @click="setPrimary(index)"
                        class="p-2 bg-white rounded-lg text-slate-700 hover:bg-primary-50 hover:text-primary-600 transition-colors"
                        title="Set as primary"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </button>

                    <!-- Delete -->
                    <button
                        @click="removeImage(index)"
                        class="p-2 bg-white rounded-lg text-slate-700 hover:bg-red-50 hover:text-red-600 transition-colors"
                        title="Delete image"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Primary Badge -->
                <div v-if="image.is_primary" class="absolute top-2 left-2">
                    <span class="px-2 py-1 text-xs font-medium bg-primary-500 text-white rounded-lg">
                        Primary
                    </span>
                </div>

                <!-- File Size -->
                <div v-if="image.size" class="absolute bottom-2 right-2">
                    <span class="px-2 py-1 text-xs bg-black/50 text-white rounded">
                        {{ formatSize(image.size) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import api from '../../api';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    propertyId: {
        type: [Number, String],
        default: null,
    },
    multiple: {
        type: Boolean,
        default: true,
    },
    maxFiles: {
        type: Number,
        default: 10,
    },
    maxSize: {
        type: Number,
        default: 5 * 1024 * 1024, // 5MB
    },
    acceptTypes: {
        type: String,
        default: 'image/jpeg,image/png,image/webp',
    },
    hint: {
        type: String,
        default: 'PNG, JPG, WEBP up to 5MB (max 10 images)',
    },
});

const emit = defineEmits(['update:modelValue', 'uploaded', 'error']);

const fileInput = ref(null);
const isDragging = ref(false);
const uploading = ref(false);
const uploadProgress = ref(0);
const error = ref('');

const images = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
});

const openFilePicker = () => {
    fileInput.value?.click();
};

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files);
    processFiles(files);
    event.target.value = ''; // Reset input
};

const handleDrop = (event) => {
    isDragging.value = false;
    const files = Array.from(event.dataTransfer.files);
    processFiles(files);
};

const processFiles = async (files) => {
    error.value = '';

    // Filter valid files
    const validFiles = files.filter(file => {
        // Check type
        if (!props.acceptTypes.split(',').some(type => file.type === type.trim())) {
            error.value = `Invalid file type: ${file.name}`;
            return false;
        }
        // Check size
        if (file.size > props.maxSize) {
            error.value = `File too large: ${file.name} (max ${formatSize(props.maxSize)})`;
            return false;
        }
        return true;
    });

    // Check max files
    const remaining = props.maxFiles - images.value.length;
    if (validFiles.length > remaining) {
        error.value = `Can only add ${remaining} more image(s)`;
        validFiles.splice(remaining);
    }

    if (validFiles.length === 0) return;

    // If we have a property ID, upload immediately
    if (props.propertyId) {
        await uploadFiles(validFiles);
    } else {
        // Otherwise, just create previews
        addPreviews(validFiles);
    }
};

const addPreviews = (files) => {
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const newImage = {
                id: `temp-${Date.now()}-${index}`,
                file: file,
                filename: file.name,
                preview: e.target.result,
                size: file.size,
                is_primary: images.value.length === 0,
                order: images.value.length,
            };
            images.value = [...images.value, newImage];
        };
        reader.readAsDataURL(file);
    });
};

const uploadFiles = async (files) => {
    uploading.value = true;
    uploadProgress.value = 0;

    try {
        const formData = new FormData();
        files.forEach((file, index) => {
            formData.append(`images[${index}]`, file);
        });

        const response = await api.post(`/properties/${props.propertyId}/images`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: (progressEvent) => {
                uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            },
        });

        // Add uploaded images to the list
        const uploadedImages = response.data.data;
        images.value = [...images.value, ...uploadedImages];
        emit('uploaded', uploadedImages);
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to upload images';
        emit('error', error.value);
    } finally {
        uploading.value = false;
        uploadProgress.value = 0;
    }
};

const setPrimary = (index) => {
    const updated = images.value.map((img, i) => ({
        ...img,
        is_primary: i === index,
    }));
    images.value = updated;
};

const removeImage = async (index) => {
    const image = images.value[index];
    
    // If it's an uploaded image with an ID, delete from server
    if (image.id && !String(image.id).startsWith('temp-') && props.propertyId) {
        try {
            await api.delete(`/properties/${props.propertyId}/images/${image.id}`);
        } catch (err) {
            error.value = 'Failed to delete image';
            return;
        }
    }

    // Remove from array
    const updated = [...images.value];
    updated.splice(index, 1);

    // If removed image was primary, set first one as primary
    if (image.is_primary && updated.length > 0) {
        updated[0].is_primary = true;
    }

    images.value = updated;
};

const formatSize = (bytes) => {
    const units = ['B', 'KB', 'MB', 'GB'];
    let i = 0;
    while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
    }
    return `${bytes.toFixed(1)} ${units[i]}`;
};

// Expose method to get files for form submission
defineExpose({
    getFiles: () => images.value.filter(img => img.file).map(img => img.file),
});
</script>
