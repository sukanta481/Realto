<template>
    <div class="space-y-6 pb-20 lg:pb-0">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Analytics & Reports</h1>
                <p class="text-gray-500">Track performance and generate reports</p>
            </div>
            <div class="flex items-center gap-3">
                <select v-model="dateRange" class="form-select" @change="loadAllData">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
                <div class="relative">
                    <button @click="showExportMenu = !showExportMenu" class="btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    <div v-if="showExportMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-10">
                        <button @click="exportReport('leads', 'csv')" class="w-full px-4 py-2 text-left text-sm hover:bg-slate-50">Leads (CSV)</button>
                        <button @click="exportReport('leads', 'pdf')" class="w-full px-4 py-2 text-left text-sm hover:bg-slate-50">Leads (PDF)</button>
                        <button @click="exportReport('properties', 'csv')" class="w-full px-4 py-2 text-left text-sm hover:bg-slate-50">Properties (CSV)</button>
                        <button @click="exportReport('deals', 'csv')" class="w-full px-4 py-2 text-left text-sm hover:bg-slate-50">Deals (CSV)</button>
                        <button @click="exportReport('monthly-summary', 'pdf')" class="w-full px-4 py-2 text-left text-sm hover:bg-slate-50">Monthly Summary (PDF)</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-slate-200">
            <nav class="flex gap-6 -mb-px">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="pb-3 px-1 text-sm font-medium border-b-2 transition-colors"
                    :class="activeTab === tab.id 
                        ? 'text-primary-600 border-primary-600' 
                        : 'text-slate-500 border-transparent hover:text-slate-700'"
                >
                    {{ tab.name }}
                </button>
            </nav>
        </div>

        <!-- Lead Analytics -->
        <div v-if="activeTab === 'leads'" class="space-y-6">
            <!-- Lead Funnel -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Lead Conversion Funnel</h3>
                <div class="flex items-end justify-between h-48 gap-4">
                    <div 
                        v-for="(stage, index) in leadAnalytics.funnel" 
                        :key="stage.name"
                        class="flex-1 flex flex-col items-center"
                    >
                        <span class="text-2xl font-bold text-slate-900 mb-2">{{ stage.value }}</span>
                        <div 
                            class="w-full rounded-t-lg transition-all duration-500"
                            :style="{ 
                                height: getBarHeight(stage.value, leadAnalytics.funnel[0]?.value || 1) + '%',
                                backgroundColor: funnelColors[index]
                            }"
                        ></div>
                        <span class="text-xs text-slate-500 mt-2 text-center">{{ stage.name }}</span>
                    </div>
                </div>
            </div>

            <!-- Lead Sources & Status -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Lead Sources</h3>
                    <div class="space-y-3">
                        <div v-for="(count, source) in leadAnalytics.sources" :key="source" class="flex items-center justify-between">
                            <span class="text-slate-600 capitalize">{{ source || 'Unknown' }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-32 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-primary-500 rounded-full"
                                        :style="{ width: getPercentage(count, getTotalSources()) + '%' }"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium text-slate-900 w-8 text-right">{{ count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Status Distribution</h3>
                    <div class="space-y-3">
                        <div v-for="status in leadAnalytics.statuses" :key="status.name" class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: status.color }"></div>
                                <span class="text-slate-600">{{ status.name }}</span>
                            </div>
                            <span class="text-sm font-medium text-slate-900">{{ status.count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Analytics -->
        <div v-if="activeTab === 'revenue'" class="space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card p-4">
                    <p class="text-sm text-slate-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-emerald-600">₹{{ formatNumber(revenueAnalytics.totals?.revenue) }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-sm text-slate-500">Total Deals</p>
                    <p class="text-2xl font-bold text-slate-900">{{ revenueAnalytics.totals?.deals || 0 }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-sm text-slate-500">Avg Deal Value</p>
                    <p class="text-2xl font-bold text-slate-900">₹{{ formatNumber(getAvgDealValue()) }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-sm text-slate-500">Win Rate</p>
                    <p class="text-2xl font-bold text-primary-600">{{ getWinRate() }}%</p>
                </div>
            </div>

            <!-- Revenue Trend -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Revenue Trend</h3>
                <div class="h-64 flex items-end gap-4">
                    <div 
                        v-for="(value, month) in revenueAnalytics.revenue_trend" 
                        :key="month"
                        class="flex-1 flex flex-col items-center"
                    >
                        <span class="text-xs text-slate-500 mb-1">₹{{ formatNumber(value) }}</span>
                        <div 
                            class="w-full bg-gradient-to-t from-primary-500 to-primary-400 rounded-t-lg transition-all duration-500"
                            :style="{ height: getBarHeight(value, getMaxRevenue()) + '%', minHeight: '4px' }"
                        ></div>
                        <span class="text-xs text-slate-500 mt-2">{{ month }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Top Performers</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-slate-500 border-b border-slate-100">
                                <th class="pb-3 font-medium">Name</th>
                                <th class="pb-3 font-medium">Deals Closed</th>
                                <th class="pb-3 font-medium">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="performer in revenueAnalytics.top_performers" :key="performer.name">
                                <td class="py-3 font-medium text-slate-900">{{ performer.name }}</td>
                                <td class="py-3 text-slate-600">{{ performer.deals }}</td>
                                <td class="py-3 text-emerald-600 font-medium">₹{{ formatNumber(performer.revenue) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Property Analytics -->
        <div v-if="activeTab === 'properties'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Status Distribution</h3>
                    <div class="space-y-3">
                        <div v-for="(count, status) in propertyAnalytics.status_distribution" :key="status" class="flex items-center justify-between">
                            <span class="text-slate-600 capitalize">{{ status }}</span>
                            <span class="text-sm font-medium text-slate-900">{{ count }}</span>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Price Range Distribution</h3>
                    <div class="space-y-3">
                        <div v-for="(count, range) in propertyAnalytics.price_ranges" :key="range" class="flex items-center justify-between">
                            <span class="text-slate-600">{{ range }}</span>
                            <div class="flex items-center gap-2">
                                <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-emerald-500 rounded-full"
                                        :style="{ width: getPercentage(count, getTotalProperties()) + '%' }"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium text-slate-900 w-8 text-right">{{ count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Properties by City</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div v-for="(count, city) in propertyAnalytics.by_city" :key="city" class="text-center p-4 bg-slate-50 rounded-xl">
                        <p class="text-2xl font-bold text-slate-900">{{ count }}</p>
                        <p class="text-sm text-slate-500">{{ city }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        <div v-if="activeTab === 'team'" class="card overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-semibold text-slate-900">Team Performance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-sm text-slate-500">
                            <th class="px-6 py-3 font-medium">Member</th>
                            <th class="px-6 py-3 font-medium">Role</th>
                            <th class="px-6 py-3 font-medium">Leads</th>
                            <th class="px-6 py-3 font-medium">Follow-ups</th>
                            <th class="px-6 py-3 font-medium">Completed</th>
                            <th class="px-6 py-3 font-medium">Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="member in teamPerformance" :key="member.id" class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ member.name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ member.role }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ member.leads }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ member.followups }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ member.completed }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full rounded-full"
                                            :class="member.completion_rate >= 70 ? 'bg-emerald-500' : member.completion_rate >= 40 ? 'bg-amber-500' : 'bg-red-500'"
                                            :style="{ width: member.completion_rate + '%' }"
                                        ></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ member.completion_rate }}%</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../api';

const activeTab = ref('leads');
const dateRange = ref('30');
const showExportMenu = ref(false);
const loading = ref(true);

const tabs = [
    { id: 'leads', name: 'Lead Analytics' },
    { id: 'revenue', name: 'Revenue' },
    { id: 'properties', name: 'Properties' },
    { id: 'team', name: 'Team Performance' },
];

const funnelColors = ['#4f46e5', '#6366f1', '#818cf8', '#a5b4fc'];

const leadAnalytics = ref({ funnel: [], sources: {}, statuses: [] });
const revenueAnalytics = ref({ revenue_trend: {}, totals: {}, top_performers: [], deal_stages: {} });
const propertyAnalytics = ref({ status_distribution: {}, price_ranges: {}, by_city: {} });
const teamPerformance = ref([]);

const loadAllData = async () => {
    loading.value = true;
    try {
        const [leads, revenue, properties, team] = await Promise.all([
            api.get('/reports/lead-analytics', { params: { days: dateRange.value } }),
            api.get('/reports/revenue-analytics', { params: { months: 6 } }),
            api.get('/reports/property-analytics'),
            api.get('/reports/team-performance', { params: { days: dateRange.value } }),
        ]);

        leadAnalytics.value = leads.data.data;
        revenueAnalytics.value = revenue.data.data;
        propertyAnalytics.value = properties.data.data;
        teamPerformance.value = team.data.data;
    } catch (error) {
        console.error('Failed to load analytics:', error);
    } finally {
        loading.value = false;
    }
};

const exportReport = async (type, format) => {
    showExportMenu.value = false;
    try {
        const response = await api.get(`/export/${type}`, {
            params: { format },
            responseType: 'blob',
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${type}_${new Date().toISOString().split('T')[0]}.${format === 'pdf' ? 'pdf' : 'csv'}`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Export failed:', error);
    }
};

// Helper functions
const getBarHeight = (value, max) => max > 0 ? Math.max((value / max) * 100, 5) : 5;
const getPercentage = (value, total) => total > 0 ? (value / total) * 100 : 0;
const getTotalSources = () => Object.values(leadAnalytics.value.sources || {}).reduce((a, b) => a + b, 0);
const getTotalProperties = () => Object.values(propertyAnalytics.value.price_ranges || {}).reduce((a, b) => a + b, 0);
const getMaxRevenue = () => Math.max(...Object.values(revenueAnalytics.value.revenue_trend || { 0: 1 }));
const getAvgDealValue = () => {
    const { revenue, deals } = revenueAnalytics.value.totals || {};
    return deals > 0 ? revenue / deals : 0;
};
const getWinRate = () => {
    const stages = revenueAnalytics.value.deal_stages || {};
    const total = Object.values(stages).reduce((a, b) => a + b, 0);
    const won = stages['closed_won'] || 0;
    return total > 0 ? Math.round((won / total) * 100) : 0;
};
const formatNumber = (num) => {
    if (!num) return '0';
    if (num >= 10000000) return (num / 10000000).toFixed(1) + ' Cr';
    if (num >= 100000) return (num / 100000).toFixed(1) + ' L';
    return num.toLocaleString('en-IN');
};

onMounted(loadAllData);
</script>
