<template>
    <div class="space-y-6 pb-20 lg:pb-0">
        <!-- Back + Header -->
        <div class="flex items-start gap-4">
            <button @click="$router.back()" class="p-2 hover:bg-gray-100 rounded-lg mt-1">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ lead?.name }}</h1>
                <p class="text-gray-500">Lead Details</p>
            </div>
            <div class="flex items-center gap-2">
                <span 
                    v-if="lead?.status"
                    class="badge"
                    :style="{ backgroundColor: lead.status.color + '20', color: lead.status.color }"
                >
                    {{ lead.status.name }}
                </span>
            </div>
        </div>

        <!-- Quick Actions (Mobile) -->
        <div class="flex gap-3 lg:hidden">
            <a :href="`tel:${lead?.phone}`" class="btn-primary flex-1 touch-target justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                Call
            </a>
            <a :href="whatsappLink" target="_blank" class="btn-success flex-1 touch-target justify-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </a>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Info -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Contact Information</h3>
                    </div>
                    <div class="card-body grid sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <a :href="`tel:${lead?.phone}`" class="text-primary-600 font-medium">{{ lead?.phone }}</a>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ lead?.email || '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Source</p>
                            <p class="font-medium text-gray-900 capitalize">{{ lead?.source || 'Manual' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Created</p>
                            <p class="font-medium text-gray-900">{{ formatDate(lead?.created_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Requirements</h3>
                    </div>
                    <div class="card-body grid sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Budget</p>
                            <p class="font-medium text-gray-900">{{ budgetRange }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Property Type</p>
                            <p class="font-medium text-gray-900">{{ lead?.property_type || 'Any' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Location</p>
                            <p class="font-medium text-gray-900">{{ lead?.location_preference || 'Any' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Purpose</p>
                            <p class="font-medium text-gray-900 capitalize">{{ lead?.purpose || 'Buy' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">BHK</p>
                            <p class="font-medium text-gray-900">{{ lead?.bhk || 'Any' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Priority</p>
                            <span :class="priorityClass">{{ priorityLabel }}</span>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Notes</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-gray-600">{{ lead?.notes || 'No notes added' }}</p>
                    </div>
                </div>

                <!-- Follow-up History -->
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Follow-up History</h3>
                        <button class="btn-sm btn-outline">Add Follow-up</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="divide-y divide-gray-100">
                            <div 
                                v-for="followUp in lead?.follow_ups" 
                                :key="followUp.id"
                                class="p-4"
                            >
                                <div class="flex items-start gap-3">
                                    <div 
                                        class="w-2 h-2 mt-2 rounded-full"
                                        :class="{
                                            'bg-success-500': followUp.status === 'completed',
                                            'bg-warning-500': followUp.status === 'pending',
                                            'bg-gray-400': followUp.status === 'cancelled',
                                        }"
                                    ></div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ followUp.purpose }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ formatDateTime(followUp.scheduled_at) }}
                                            • {{ followUp.user?.name }}
                                        </p>
                                        <p v-if="followUp.outcome" class="text-sm text-gray-600 mt-1">
                                            {{ followUp.outcome }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!lead?.follow_ups?.length" class="p-4 text-center text-gray-500 text-sm">
                                No follow-ups yet
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions (Desktop) -->
                <div class="card hidden lg:block">
                    <div class="card-body space-y-3">
                        <a :href="`tel:${lead?.phone}`" class="btn-primary w-full justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Call
                        </a>
                        <a :href="whatsappLink" target="_blank" class="btn-success w-full justify-center">
                            WhatsApp
                        </a>
                        <button class="btn-outline w-full justify-center">Schedule Follow-up</button>
                        <button 
                            v-if="!lead?.converted_at"
                            class="btn-secondary w-full justify-center"
                        >
                            Convert to Client
                        </button>
                    </div>
                </div>

                <!-- Assigned To -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="font-semibold text-gray-900">Assigned To</h3>
                    </div>
                    <div class="card-body">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 font-medium">
                                    {{ lead?.assigned_to?.name?.charAt(0) || '?' }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ lead?.assigned_to?.name || 'Unassigned' }}</p>
                                <p class="text-sm text-gray-500">{{ lead?.assigned_to?.phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { leadsApi } from '../../api';

const route = useRoute();
const lead = ref(null);
const loading = ref(true);

const whatsappLink = computed(() => {
    if (!lead.value?.phone) return '#';
    const phone = lead.value.phone.replace(/[^0-9]/g, '');
    const formatted = phone.length === 10 ? '91' + phone : phone;
    return `https://wa.me/${formatted}`;
});

const budgetRange = computed(() => {
    if (!lead.value) return 'Not specified';
    const formatCurrency = (amount) => {
        if (!amount) return null;
        if (amount >= 10000000) return '₹' + (amount / 10000000).toFixed(2) + ' Cr';
        if (amount >= 100000) return '₹' + (amount / 100000).toFixed(2) + ' L';
        return '₹' + amount.toLocaleString();
    };
    const min = formatCurrency(lead.value.budget_min);
    const max = formatCurrency(lead.value.budget_max);
    if (min && max) return `${min} - ${max}`;
    if (max) return `Up to ${max}`;
    if (min) return `From ${min}`;
    return 'Not specified';
});

const priorityLabel = computed(() => {
    const priorities = { 1: 'High', 2: 'Medium', 3: 'Low' };
    return priorities[lead.value?.priority] || 'Medium';
});

const priorityClass = computed(() => {
    const classes = {
        1: 'badge-danger',
        2: 'badge-warning',
        3: 'badge-success',
    };
    return classes[lead.value?.priority] || 'badge-warning';
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-IN', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatDateTime = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleString('en-IN', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const fetchLead = async () => {
    loading.value = true;
    try {
        const response = await leadsApi.getOne(route.params.id);
        lead.value = response.data.data.lead;
    } catch (error) {
        console.error('Failed to fetch lead:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchLead);
</script>
