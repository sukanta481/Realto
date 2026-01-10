<template>
    <Modal
        :model-value="modelValue"
        :title="isEditing ? 'Edit Property' : 'Add New Property'"
        size="xl"
        @update:model-value="$emit('update:modelValue', $event)"
        @close="resetForm"
    >
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Basic Info -->
            <div>
                <h4 class="font-medium text-slate-900 mb-4">Basic Information</h4>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <FormField
                            v-model="form.title"
                            label="Property Title"
                            placeholder="e.g., Spacious 3BHK in Bandra West"
                            :error="errors.title"
                            required
                        />
                    </div>

                    <FormField
                        v-model="form.listing_type"
                        type="select"
                        label="Listing Type"
                        :options="listingTypeOptions"
                        :error="errors.listing_type"
                        required
                    />

                    <FormField
                        v-model="form.property_type_id"
                        type="select"
                        label="Property Type"
                        placeholder="Select type"
                        :options="propertyTypes"
                        :error="errors.property_type_id"
                    />
                </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Location -->
            <div>
                <h4 class="font-medium text-slate-900 mb-4">Location</h4>
                <div class="grid sm:grid-cols-2 gap-5">
                    <FormField
                        v-model="form.address"
                        label="Full Address"
                        placeholder="Building name, Street"
                        :error="errors.address"
                    />

                    <FormField
                        v-model="form.locality"
                        label="Locality / Area"
                        placeholder="e.g., Bandra West"
                        :error="errors.locality"
                        required
                    />

                    <FormField
                        v-model="form.city"
                        label="City"
                        placeholder="e.g., Mumbai"
                        :error="errors.city"
                        required
                    />

                    <FormField
                        v-model="form.pincode"
                        label="Pincode"
                        placeholder="400050"
                        :error="errors.pincode"
                    />
                </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Property Details -->
            <div>
                <h4 class="font-medium text-slate-900 mb-4">Property Details</h4>
                <div class="grid sm:grid-cols-3 gap-5">
                    <FormField
                        v-model="form.bhk"
                        type="select"
                        label="BHK"
                        placeholder="Select"
                        :options="bhkOptions"
                        :error="errors.bhk"
                    />

                    <FormField
                        v-model="form.carpet_area"
                        type="number"
                        label="Carpet Area (sqft)"
                        placeholder="1000"
                        :error="errors.carpet_area"
                    />

                    <FormField
                        v-model="form.built_up_area"
                        type="number"
                        label="Built-up Area (sqft)"
                        placeholder="1200"
                        :error="errors.built_up_area"
                    />

                    <FormField
                        v-model="form.floor"
                        type="number"
                        label="Floor"
                        placeholder="5"
                        :error="errors.floor"
                    />

                    <FormField
                        v-model="form.total_floors"
                        type="number"
                        label="Total Floors"
                        placeholder="20"
                        :error="errors.total_floors"
                    />

                    <FormField
                        v-model="form.furnishing"
                        type="select"
                        label="Furnishing"
                        :options="furnishingOptions"
                        :error="errors.furnishing"
                    />
                </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Pricing -->
            <div>
                <h4 class="font-medium text-slate-900 mb-4">Pricing</h4>
                <div class="grid sm:grid-cols-2 gap-5">
                    <FormField
                        v-model="form.price"
                        type="number"
                        label="Price (₹)"
                        placeholder="10000000"
                        :error="errors.price"
                        required
                    />

                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                v-model="form.price_negotiable"
                                class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span class="text-sm text-slate-700">Price Negotiable</span>
                        </label>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Owner Details -->
            <div>
                <h4 class="font-medium text-slate-900 mb-4">Owner Details</h4>
                <div class="grid sm:grid-cols-2 gap-5">
                    <FormField
                        v-model="form.owner_name"
                        label="Owner Name"
                        placeholder="Property owner's name"
                        :error="errors.owner_name"
                    />

                    <FormField
                        v-model="form.owner_phone"
                        type="tel"
                        label="Owner Phone"
                        placeholder="9876543210"
                        :error="errors.owner_phone"
                    />
                </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Property Photos -->
            <div>
                <h4 class="font-medium text-slate-900 mb-4">Property Photos</h4>
                
                <!-- Upload Area -->
                <div 
                    @drop.prevent="handleDrop"
                    @dragover.prevent="isDragging = true"
                    @dragleave="isDragging = false"
                    :class="[
                        'border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer',
                        isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-indigo-400 hover:bg-slate-50'
                    ]"
                    @click="$refs.photoInput.click()"
                >
                    <input 
                        ref="photoInput"
                        type="file"
                        multiple
                        accept="image/*"
                        class="hidden"
                        @change="handleFileSelect"
                    />
                    <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-slate-600 font-medium mb-1">Drop photos here or click to upload</p>
                    <p class="text-sm text-slate-400">PNG, JPG, WEBP up to 5MB each</p>
                </div>

                <!-- Photo Preview Grid -->
                <div v-if="photos.length > 0" class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div 
                        v-for="(photo, index) in photos" 
                        :key="index"
                        class="relative group aspect-[4/3] rounded-lg overflow-hidden bg-slate-100"
                    >
                        <img 
                            :src="photo.preview" 
                            :alt="'Photo ' + (index + 1)"
                            class="w-full h-full object-cover"
                        />
                        
                        <!-- Cover Badge -->
                        <div 
                            v-if="coverPhotoIndex === index"
                            class="absolute top-2 left-2 px-2 py-1 bg-indigo-600 text-white text-xs font-medium rounded-full"
                        >
                            Cover
                        </div>
                        
                        <!-- Overlay Actions -->
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <!-- Set as Cover -->
                            <button 
                                v-if="coverPhotoIndex !== index"
                                type="button"
                                @click.stop="setCoverPhoto(index)"
                                class="p-2 bg-white rounded-full text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                title="Set as cover"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </button>
                            
                            <!-- Remove Photo -->
                            <button 
                                type="button"
                                @click.stop="removePhoto(index)"
                                class="p-2 bg-white rounded-full text-red-500 hover:bg-red-50 transition-colors"
                                title="Remove"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <p v-if="photos.length > 0" class="mt-2 text-sm text-slate-500">
                    {{ photos.length }} photo(s) selected. Click on a photo to set it as cover.
                </p>
            </div>

            <!-- Description -->
            <FormField
                v-model="form.description"
                type="textarea"
                label="Description"
                placeholder="Describe the property..."
                :rows="4"
            />
        </form>

        <template #footer>
            <div class="flex justify-end gap-3">
                <button 
                    type="button" 
                    class="btn-secondary"
                    @click="$emit('update:modelValue', false)"
                >
                    Cancel
                </button>
                <button 
                    type="submit"
                    class="btn-primary"
                    :disabled="loading"
                    @click="handleSubmit"
                >
                    <span v-if="loading">Saving...</span>
                    <span v-else>{{ isEditing ? 'Update Property' : 'Add Property' }}</span>
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import Modal from './Modal.vue';
import FormField from './FormField.vue';
import { propertiesApi } from '../../api';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    property: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'saved']);

const loading = ref(false);
const errors = reactive({});
const propertyTypes = ref([]);

// Photo upload state
const photos = ref([]);
const coverPhotoIndex = ref(0);
const isDragging = ref(false);

const form = reactive({
    title: '',
    listing_type: 'sale',
    property_type_id: '',
    address: '',
    locality: '',
    city: '',
    pincode: '',
    bhk: '',
    carpet_area: '',
    built_up_area: '',
    floor: '',
    total_floors: '',
    furnishing: 'unfurnished',
    price: '',
    price_negotiable: false,
    owner_name: '',
    owner_phone: '',
    description: '',
});

const isEditing = computed(() => !!props.property?.id);

const listingTypeOptions = [
    { value: 'sale', label: 'For Sale' },
    { value: 'rent', label: 'For Rent' },
];

const bhkOptions = [
    { value: '1 BHK', label: '1 BHK' },
    { value: '2 BHK', label: '2 BHK' },
    { value: '3 BHK', label: '3 BHK' },
    { value: '4 BHK', label: '4 BHK' },
    { value: '5+ BHK', label: '5+ BHK' },
];

const furnishingOptions = [
    { value: 'unfurnished', label: 'Unfurnished' },
    { value: 'semi_furnished', label: 'Semi-Furnished' },
    { value: 'fully_furnished', label: 'Fully Furnished' },
];

const resetForm = () => {
    Object.assign(form, {
        title: '',
        listing_type: 'sale',
        property_type_id: '',
        address: '',
        locality: '',
        city: '',
        pincode: '',
        bhk: '',
        carpet_area: '',
        built_up_area: '',
        floor: '',
        total_floors: '',
        furnishing: 'unfurnished',
        price: '',
        price_negotiable: false,
        owner_name: '',
        owner_phone: '',
        description: '',
    });
    Object.keys(errors).forEach(key => delete errors[key]);
    // Reset photos
    photos.value = [];
    coverPhotoIndex.value = 0;
};

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    
    if (!form.title.trim()) errors.title = 'Title is required';
    if (!form.locality.trim()) errors.locality = 'Locality is required';
    if (!form.city.trim()) errors.city = 'City is required';
    if (!form.price) errors.price = 'Price is required';
    
    return Object.keys(errors).length === 0;
};

// Photo handling functions
const handleFileSelect = (event) => {
    const files = event.target.files;
    if (files) {
        addPhotos(Array.from(files));
    }
};

const handleDrop = (event) => {
    isDragging.value = false;
    const files = event.dataTransfer.files;
    if (files) {
        addPhotos(Array.from(files));
    }
};

const addPhotos = (files) => {
    const validFiles = files.filter(file => {
        const isImage = file.type.startsWith('image/');
        const isValidSize = file.size <= 5 * 1024 * 1024; // 5MB
        return isImage && isValidSize;
    });
    
    validFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            photos.value.push({
                file,
                preview: e.target.result
            });
        };
        reader.readAsDataURL(file);
    });
};

const removePhoto = (index) => {
    photos.value.splice(index, 1);
    // Adjust cover index if needed
    if (coverPhotoIndex.value >= photos.value.length) {
        coverPhotoIndex.value = Math.max(0, photos.value.length - 1);
    }
    if (coverPhotoIndex.value === index && photos.value.length > 0) {
        coverPhotoIndex.value = 0;
    }
};

const setCoverPhoto = (index) => {
    coverPhotoIndex.value = index;
};

const uploadPhotos = async (propertyId) => {
    if (photos.value.length === 0) return;
    
    const formData = new FormData();
    photos.value.forEach((photo, index) => {
        formData.append('images[]', photo.file);
        if (index === coverPhotoIndex.value) {
            formData.append('primary_index', index.toString());
        }
    });
    
    await propertiesApi.uploadImages(propertyId, formData);
};

const handleSubmit = async () => {
    if (!validateForm()) return;

    loading.value = true;
    try {
        const payload = {
            ...form,
            carpet_area: form.carpet_area ? parseInt(form.carpet_area) : null,
            built_up_area: form.built_up_area ? parseInt(form.built_up_area) : null,
            floor: form.floor ? parseInt(form.floor) : null,
            total_floors: form.total_floors ? parseInt(form.total_floors) : null,
            price: parseInt(form.price),
        };

        let propertyId;
        
        if (isEditing.value) {
            await propertiesApi.update(props.property.id, payload);
            propertyId = props.property.id;
        } else {
            const response = await propertiesApi.create(payload);
            propertyId = response.data.data.id;
        }
        
        // Upload photos if any
        if (photos.value.length > 0) {
            await uploadPhotos(propertyId);
        }

        emit('saved');
        emit('update:modelValue', false);
        resetForm();
    } catch (error) {
        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors);
        }
        console.error('Failed to save property:', error);
    } finally {
        loading.value = false;
    }
};

// Populate form when editing
watch(() => props.property, (property) => {
    if (property) {
        Object.assign(form, {
            title: property.title || '',
            listing_type: property.listing_type || 'sale',
            property_type_id: property.property_type_id || '',
            address: property.address || '',
            locality: property.locality || '',
            city: property.city || '',
            pincode: property.pincode || '',
            bhk: property.bhk || '',
            carpet_area: property.carpet_area || '',
            built_up_area: property.built_up_area || '',
            floor: property.floor || '',
            total_floors: property.total_floors || '',
            furnishing: property.furnishing || 'unfurnished',
            price: property.price || '',
            price_negotiable: property.price_negotiable || false,
            owner_name: property.owner_name || '',
            owner_phone: property.owner_phone || '',
            description: property.description || '',
        });
    }
}, { immediate: true });

// Fetch property types on mount
onMounted(async () => {
    try {
        const response = await propertiesApi.getTypes();
        propertyTypes.value = response.data.data.map(type => ({
            value: type.id,
            label: type.name
        }));
    } catch (error) {
        console.error('Failed to fetch property types:', error);
        // Fallback property types
        propertyTypes.value = [
            { value: 'apartment', label: 'Apartment' },
            { value: 'villa', label: 'Villa' },
            { value: 'house', label: 'Independent House' },
            { value: 'plot', label: 'Plot / Land' },
            { value: 'commercial', label: 'Commercial' },
            { value: 'office', label: 'Office Space' },
            { value: 'shop', label: 'Shop / Showroom' },
        ];
    }
});
</script>
