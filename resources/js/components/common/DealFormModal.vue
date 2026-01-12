<template>
    <Modal
        :model-value="modelValue"
        :title="isEditing ? 'Edit Deal' : 'Create New Deal'"
        size="lg"
        @update:model-value="$emit('update:modelValue', $event)"
        @close="resetForm"
    >
        <form @submit.prevent="handleSubmit" class="space-y-5">
            <!-- Deal Title -->
            <FormField
                v-model="form.title"
                type="text"
                label="Deal Title"
                placeholder="e.g., 3BHK Flat Sale - Andheri West"
                :error="errors.title"
                required
            />

            <div class="grid sm:grid-cols-2 gap-5">
                <!-- Property Selection -->
                <FormField
                    v-model="form.property_id"
                    type="select"
                    label="Property"
                    placeholder="Select property"
                    :options="propertyOptions"
                    :error="errors.property_id"
                    required
                />

                <!-- Client/Buyer Selection -->
                <FormField
                    v-model="form.buyer_id"
                    type="select"
                    label="Buyer (Won Lead)"
                    placeholder="Select buyer"
                    :options="clientOptions"
                    :error="errors.buyer_id"
                    required
                />
            </div>

            <hr class="border-slate-100" />

            <h4 class="font-medium text-slate-900">Deal Details</h4>

            <div class="grid sm:grid-cols-2 gap-5">
                <!-- Deal Amount -->
                <FormField
                    v-model="form.deal_value"
                    type="number"
                    label="Deal Value (₹)"
                    placeholder="10000000"
                    :error="errors.deal_value"
                    required
                />

                <!-- Commission Percentage -->
                <FormField
                    v-model="form.commission_percentage"
                    type="number"
                    label="Commission (%)"
                    placeholder="2"
                    step="0.1"
                    :error="errors.commission_percentage"
                    hint="Enter commission percentage (e.g., 2 for 2%)"
                />

                <!-- Expected Close Date -->
                <FormField
                    v-model="form.expected_close_date"
                    type="date"
                    label="Expected Close Date"
                    :error="errors.expected_close_date"
                />

                <!-- Stage -->
                <FormField
                    v-model="form.stage"
                    type="select"
                    label="Deal Stage"
                    :options="stageOptions"
                    :error="errors.stage"
                />
            </div>

            <!-- Calculated Commission -->
            <div v-if="calculatedCommission" class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                <p class="text-sm text-emerald-700 font-medium">
                    Estimated Commission: <span class="text-lg">₹{{ calculatedCommission.toLocaleString('en-IN') }}</span>
                </p>
            </div>

            <!-- Notes -->
            <FormField
                v-model="form.notes"
                type="textarea"
                label="Notes"
                placeholder="Any additional information about the deal..."
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
                    <span v-else>{{ isEditing ? 'Update Deal' : 'Create Deal' }}</span>
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue';
import Modal from './Modal.vue';
import FormField from './FormField.vue';
import { dealsApi, propertiesApi, leadsApi, clientsApi } from '../../api';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    deal: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'saved']);

const loading = ref(false);
const errors = reactive({});
const propertyOptions = ref([]);
const clientOptions = ref([]);

const form = reactive({
    title: '',
    property_id: '',
    buyer_id: '',
    deal_value: '',
    commission_percentage: '2',
    expected_close_date: '',
    stage: 'open',
    notes: '',
});

const isEditing = computed(() => !!props.deal?.id);

const stageOptions = [
    { value: 'open', label: 'Open' },
    { value: 'negotiation', label: 'Negotiation' },
    { value: 'agreement', label: 'Agreement' },
    { value: 'documentation', label: 'Documentation' },
    { value: 'closed_won', label: 'Closed Won' },
    { value: 'closed_lost', label: 'Closed Lost' },
];

const calculatedCommission = computed(() => {
    if (form.deal_value && form.commission_percentage) {
        return (parseFloat(form.deal_value) * parseFloat(form.commission_percentage)) / 100;
    }
    return 0;
});

const resetForm = () => {
    Object.assign(form, {
        title: '',
        property_id: '',
        buyer_id: '',
        deal_value: '',
        commission_percentage: '2',
        expected_close_date: '',
        stage: 'open',
        notes: '',
    });
    Object.keys(errors).forEach(key => delete errors[key]);
};

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    
    if (!form.title) errors.title = 'Title is required';
    if (!form.property_id) errors.property_id = 'Property is required';
    if (!form.buyer_id) errors.buyer_id = 'Buyer is required';
    if (!form.deal_value) errors.deal_value = 'Deal value is required';
    
    return Object.keys(errors).length === 0;
};

// Get deal type from selected property
const getSelectedPropertyType = () => {
    const selectedProperty = propertyOptions.value.find(p => p.value == form.property_id);
    return selectedProperty?.listing_type || 'sale';
};

const handleSubmit = async () => {
    if (!validateForm()) return;

    loading.value = true;
    try {
        const payload = {
            title: form.title,
            property_id: form.property_id,
            buyer_id: form.buyer_id,
            type: getSelectedPropertyType(), // Get type from property
            deal_value: parseFloat(form.deal_value),
            commission_percentage: form.commission_percentage ? parseFloat(form.commission_percentage) : null,
            expected_close_date: form.expected_close_date || null,
            stage: form.stage,
            notes: form.notes || null,
        };

        if (isEditing.value) {
            await dealsApi.update(props.deal.id, payload);
        } else {
            await dealsApi.create(payload);
        }

        emit('saved');
        emit('update:modelValue', false);
        resetForm();
    } catch (error) {
        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors);
        }
        console.error('Failed to save deal:', error);
    } finally {
        loading.value = false;
    }
};

const fetchProperties = async () => {
    try {
        const response = await propertiesApi.getAll();
        propertyOptions.value = response.data.data.map(p => ({
            value: p.id,
            label: `${p.title} - ₹${(p.price / 100000).toFixed(1)}L`,
            listing_type: p.listing_type || 'sale',
        }));
    } catch (error) {
        console.error('Failed to fetch properties:', error);
    }
};

// Fetch clients/leads for buyer dropdown
const fetchClients = async () => {
    try {
        // First try to get clients from the clients table
        const clientsResponse = await clientsApi.getAll();
        if (clientsResponse.data.data && clientsResponse.data.data.length > 0) {
            clientOptions.value = clientsResponse.data.data.map(c => ({
                value: c.id,
                label: `${c.name} - ${c.phone || c.email || ''}`,
            }));
        } else {
            // Fallback: No clients yet, show all leads instead
            const leadsResponse = await leadsApi.getAll();
            clientOptions.value = leadsResponse.data.data.map(c => ({
                value: c.id,
                label: `${c.name} - ${c.phone}`,
            }));
        }
    } catch (error) {
        console.error('Failed to fetch clients:', error);
        // Fallback to leads on error
        try {
            const response = await leadsApi.getAll();
            clientOptions.value = response.data.data.map(c => ({
                value: c.id,
                label: `${c.name} - ${c.phone}`,
            }));
        } catch (e) {
            console.error('Failed to fetch leads:', e);
        }
    }
};

// Fetch data on mount
onMounted(() => {
    fetchProperties();
    fetchClients();
});

// Fetch data when modal opens
watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        fetchProperties();
        fetchClients();
    }
});

// Populate form when editing
watch(() => props.deal, (deal) => {
    if (deal) {
        Object.assign(form, {
            title: deal.title || '',
            property_id: deal.property_id || '',
            buyer_id: deal.buyer_id || '',
            deal_value: deal.deal_value || '',
            commission_percentage: deal.commission_percentage || '2',
            expected_close_date: deal.expected_close_date?.split('T')[0] || '',
            stage: deal.stage || 'open',
            notes: deal.notes || '',
        });
    }
}, { immediate: true });
</script>
