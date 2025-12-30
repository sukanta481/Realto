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
                    <router-link to="/leads" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                        View all →
                    </router-link>
                </div>
                <div class="card-body space-y-4">
                    <div 
                        v-for="(stage, index) in leadFunnel" 
                        :key="stage.id"
                        class="flex items-center gap-4"
                        :style="{ animationDelay: (index * 0.05) + 's' }"
                    >
                        <div 
                            class="w-3 h-3 rounded-full ring-4 ring-opacity-20"
                            :style="{ backgroundColor: stage.color, '--tw-ring-color': stage.color }"
                        ></div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-slate-700">{{ stage.name }}</span>
                                <span class="text-sm font-semibold text-slate-900">{{ stage.count }}</span>
                            </div>
                            <div class="progress-bar">
                                <div 
                                    class="progress-bar-fill"
                                    :style="{ 
                                        width: getProgressWidth(stage.count) + '%',
                                        background: `linear-gradient(90deg, ${stage.color} 0%, ${stage.color}cc 100%)`
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div v-if="leadFunnel.length === 0" class="text-center py-8 text-slate-400">
                        <p>No lead data yet</p>
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
import { useAuthStore } from '../stores/auth';
import { dashboardApi } from '../api';

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
