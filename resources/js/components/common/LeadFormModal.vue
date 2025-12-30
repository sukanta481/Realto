<template>
    <Modal
        :model-value="modelValue"
        :title="isEditing ? 'Edit Lead' : 'Add New Lead'"
        size="lg"
        @update:model-value="$emit('update:modelValue', $event)"
        @close="resetForm"
    >
        <form @submit.prevent="handleSubmit" class="space-y-5">
            <div class="grid sm:grid-cols-2 gap-5">
                <!-- Name -->
                <FormField
                    v-model="form.name"
                    label="Full Name"
                    placeholder="John Doe"
                    :error="errors.name"
                    required
                />

                <!-- Phone -->
                <FormField
                    v-model="form.phone"
                    type="tel"
                    label="Phone Number"
                    placeholder="9876543210"
                    :error="errors.phone"
                    required
                />

                <!-- Email -->
                <FormField
                    v-model="form.email"
                    type="email"
                    label="Email"
                    placeholder="john@example.com"
                    :error="errors.email"
                />

                <!-- Source -->
                <FormField
                    v-model="form.source"
                    type="select"
                    label="Lead Source"
                    placeholder="Select source"
                    :options="sourceOptions"
                    :error="errors.source"
                />
            </div>

            <hr class="border-slate-100" />

            <h4 class="font-medium text-slate-900">Requirements</h4>

            <div class="grid sm:grid-cols-2 gap-5">
                <!-- Budget Min -->
                <FormField
                    v-model="form.budget_min"
                    type="number"
                    label="Minimum Budget (₹)"
                    placeholder="5000000"
                    :error="errors.budget_min"
                />

                <!-- Budget Max -->
                <FormField
                    v-model="form.budget_max"
                    type="number"
                    label="Maximum Budget (₹)"
                    placeholder="10000000"
                    :error="errors.budget_max"
                />

                <!-- Location -->
                <FormField
                    v-model="form.location_preference"
                    label="Preferred Location"
                    placeholder="e.g., Bandra, Andheri"
                    :error="errors.location_preference"
                />

                <!-- Property Type -->
                <FormField
                    v-model="form.property_type"
                    type="select"
                    label="Property Type"
                    placeholder="Select type"
                    :options="propertyTypeOptions"
                    :error="errors.property_type"
                />

                <!-- BHK -->
                <FormField
                    v-model="form.bhk"
                    type="select"
                    label="BHK"
                    placeholder="Select BHK"
                    :options="bhkOptions"
                    :error="errors.bhk"
                />

                <!-- Priority -->
                <FormField
                    v-model="form.priority"
                    type="select"
                    label="Priority"
                    :options="priorityOptions"
                    :error="errors.priority"
                />
            </div>

            <!-- Notes -->
            <FormField
                v-model="form.notes"
                type="textarea"
                label="Notes"
                placeholder="Any additional information..."
                :rows="3"
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
                    <span v-else>{{ isEditing ? 'Update Lead' : 'Add Lead' }}</span>
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import Modal from './Modal.vue';
import FormField from './FormField.vue';
import { leadsApi } from '../../api';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    lead: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'saved']);

const loading = ref(false);
const errors = reactive({});

const form = reactive({
    name: '',
    phone: '',
    email: '',
    source: 'website',
    budget_min: '',
    budget_max: '',
    location_preference: '',
    property_type: '',
    bhk: '',
    priority: '2',
    notes: '',
});

const isEditing = computed(() => !!props.lead?.id);

const sourceOptions = [
    { value: 'website', label: 'Website' },
    { value: '99acres', label: '99acres' },
    { value: 'magicbricks', label: 'MagicBricks' },
    { value: 'housing', label: 'Housing.com' },
    { value: 'referral', label: 'Referral' },
    { value: 'walk_in', label: 'Walk-in' },
    { value: 'facebook', label: 'Facebook' },
    { value: 'instagram', label: 'Instagram' },
    { value: 'other', label: 'Other' },
];

const propertyTypeOptions = [
    { value: 'Apartment', label: 'Apartment' },
    { value: 'Villa', label: 'Villa' },
    { value: 'Plot', label: 'Plot' },
    { value: 'Commercial', label: 'Commercial' },
    { value: 'Office Space', label: 'Office Space' },
    { value: 'Shop', label: 'Shop' },
];

const bhkOptions = [
    { value: '1 BHK', label: '1 BHK' },
    { value: '2 BHK', label: '2 BHK' },
    { value: '3 BHK', label: '3 BHK' },
    { value: '4 BHK', label: '4 BHK' },
    { value: '5+ BHK', label: '5+ BHK' },
];

const priorityOptions = [
    { value: '1', label: 'High' },
    { value: '2', label: 'Medium' },
    { value: '3', label: 'Low' },
];

const resetForm = () => {
    Object.assign(form, {
        name: '',
        phone: '',
        email: '',
        source: 'website',
        budget_min: '',
        budget_max: '',
        location_preference: '',
        property_type: '',
        bhk: '',
        priority: '2',
        notes: '',
    });
    Object.keys(errors).forEach(key => delete errors[key]);
};

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    
    if (!form.name.trim()) {
        errors.name = 'Name is required';
    }
    if (!form.phone.trim()) {
        errors.phone = 'Phone is required';
    } else if (!/^[0-9]{10}$/.test(form.phone.replace(/\D/g, ''))) {
        errors.phone = 'Enter a valid 10-digit phone number';
    }
    if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Enter a valid email address';
    }
    
    return Object.keys(errors).length === 0;
};

const handleSubmit = async () => {
    if (!validateForm()) return;

    loading.value = true;
    try {
        const payload = {
            ...form,
            budget_min: form.budget_min ? parseInt(form.budget_min) : null,
            budget_max: form.budget_max ? parseInt(form.budget_max) : null,
            priority: parseInt(form.priority),
        };

        if (isEditing.value) {
            await leadsApi.update(props.lead.id, payload);
        } else {
            await leadsApi.create(payload);
        }

        emit('saved');
        emit('update:modelValue', false);
        resetForm();
    } catch (error) {
        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors);
        }
        console.error('Failed to save lead:', error);
    } finally {
        loading.value = false;
    }
};

// Populate form when editing
watch(() => props.lead, (lead) => {
    if (lead) {
        Object.assign(form, {
            name: lead.name || '',
            phone: lead.phone || '',
            email: lead.email || '',
            source: lead.source || 'website',
            budget_min: lead.budget_min || '',
            budget_max: lead.budget_max || '',
            location_preference: lead.location_preference || '',
            property_type: lead.property_type || '',
            bhk: lead.bhk || '',
            priority: String(lead.priority || 2),
            notes: lead.notes || '',
        });
    }
}, { immediate: true });
</script>
