<template>
    <Modal
        :model-value="modelValue"
        :title="isEditing ? 'Edit Follow-up' : 'Schedule Follow-up'"
        size="md"
        @update:model-value="$emit('update:modelValue', $event)"
        @close="resetForm"
    >
        <form @submit.prevent="handleSubmit" class="space-y-5">
            <!-- Purpose -->
            <FormField
                v-model="form.purpose"
                label="Purpose"
                placeholder="e.g., Show property, Price discussion"
                :error="errors.purpose"
                required
            />

            <!-- Type -->
            <FormField
                v-model="form.type"
                type="select"
                label="Type"
                :options="typeOptions"
                :error="errors.type"
            />

            <div class="grid grid-cols-2 gap-4">
                <!-- Date -->
                <FormField
                    v-model="form.scheduled_date"
                    type="date"
                    label="Date"
                    :error="errors.scheduled_date"
                    required
                />

                <!-- Time -->
                <FormField
                    v-model="form.scheduled_time"
                    type="time"
                    label="Time"
                    :error="errors.scheduled_time"
                />
            </div>

            <!-- Priority -->
            <FormField
                v-model="form.priority"
                type="select"
                label="Priority"
                :options="priorityOptions"
                :error="errors.priority"
            />

            <!-- Notes -->
            <FormField
                v-model="form.notes"
                type="textarea"
                label="Notes"
                placeholder="Add any notes or reminders..."
                :rows="3"
            />

            <!-- Reminder Checkbox -->
            <label class="flex items-center gap-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    v-model="form.reminder"
                    class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                />
                <span class="text-sm text-slate-700">Send reminder notification</span>
            </label>
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
                    <span v-else>{{ isEditing ? 'Update' : 'Schedule' }}</span>
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue';
import Modal from './Modal.vue';
import FormField from './FormField.vue';
import { followUpsApi } from '../../api';

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    followUp: {
        type: Object,
        default: null,
    },
    entityType: {
        type: String,
        default: 'lead', // 'lead' or 'client'
    },
    entityId: {
        type: [Number, String],
        default: null,
    },
});

const emit = defineEmits(['update:modelValue', 'saved']);

const loading = ref(false);
const errors = reactive({});

const form = reactive({
    purpose: '',
    type: 'call',
    scheduled_date: '',
    scheduled_time: '10:00',
    priority: 'medium',
    notes: '',
    reminder: true,
});

const isEditing = computed(() => !!props.followUp?.id);

const typeOptions = [
    { value: 'call', label: 'Phone Call' },
    { value: 'meeting', label: 'Meeting' },
    { value: 'site_visit', label: 'Site Visit' },
    { value: 'video_call', label: 'Video Call' },
    { value: 'whatsapp', label: 'WhatsApp' },
    { value: 'email', label: 'Email' },
    { value: 'other', label: 'Other' },
];

const priorityOptions = [
    { value: 'high', label: 'High - Urgent' },
    { value: 'medium', label: 'Medium - Normal' },
    { value: 'low', label: 'Low - Can wait' },
];

const resetForm = () => {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    Object.assign(form, {
        purpose: '',
        type: 'call',
        scheduled_date: tomorrow.toISOString().split('T')[0],
        scheduled_time: '10:00',
        priority: 'medium',
        notes: '',
        reminder: true,
    });
    Object.keys(errors).forEach(key => delete errors[key]);
};

const validateForm = () => {
    Object.keys(errors).forEach(key => delete errors[key]);
    
    if (!form.purpose.trim()) errors.purpose = 'Purpose is required';
    if (!form.scheduled_date) errors.scheduled_date = 'Date is required';
    
    return Object.keys(errors).length === 0;
};

const handleSubmit = async () => {
    if (!validateForm()) return;

    loading.value = true;
    try {
        const scheduledAt = form.scheduled_time 
            ? `${form.scheduled_date} ${form.scheduled_time}:00`
            : `${form.scheduled_date} 10:00:00`;

        const payload = {
            purpose: form.purpose,
            type: form.type,
            scheduled_at: scheduledAt,
            priority: form.priority,
            notes: form.notes,
            followable_type: props.entityType === 'client' ? 'App\\Models\\Client' : 'App\\Models\\Lead',
            followable_id: props.entityId,
        };

        if (isEditing.value) {
            await followUpsApi.update(props.followUp.id, payload);
        } else {
            await followUpsApi.create(payload);
        }

        emit('saved');
        emit('update:modelValue', false);
        resetForm();
    } catch (error) {
        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors);
        }
        console.error('Failed to save follow-up:', error);
    } finally {
        loading.value = false;
    }
};

// Set default date on open
watch(() => props.modelValue, (isOpen) => {
    if (isOpen && !props.followUp) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        form.scheduled_date = tomorrow.toISOString().split('T')[0];
    }
});

// Populate form when editing
watch(() => props.followUp, (followUp) => {
    if (followUp) {
        const date = new Date(followUp.scheduled_at);
        Object.assign(form, {
            purpose: followUp.purpose || '',
            type: followUp.type || 'call',
            scheduled_date: date.toISOString().split('T')[0],
            scheduled_time: date.toTimeString().slice(0, 5),
            priority: followUp.priority || 'medium',
            notes: followUp.notes || '',
            reminder: true,
        });
    }
}, { immediate: true });
</script>
