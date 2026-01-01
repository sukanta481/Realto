<template>
    <Modal
        :model-value="modelValue"
        :title="isEditing ? 'Edit Deal' : 'Create New Deal'"
        size="lg"
        @update:model-value="$emit('update:modelValue', $event)"
        @close="resetForm"
    >
        <form @submit.prevent="handleSubmit" class="space-y-5">
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

            <!-- Client Selection -->
            <FormField
                v-model="form.client_id"
                type="select"
                label="Client"
                placeholder="Select client"
                :options="clientOptions"
                :error="errors.client_id"
                required
            />

            <hr class="border-slate-100" />

            <h4 class="font-medium text-slate-900">Deal Details</h4>

            <div class="grid sm:grid-cols-2 gap-5">
                <!-- Deal Amount -->
                <FormField
                    v-model="form.amount"
                    type="number"
                    label="Deal Amount (₹)"
                    placeholder="10000000"
                    :error="errors.amount"
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

                <!-- Deal Date -->
                <FormField
                    v-model="form.deal_date"
                    type="date"
                    label="Deal Date"
                    :error="errors.deal_date"
                    required
                />

                <!-- Expected Close Date -->
                <FormField
                    v-model="form.expected_close_date"
                    type="date"
                    label="Expected Close Date"
                    :error="errors.expected_close_date"
                />

                <!-- Status -->
                <FormField
                    v-model="form.status"
                    type="select"
                    label="Deal Status"
                    :options="statusOptions"
                    :error="errors.status"
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
import { dealsApi, propertiesApi, clientsApi } from '../../api';

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
    property_id: '',
    client_id: '',
    amount: '',
    commission_percentage: '2',
    deal_date: '',
    expected_close_date: '',
    status: 'pending',
    stage: 'negotiation',
    notes: '',
});

const isEditing = computed(() => !!props.deal?.id);

const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'won', label: 'Won' },
    { value: 'lost', label: 'Lost' },
];

const stageOptions = [
    { value: 'negotiation', label: 'Negotiation' },
    { value: 'agreement', label: 'Agreement' },
    { value: 'token_received', label: 'Token Received' },
    { value: 'documentation', label: 'Documentation' },
    { value: 'payment_pending', label: 'Payment Pending' },
    { value: 'completed', label: 'Completed' },
];

const calculatedCommission = computed(() => {
    if (form.amount && form.commission_percentage) {
        return (parseFloat(form.amount) * parseFloat(form.commission_percentage)) / 100;
    }
    return 0;
});

const resetForm = () => {
    Object.assign(form, {
        property_id: '',
        client_id: '',
        amount: '',
        commission_percentage: '2',
        deal_date: new Date().toISOString().split('T')[0],
        expected_close_date: '',
        status: 'pending',
        stage: 'negotiation',
        notes: '',
    });
    Object.keys(errors).forEach(key => delete errors[key]);
};

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    
    if (!form.property_id) errors.property_id = 'Property is required';
    if (!form.client_id) errors.client_id = 'Client is required';
    if (!form.amount) errors.amount = 'Amount is required';
    if (!form.deal_date) errors.deal_date = 'Deal date is required';
    
    return Object.keys(errors).length === 0;
};

const handleSubmit = async () => {
    if (!validateForm()) return;

    loading.value = true;
    try {
        const payload = {
            ...form,
            amount: parseFloat(form.amount),
            commission_percentage: parseFloat(form.commission_percentage),
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
        const response = await propertiesApi.getAll({ status: 'available' });
        propertyOptions.value = response.data.data.map(p => ({
            value: p.id,
            label: `${p.title} - ₹${(p.price / 100000).toFixed(1)}L`,
        }));
    } catch (error) {
        console.error('Failed to fetch properties:', error);
    }
};

const fetchClients = async () => {
    try {
        const response = await clientsApi.getAll();
        clientOptions.value = response.data.data.map(c => ({
            value: c.id,
            label: `${c.name} - ${c.phone}`,
        }));
    } catch (error) {
        console.error('Failed to fetch clients:', error);
    }
};

// Set default date on mount
onMounted(() => {
    form.deal_date = new Date().toISOString().split('T')[0];
    fetchProperties();
    fetchClients();
});

// Populate form when editing
watch(() => props.deal, (deal) => {
    if (deal) {
        Object.assign(form, {
            property_id: deal.property_id || '',
            client_id: deal.client_id || '',
            amount: deal.amount || '',
            commission_percentage: deal.commission_percentage || '2',
            deal_date: deal.deal_date?.split(' ')[0] || '',
            expected_close_date: deal.expected_close_date?.split(' ')[0] || '',
            status: deal.status || 'pending',
            stage: deal.stage || 'negotiation',
            notes: deal.notes || '',
        });
    }
}, { immediate: true });

// Fetch data when modal opens
watch(() => props.modelValue, (isOpen) => {
    if (isOpen) {
        fetchProperties();
        fetchClients();
    }
});
</script>
