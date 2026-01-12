<template>
    <div class="space-y-6 pb-20 lg:pb-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Leads</h1>
                <p class="text-gray-500">Manage your potential customers</p>
            </div>
            <div class="flex items-center gap-3">
                <button 
                    @click="viewMode = 'table'"
                    class="p-2 rounded-lg"
                    :class="viewMode === 'table' ? 'bg-primary-100 text-primary-600' : 'text-gray-500 hover:bg-gray-100'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
                <button 
                    @click="viewMode = 'kanban'"
                    class="p-2 rounded-lg"
                    :class="viewMode === 'kanban' ? 'bg-primary-100 text-primary-600' : 'text-gray-500 hover:bg-gray-100'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                    </svg>
                </button>
                <button @click="showAddModal = true" class="btn-primary">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Lead
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card p-4">
            <div class="flex flex-wrap gap-3">
                <input 
                    v-model="filters.search"
                    type="text"
                    placeholder="Search by name, phone..."
                    class="form-input w-full sm:w-64"
                />
                <select v-model="filters.status_id" class="form-select w-full sm:w-auto">
                    <option value="">All Statuses</option>
                    <option v-for="status in statuses" :key="status.id" :value="status.id">
                        {{ status.name }}
                    </option>
                </select>
                <select v-model="filters.priority" class="form-select w-full sm:w-auto">
                    <option value="">All Priorities</option>
                    <option value="1">High</option>
                    <option value="2">Medium</option>
                    <option value="3">Low</option>
                </select>
            </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'" class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr 
                            v-for="lead in leads" 
                            :key="lead.id"
                            class="hover:bg-gray-50 cursor-pointer"
                            @click="$router.push(`/app/leads/${lead.id}`)"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                        <span class="text-primary-600 font-medium text-sm">
                                            {{ lead.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ lead.name }}</p>
                                        <p class="text-xs text-gray-500">{{ lead.property_type || 'Any type' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a :href="`tel:${lead.phone}`" class="text-primary-600 hover:underline" @click.stop>
                                    {{ lead.phone }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ lead.budget_range || 'Not specified' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ lead.location_preference || 'Any' }}
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    class="badge"
                                    :style="{ backgroundColor: lead.status?.color + '20', color: lead.status?.color }"
                                >
                                    {{ lead.status?.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2" @click.stop>
                                    <a 
                                        :href="`tel:${lead.phone}`"
                                        class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg"
                                        title="Call"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </a>
                                    <a 
                                        :href="getWhatsAppLink(lead.phone, lead.name)"
                                        target="_blank"
                                        class="p-2 text-green-500 hover:bg-green-50 rounded-lg"
                                        title="WhatsApp"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Showing {{ leads.length }} of {{ pagination.total }} leads
                </p>
                <div class="flex gap-2">
                    <button 
                        @click="prevPage"
                        :disabled="pagination.current_page === 1"
                        class="btn-secondary btn-sm"
                    >
                        Previous
                    </button>
                    <button 
                        @click="nextPage"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="btn-secondary btn-sm"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Kanban View -->
        <div v-if="viewMode === 'kanban'" class="overflow-x-auto pb-4">
            <div class="flex gap-4" style="min-width: max-content;">
                <div 
                    v-for="column in kanbanData" 
                    :key="column.id"
                    class="w-80 flex-shrink-0"
                    @dragover.prevent
                    @drop="handleDrop($event, column.id)"
                >
                    <div class="bg-slate-100 rounded-xl p-4">
                        <!-- Column Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div 
                                    class="w-3 h-3 rounded-full"
                                    :style="{ backgroundColor: column.color }"
                                ></div>
                                <span class="font-semibold text-slate-900">{{ column.name }}</span>
                            </div>
                            <span class="text-sm font-medium text-slate-500 bg-white px-2 py-0.5 rounded-full">
                                {{ column.leads.length }}
                            </span>
                        </div>
                        
                        <!-- Lead Cards -->
                        <div class="space-y-3 min-h-[200px]">
                            <div 
                                v-for="lead in column.leads" 
                                :key="lead.id"
                                :draggable="true"
                                @dragstart="handleDragStart($event, lead, column.id)"
                                @dragend="handleDragEnd"
                                class="card p-4 cursor-grab active:cursor-grabbing hover:shadow-lg transition-all duration-200 border-l-4"
                                :style="{ borderLeftColor: column.color }"
                                :class="{ 'opacity-50 scale-95': draggedLead?.id === lead.id }"
                            >
                                <!-- Lead Info -->
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ lead.name }}</p>
                                        <p class="text-sm text-slate-500">{{ lead.phone }}</p>
                                    </div>
                                    <span 
                                        class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-1"
                                        :class="{
                                            'bg-rose-500': lead.priority === 1,
                                            'bg-amber-500': lead.priority === 2,
                                            'bg-emerald-500': lead.priority === 3,
                                        }"
                                        :title="lead.priority === 1 ? 'High' : lead.priority === 2 ? 'Medium' : 'Low'"
                                    ></span>
                                </div>
                                
                                <!-- Budget & Location -->
                                <div class="text-sm text-slate-600 mb-3">
                                    <p v-if="lead.budget_range">💰 {{ lead.budget_range }}</p>
                                    <p v-if="lead.location_preference" class="truncate">📍 {{ lead.location_preference }}</p>
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                    <div class="flex items-center gap-1">
                                        <!-- WhatsApp -->
                                        <a 
                                            :href="getWhatsAppLink(lead.phone, lead.name)"
                                            target="_blank"
                                            @click.stop
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="WhatsApp"
                                        >
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </a>
                                        <!-- Call -->
                                        <a 
                                            :href="`tel:${lead.phone}`"
                                            @click.stop
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Call"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <!-- View Details -->
                                    <button 
                                        @click.stop="$router.push(`/app/leads/${lead.id}`)"
                                        class="text-xs font-medium text-indigo-600 hover:text-indigo-700"
                                    >
                                        View →
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Empty State -->
                            <div 
                                v-if="column.leads.length === 0"
                                class="py-8 text-center text-slate-400"
                            >
                                <p class="text-sm">No leads in this stage</p>
                                <p class="text-xs mt-1">Drag leads here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && leads.length === 0 && viewMode === 'table'" class="card p-12 text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl">👥</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No leads yet</h3>
            <p class="text-gray-500 mb-4">Start by adding your first lead</p>
            <button @click="showAddModal = true" class="btn-primary">Add Lead</button>
        </div>
    </div>

    <!-- Lead Form Modal -->
    <LeadFormModal 
        v-model="showAddModal"
        @saved="handleLeadSaved"
    />
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { leadsApi } from '../../api';
import LeadFormModal from '../../components/common/LeadFormModal.vue';

const route = useRoute();

const viewMode = ref('table');
const leads = ref([]);
const kanbanData = ref([]);
const statuses = ref([]);
const loading = ref(true);
const showAddModal = ref(false);

const filters = reactive({
    search: '',
    status_id: '',
    priority: '',
});

const pagination = reactive({
    current_page: 1,
    last_page: 1,
    total: 0,
});

const fetchLeads = async () => {
    loading.value = true;
    try {
        const response = await leadsApi.getAll({
            ...filters,
            page: pagination.current_page,
        });
        leads.value = response.data.data;
        Object.assign(pagination, response.data.meta);
    } catch (error) {
        console.error('Failed to fetch leads:', error);
    } finally {
        loading.value = false;
    }
};

const fetchKanban = async () => {
    try {
        const response = await leadsApi.getKanban();
        kanbanData.value = response.data.data;
        // Extract statuses from kanban data
        statuses.value = kanbanData.value.map(col => ({ id: col.id, name: col.name }));
    } catch (error) {
        console.error('Failed to fetch kanban:', error);
    }
};

// Drag and drop state
const draggedLead = ref(null);
const dragSourceColumnId = ref(null);

const handleDragStart = (event, lead, columnId) => {
    draggedLead.value = lead;
    dragSourceColumnId.value = columnId;
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', lead.id);
};

const handleDragEnd = () => {
    draggedLead.value = null;
    dragSourceColumnId.value = null;
};

const handleDrop = async (event, newStatusId) => {
    event.preventDefault();
    if (!draggedLead.value || !dragSourceColumnId.value) return;
    
    const lead = { ...draggedLead.value };
    const oldColumnId = dragSourceColumnId.value;
    
    // Don't do anything if dropped in same column
    if (oldColumnId === newStatusId) {
        draggedLead.value = null;
        dragSourceColumnId.value = null;
        return;
    }
    
    // Optimistically update UI - create new array for reactivity
    const updatedKanban = kanbanData.value.map(col => {
        if (col.id === oldColumnId) {
            // Remove from old column
            return {
                ...col,
                leads: col.leads.filter(l => l.id !== lead.id)
            };
        }
        if (col.id === newStatusId) {
            // Add to new column (only if not already there)
            const alreadyExists = col.leads.some(l => l.id === lead.id);
            if (alreadyExists) return col;
            return {
                ...col,
                leads: [...col.leads, { ...lead, status_id: newStatusId }]
            };
        }
        return col;
    });
    
    // Update the reactive data
    kanbanData.value = updatedKanban;
    draggedLead.value = null;
    dragSourceColumnId.value = null;
    
    try {
        await leadsApi.updateStatus(lead.id, newStatusId);
    } catch (error) {
        console.error('Failed to update lead status:', error);
        // Revert on error by refetching
        fetchKanban();
    }
};

// WhatsApp with pre-filled message
const getWhatsAppLink = (phone, name = '') => {
    const cleanPhone = phone?.replace(/[^0-9]/g, '') || '';
    const formattedPhone = cleanPhone.length === 10 ? '91' + cleanPhone : cleanPhone;
    const message = name 
        ? encodeURIComponent(`Hi ${name}, I'm reaching out regarding your property inquiry. How can I help you today?`)
        : encodeURIComponent('Hi, I am reaching out regarding your property inquiry.');
    return `https://wa.me/${formattedPhone}?text=${message}`;
};

const prevPage = () => {
    if (pagination.current_page > 1) {
        pagination.current_page--;
        fetchLeads();
    }
};

const nextPage = () => {
    if (pagination.current_page < pagination.last_page) {
        pagination.current_page++;
        fetchLeads();
    }
};

// Watch for filter changes
let debounceTimer;
watch(filters, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        pagination.current_page = 1;
        fetchLeads();
    }, 300);
});

const handleLeadSaved = () => {
    fetchLeads();
    fetchKanban();
};

onMounted(() => {
    // Check for status filter from query params (e.g., from funnel click)
    if (route.query.status_id) {
        filters.status_id = route.query.status_id;
    }
    fetchLeads();
    fetchKanban();
});
</script>
