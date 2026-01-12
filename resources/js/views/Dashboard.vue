<template>
    <div class="space-y-8 pb-20 lg:pb-0 animate-fade-in">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
                <p class="text-slate-500 mt-1">Welcome back, <span class="text-indigo-600 font-medium">{{ authStore.userName }}</span></p>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ currentDate }}
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 stagger-children">
            <div class="kpi-card group">
                <div class="kpi-icon blue text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="kpi-value">{{ stats.leads_today }}</p>
                    <p class="kpi-label">New Leads Today</p>
                </div>
            </div>

            <div class="kpi-card group">
                <div class="kpi-icon orange text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <div>
                    <p class="kpi-value">{{ stats.follow_ups_today }}</p>
                    <p class="kpi-label">Follow-ups Today</p>
                </div>
            </div>

            <div class="kpi-card group">
                <div class="kpi-icon green text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="kpi-value">{{ stats.active_properties }}</p>
                    <p class="kpi-label">Active Properties</p>
                </div>
            </div>

            <div class="kpi-card group">
                <div class="kpi-icon pink text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="kpi-value">{{ stats.deals_this_month }}</p>
                    <p class="kpi-label">Deals This Month</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Lead Funnel -->
            <div class="lg:col-span-2 card">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-lg text-slate-900">Lead Funnel</h3>
                        <p class="text-sm text-slate-500 mt-0.5">Conversion pipeline overview</p>
                    </div>
                    <router-link to="/app/leads" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        View all →
                    </router-link>
                </div>
                <div class="card-body">
                    <!-- Visual Funnel Chart -->
                    <div v-if="leadFunnel.length > 0" class="py-4">
                        <!-- Funnel Stages -->
                        <div class="relative flex flex-col items-center">
                            <div 
                                v-for="(stage, index) in leadFunnel" 
                                :key="stage.id"
                                class="relative group"
                                :style="{ animationDelay: (index * 0.1) + 's' }"
                            >
                                <!-- Funnel Bar -->
                                <div 
                                    @click="navigateToLeads(stage)"
                                    class="relative h-14 flex items-center justify-center transition-all duration-300 hover:scale-105 cursor-pointer"
                                    :style="{ 
                                        width: getFunnelWidth(index) + '%',
                                        minWidth: '180px',
                                        background: `linear-gradient(135deg, ${stage.color} 0%, ${adjustColor(stage.color, -15)} 100%)`,
                                        clipPath: index === leadFunnel.length - 1 
                                            ? 'polygon(5% 0%, 95% 0%, 90% 100%, 10% 100%)' 
                                            : 'polygon(0% 0%, 100% 0%, 95% 100%, 5% 100%)',
                                        borderRadius: index === 0 ? '12px 12px 0 0' : '0',
                                        marginTop: index > 0 ? '-4px' : '0'
                                    }"
                                >
                                    <!-- Stage Content -->
                                    <div class="flex items-center justify-between w-full px-6 text-white">
                                        <span class="font-medium text-sm truncate">{{ stage.name }}</span>
                                        <span class="font-bold text-lg ml-2">{{ stage.count }}</span>
                                    </div>
                                    
                                    <!-- Tooltip on hover -->
                                    <div class="absolute left-full ml-4 top-1/2 -translate-y-1/2 bg-slate-800 text-white px-3 py-2 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-10 pointer-events-none">
                                        <div class="font-semibold">{{ stage.name }}</div>
                                        <div class="text-slate-300">{{ stage.count }} leads</div>
                                        <div v-if="index > 0 && leadFunnel[index-1].count > 0" class="text-xs mt-1" :class="getConversionColor(index)">
                                            {{ getConversionRate(index) }}% conversion from previous
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Drop-off indicator between stages -->
                                <div 
                                    v-if="index < leadFunnel.length - 1 && leadFunnel[index].count > 0"
                                    class="absolute -right-24 top-1/2 -translate-y-1/2 hidden lg:flex items-center gap-2"
                                >
                                    <div class="w-6 h-px bg-slate-300"></div>
                                    <div 
                                        class="text-xs font-semibold px-2 py-1 rounded-full"
                                        :class="getDropOffClass(index)"
                                    >
                                        {{ getDropOffPercent(index) }}% drop
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Funnel Summary Stats -->
                        <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t border-slate-100">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-slate-900">{{ totalLeads }}</p>
                                <p class="text-xs text-slate-500 mt-1">Total Leads</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold" :class="overallConversionColor">{{ overallConversionRate }}%</p>
                                <p class="text-xs text-slate-500 mt-1">Overall Conversion</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-indigo-600">{{ biggestDropStage }}</p>
                                <p class="text-xs text-slate-500 mt-1">Needs Attention</p>
                            </div>
                        </div>
                    </div>
                    
                    <div v-else class="text-center py-12 text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4h18M4 4v16l8-4 8 4V4"></path>
                        </svg>
                        <p>No lead data yet</p>
                        <p class="text-sm mt-1">Add your first lead to see the funnel</p>
                    </div>
                </div>
            </div>

            <!-- Today's Tasks -->
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-lg text-slate-900">Today's Tasks</h3>
                        <p class="text-sm text-slate-500 mt-0.5">{{ todaysTasks.length }} pending</p>
                    </div>
                    <span v-if="overdueCount > 0" class="badge-danger">
                        {{ overdueCount }} Overdue
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    <div 
                        v-for="task in todaysTasks.slice(0, 5)" 
                        :key="task.id"
                        class="px-6 py-4 hover:bg-slate-50/80 cursor-pointer transition-colors"
                    >
                        <div class="flex items-start gap-3">
                            <div 
                                class="w-2.5 h-2.5 mt-1.5 rounded-full ring-4 ring-opacity-20"
                                :style="{ backgroundColor: task.priority_color, '--tw-ring-color': task.priority_color }"
                            ></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-900 truncate">{{ task.purpose }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ task.entity_name }}</p>
                            </div>
                            <span class="text-xs text-slate-400 whitespace-nowrap">{{ formatTime(task.scheduled_at) }}</span>
                        </div>
                    </div>
                    <div v-if="todaysTasks.length === 0" class="px-6 py-12 text-center">
                        <div class="text-4xl mb-3">🎉</div>
                        <p class="text-slate-500 text-sm">No tasks for today!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-lg text-slate-900">Recent Activity</h3>
                <p class="text-sm text-slate-500 mt-0.5">Latest updates across your team</p>
            </div>
            <div class="divide-y divide-slate-100">
                <div 
                    v-for="activity in recentActivity.slice(0, 5)" 
                    :key="activity.id"
                    class="px-6 py-4 flex items-center gap-4"
                >
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-lg">
                        {{ activity.icon || '📝' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-900">{{ activity.description }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ activity.user }} • {{ activity.time }}</p>
                    </div>
                </div>
                <div v-if="recentActivity.length === 0" class="px-6 py-12 text-center text-slate-400 text-sm">
                    No recent activity
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { dashboardApi } from '../api';

const router = useRouter();
const authStore = useAuthStore();

const stats = ref({
    leads_today: 0,
    follow_ups_today: 0,
    active_properties: 0,
    deals_this_month: 0,
});

const leadFunnel = ref([]);
const todaysTasks = ref([]);
const recentActivity = ref([]);
const overdueCount = ref(0);
const loading = ref(true);

const currentDate = computed(() => {
    return new Date().toLocaleDateString('en-IN', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
});

const maxLeadCount = computed(() => {
    return Math.max(...leadFunnel.value.map(s => s.count), 1);
});

// Calculate total leads (first stage count)
const totalLeads = computed(() => {
    return leadFunnel.value.length > 0 ? leadFunnel.value[0].count : 0;
});

// Calculate overall conversion rate (last stage / first stage)
const overallConversionRate = computed(() => {
    if (leadFunnel.value.length < 2 || leadFunnel.value[0].count === 0) return 0;
    const lastStage = leadFunnel.value[leadFunnel.value.length - 1];
    return Math.round((lastStage.count / leadFunnel.value[0].count) * 100);
});

const overallConversionColor = computed(() => {
    const rate = overallConversionRate.value;
    if (rate >= 20) return 'text-emerald-600';
    if (rate >= 10) return 'text-amber-600';
    return 'text-rose-600';
});

// Find stage with biggest drop-off
const biggestDropStage = computed(() => {
    if (leadFunnel.value.length < 2) return '-';
    let maxDrop = 0;
    let stageName = '-';
    for (let i = 1; i < leadFunnel.value.length; i++) {
        const prev = leadFunnel.value[i - 1].count;
        const current = leadFunnel.value[i].count;
        if (prev > 0) {
            const drop = ((prev - current) / prev) * 100;
            if (drop > maxDrop) {
                maxDrop = drop;
                stageName = leadFunnel.value[i].name;
            }
        }
    }
    return stageName;
});

// Get funnel width percentage for each stage (creates pyramid effect)
const getFunnelWidth = (index) => {
    const total = leadFunnel.value.length;
    // Start at 100% and decrease by ~12% each stage
    return Math.max(100 - (index * 12), 40);
};

// Adjust hex color brightness
const adjustColor = (hex, percent) => {
    if (!hex) return '#6366f1';
    const num = parseInt(hex.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = Math.max(0, Math.min(255, (num >> 16) + amt));
    const G = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amt));
    const B = Math.max(0, Math.min(255, (num & 0x0000FF) + amt));
    return '#' + (0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1);
};

// Get conversion rate from previous stage
const getConversionRate = (index) => {
    if (index === 0 || leadFunnel.value[index - 1].count === 0) return 0;
    return Math.round((leadFunnel.value[index].count / leadFunnel.value[index - 1].count) * 100);
};

const getConversionColor = (index) => {
    const rate = getConversionRate(index);
    if (rate >= 50) return 'text-emerald-400';
    if (rate >= 25) return 'text-amber-400';
    return 'text-rose-400';
};

// Navigate to leads page with status filter
const navigateToLeads = (stage) => {
    router.push({
        path: '/app/leads',
        query: { status_id: stage.id }
    });
};

// Get drop-off percentage between stages
const getDropOffPercent = (index) => {
    if (leadFunnel.value[index].count === 0) return 0;
    const current = leadFunnel.value[index].count;
    const next = leadFunnel.value[index + 1]?.count || 0;
    return Math.round(((current - next) / current) * 100);
};

const getDropOffClass = (index) => {
    const drop = getDropOffPercent(index);
    if (drop >= 70) return 'bg-rose-100 text-rose-700';
    if (drop >= 50) return 'bg-amber-100 text-amber-700';
    return 'bg-emerald-100 text-emerald-700';
};

const getProgressWidth = (count) => {
    return (count / maxLeadCount.value) * 100;
};

const formatTime = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleTimeString('en-IN', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const fetchDashboardData = async () => {
    loading.value = true;
    try {
        const response = await dashboardApi.getStats();
        const data = response.data.data;

        stats.value = data.stats || stats.value;
        leadFunnel.value = data.lead_funnel || [];
        todaysTasks.value = data.todays_tasks || [];
        recentActivity.value = data.recent_activity || [];
        overdueCount.value = data.overdue_count || 0;
    } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>
