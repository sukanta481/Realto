<template>
    <div class="space-y-6 pb-20 lg:pb-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Deals</h1>
                <p class="text-gray-500">Track your transactions</p>
            </div>
            <button @click="openAddModal" class="btn-primary">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Deal
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ stats.total_deals }}</p>
                <p class="text-sm text-gray-500">Total Deals</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-primary-600">{{ stats.open_deals }}</p>
                <p class="text-sm text-gray-500">Open Deals</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-success-600">{{ stats.won_this_month }}</p>
                <p class="text-sm text-gray-500">Won This Month</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-2xl font-bold text-success-600">{{ formatCurrency(stats.revenue_this_month) }}</p>
                <p class="text-sm text-gray-500">Commission This Month</p>
            </div>
        </div>

        <!-- Pipeline View with Drag & Drop -->
        <div class="overflow-x-auto pb-4">
            <div class="flex gap-4" style="min-width: max-content;">
                <div 
                    v-for="stage in pipeline" 
                    :key="stage.stage"
                    class="w-72 flex-shrink-0"
                    @dragover.prevent="handleDragOver($event, stage.stage)"
                    @drop="handleDrop($event, stage.stage)"
                    @dragleave="handleDragLeave"
                >
                    <div 
                        class="bg-gray-100 rounded-xl p-3 min-h-[200px] transition-all duration-200"
                        :class="{ 'bg-indigo-100 ring-2 ring-indigo-400': dragOverStage === stage.stage }"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div 
                                    class="w-3 h-3 rounded-full"
                                    :style="{ backgroundColor: stage.color }"
                                ></div>
                                <span class="font-medium text-gray-900">{{ stage.name }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500">{{ stage.count }}</span>
                                <p class="text-xs text-gray-400">{{ formatCurrency(stage.total_value) }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div 
                                v-for="deal in stage.deals" 
                                :key="deal.id"
                                class="card p-3 cursor-grab hover:shadow-md transition-all duration-200"
                                :class="{ 'opacity-50 scale-95': draggedDeal?.id === deal.id }"
                                draggable="true"
                                @dragstart="handleDragStart($event, deal, stage.stage)"
                                @dragend="handleDragEnd"
                                @click="openEditModal(deal)"
                            >
                                <div class="flex items-start justify-between">
                                    <p class="font-medium text-gray-900 mb-1 flex-1">{{ deal.title }}</p>
                                    <button 
                                        @click.stop="openEditModal(deal)"
                                        class="text-gray-400 hover:text-indigo-600 p-1 -m-1"
                                        title="Edit deal"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-lg font-bold text-primary-600 mb-2">{{ deal.value }}</p>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">{{ deal.property || 'No property' }}</span>
                                    <span class="text-gray-400">{{ deal.expected_close || '' }}</span>
                                </div>
                                <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                    <span>👤 {{ deal.buyer || 'No buyer' }}</span>
                                </div>
                            </div>
                            <div v-if="stage.deals.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                <p>No deals</p>
                                <p class="text-xs mt-1">Drag deals here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deals Table -->
        <div class="card overflow-hidden">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">All Deals</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr 
                            v-for="deal in deals" 
                            :key="deal.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ deal.title }}</p>
                                <p class="text-sm text-gray-500">{{ deal.property?.title || 'No property' }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ formatCurrency(deal.deal_value) }}
                            </td>
                            <td class="px-6 py-4 text-success-600 font-medium">
                                {{ formatCurrency(deal.commission_amount) }}
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    class="badge"
                                    :style="{ backgroundColor: getStageColor(deal.stage) + '20', color: getStageColor(deal.stage) }"
                                >
                                    {{ deal.stage }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    class="badge"
                                    :class="{
                                        'badge-success': deal.payment_status === 'received',
                                        'badge-warning': deal.payment_status === 'partial',
                                        'badge-danger': deal.payment_status === 'pending',
                                    }"
                                >
                                    {{ deal.payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button 
                                    @click="openEditModal(deal)"
                                    class="text-indigo-600 hover:text-indigo-800 font-medium text-sm"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Deal Form Modal -->
    <DealFormModal 
        v-model="showModal"
        :deal="selectedDeal"
        @saved="handleDealSaved"
    />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { dealsApi } from '../../api';
import DealFormModal from '../../components/common/DealFormModal.vue';

const deals = ref([]);
const pipeline = ref([]);
const stats = ref({
    total_deals: 0,
    open_deals: 0,
    won_this_month: 0,
    revenue_this_month: 0,
});
const loading = ref(true);
const showModal = ref(false);
const selectedDeal = ref(null);

// Drag and drop state
const draggedDeal = ref(null);
const dragSourceStage = ref(null);
const dragOverStage = ref(null);

const formatCurrency = (amount) => {
    if (!amount) return '₹0';
    if (amount >= 10000000) return '₹' + (amount / 10000000).toFixed(2) + ' Cr';
    if (amount >= 100000) return '₹' + (amount / 100000).toFixed(2) + ' L';
    return '₹' + amount.toLocaleString();
};

const getStageColor = (stage) => {
    const colors = {
        open: '#3B82F6',
        negotiation: '#8B5CF6',
        agreement: '#F59E0B',
        documentation: '#EC4899',
        closed_won: '#10B981',
        closed_lost: '#EF4444',
    };
    return colors[stage] || '#6B7280';
};

const fetchDeals = async () => {
    loading.value = true;
    try {
        const [dealsRes, pipelineRes, statsRes] = await Promise.all([
            dealsApi.getAll(),
            dealsApi.getPipeline(),
            dealsApi.getStats(),
        ]);
        deals.value = dealsRes.data.data;
        pipeline.value = pipelineRes.data.data;
        stats.value = statsRes.data.data;
    } catch (error) {
        console.error('Failed to fetch deals:', error);
    } finally {
        loading.value = false;
    }
};

// Modal handlers
const openAddModal = () => {
    selectedDeal.value = null;
    showModal.value = true;
};

const openEditModal = (deal) => {
    selectedDeal.value = deal;
    showModal.value = true;
};

const handleDealSaved = () => {
    fetchDeals();
    showModal.value = false;
    selectedDeal.value = null;
};

// Drag and drop handlers
const handleDragStart = (event, deal, stage) => {
    draggedDeal.value = deal;
    dragSourceStage.value = stage;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', deal.id);
};

const handleDragEnd = () => {
    draggedDeal.value = null;
    dragSourceStage.value = null;
    dragOverStage.value = null;
};

const handleDragOver = (event, stage) => {
    event.preventDefault();
    if (stage !== dragSourceStage.value) {
        dragOverStage.value = stage;
    }
};

const handleDragLeave = () => {
    dragOverStage.value = null;
};

const handleDrop = async (event, targetStage) => {
    event.preventDefault();
    dragOverStage.value = null;
    
    if (!draggedDeal.value || targetStage === dragSourceStage.value) {
        return;
    }

    const dealId = draggedDeal.value.id;
    const oldStage = dragSourceStage.value;

    // Optimistic update - move deal in UI immediately
    const sourceStageData = pipeline.value.find(s => s.stage === oldStage);
    const targetStageData = pipeline.value.find(s => s.stage === targetStage);
    
    if (sourceStageData && targetStageData) {
        const dealIndex = sourceStageData.deals.findIndex(d => d.id === dealId);
        if (dealIndex > -1) {
            const [movedDeal] = sourceStageData.deals.splice(dealIndex, 1);
            targetStageData.deals.push(movedDeal);
            sourceStageData.count--;
            targetStageData.count++;
        }
    }

    try {
        await dealsApi.updateStage(dealId, targetStage);
        // Refresh to get accurate data
        fetchDeals();
    } catch (error) {
        console.error('Failed to update deal stage:', error);
        // Revert on error
        fetchDeals();
    }
};

onMounted(fetchDeals);
</script>
