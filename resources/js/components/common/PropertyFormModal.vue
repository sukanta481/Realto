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
};

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    
    if (!form.title.trim()) errors.title = 'Title is required';
    if (!form.locality.trim()) errors.locality = 'Locality is required';
    if (!form.city.trim()) errors.city = 'City is required';
    if (!form.price) errors.price = 'Price is required';
    
    return Object.keys(errors).length === 0;
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

        if (isEditing.value) {
            await propertiesApi.update(props.property.id, payload);
        } else {
            await propertiesApi.create(payload);
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
</script>
