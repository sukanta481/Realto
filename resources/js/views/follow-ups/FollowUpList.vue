<template>
    <div class="space-y-8 pb-20 lg:pb-0 animate-fade-in">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Follow-ups</h1>
                <p class="text-slate-500 mt-1">Manage your tasks and reminders</p>
            </div>
            <div class="flex items-center gap-3">
                <button 
                    @click="viewMode = 'list'"
                    class="p-2 rounded-lg"
                    :class="viewMode === 'list' ? 'bg-indigo-100 text-indigo-600' : 'text-slate-500 hover:bg-slate-100'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
                <button 
                    @click="viewMode = 'calendar'"
                    class="p-2 rounded-lg"
                    :class="viewMode === 'calendar' ? 'bg-indigo-100 text-indigo-600' : 'text-slate-500 hover:bg-slate-100'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 stagger-children">
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform cursor-pointer" @click="activeFilter = 'today'">
                <p class="text-3xl font-bold text-indigo-600">{{ stats.today }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Today</p>
            </div>
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform cursor-pointer" @click="activeFilter = 'overdue'">
                <p class="text-3xl font-bold text-rose-600">{{ stats.overdue }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Overdue</p>
            </div>
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform cursor-pointer" @click="activeFilter = 'upcoming'">
                <p class="text-3xl font-bold text-emerald-600">{{ stats.upcoming }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Upcoming</p>
            </div>
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform cursor-pointer" @click="activeFilter = 'completed'">
                <p class="text-3xl font-bold text-slate-600">{{ stats.completed }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Completed</p>
            </div>
        </div>

        <!-- List View -->
        <div v-if="viewMode === 'list'" class="card">
            <div class="card-header flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button 
                        v-for="filter in filterOptions" 
                        :key="filter.value"
                        @click="activeFilter = filter.value"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                        :class="activeFilter === filter.value 
                            ? 'bg-indigo-100 text-indigo-600' 
                            : 'text-slate-500 hover:bg-slate-100'"
                    >
                        {{ filter.label }}
                    </button>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                <div 
                    v-for="followUp in filteredFollowUps" 
                    :key="followUp.id"
                    class="p-5 hover:bg-slate-50/50 transition-colors group"
                >
                    <div class="flex items-start gap-4">
                        <!-- Status Checkbox -->
                        <button 
                            @click="toggleComplete(followUp)"
                            class="mt-0.5 flex-shrink-0"
                        >
                            <div 
                                class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-colors"
                                :class="followUp.status === 'completed' 
                                    ? 'bg-emerald-500 border-emerald-500 text-white' 
                                    : 'border-slate-300 hover:border-indigo-400'"
                            >
                                <svg v-if="followUp.status === 'completed'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </button>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p 
                                        class="font-medium text-slate-900"
                                        :class="{ 'line-through text-slate-400': followUp.status === 'completed' }"
                                    >
                                        {{ followUp.purpose }}
                                    </p>
                                    <p class="text-sm text-slate-500 mt-0.5">
                                        {{ followUp.followable?.name || 'Unknown' }} • {{ followUp.type }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p 
                                        class="text-sm font-medium"
                                        :class="getDateClass(followUp)"
                                    >
                                        {{ formatDate(followUp.scheduled_at) }}
                                    </p>
                                    <p class="text-xs text-slate-400">{{ formatTime(followUp.scheduled_at) }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button 
                                    v-if="followUp.status !== 'completed'"
                                    @click="toggleComplete(followUp)"
                                    class="btn-sm btn-success"
                                >
                                    Mark Complete
                                </button>
                                <button 
                                    v-if="followUp.status !== 'completed'"
                                    class="btn-sm btn-secondary"
                                >
                                    Reschedule
                                </button>
                                <a 
                                    v-if="followUp.followable?.phone"
                                    :href="`tel:${followUp.followable.phone}`"
                                    class="btn-sm btn-outline"
                                >
                                    Call
                                </a>
                            </div>
                        </div>

                        <!-- Priority Indicator -->
                        <div 
                            class="w-2 h-2 rounded-full flex-shrink-0 mt-2"
                            :class="{
                                'bg-rose-500': followUp.priority === 'high',
                                'bg-amber-500': followUp.priority === 'medium',
                                'bg-emerald-500': followUp.priority === 'low',
                            }"
                        ></div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredFollowUps.length === 0" class="p-12 text-center">
                    <div class="empty-state-icon mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-1">No follow-ups</h3>
                    <p class="text-slate-500">{{ getEmptyMessage() }}</p>
                </div>
            </div>
        </div>

        <!-- Calendar View -->
        <div v-if="viewMode === 'calendar'" class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <button @click="prevMonth" class="p-2 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h3 class="text-lg font-semibold text-slate-900">
                    {{ currentMonthName }} {{ currentYear }}
                </h3>
                <button @click="nextMonth" class="p-2 hover:bg-slate-100 rounded-lg">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1">
                <!-- Day Headers -->
                <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="p-2 text-center text-xs font-semibold text-slate-500 uppercase">
                    {{ day }}
                </div>

                <!-- Calendar Days -->
                <div 
                    v-for="(day, index) in calendarDays" 
                    :key="index"
                    class="min-h-[80px] p-1 border border-slate-100 rounded-lg"
                    :class="{ 
                        'bg-slate-50/50': !day.isCurrentMonth,
                        'bg-indigo-50 border-indigo-200': day.isToday,
                    }"
                >
                    <p 
                        class="text-sm font-medium mb-1"
                        :class="day.isCurrentMonth ? 'text-slate-900' : 'text-slate-400'"
                    >
                        {{ day.date }}
                    </p>
                    <div class="space-y-0.5">
                        <div 
                            v-for="event in day.events.slice(0, 2)" 
                            :key="event.id"
                            class="text-xs px-1.5 py-0.5 rounded truncate cursor-pointer"
                            :class="{
                                'bg-rose-100 text-rose-700': event.priority === 'high',
                                'bg-amber-100 text-amber-700': event.priority === 'medium',
                                'bg-emerald-100 text-emerald-700': event.priority === 'low',
                            }"
                            :title="event.purpose"
                        >
                            {{ event.purpose }}
                        </div>
                        <p v-if="day.events.length > 2" class="text-xs text-slate-400 px-1">
                            +{{ day.events.length - 2 }} more
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { followUpsApi } from '../../api';

const viewMode = ref('list');
const activeFilter = ref('today');
const followUps = ref([]);
const loading = ref(true);

const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());

const stats = ref({
    today: 0,
    overdue: 0,
    upcoming: 0,
    completed: 0,
});

const filterOptions = [
    { value: 'today', label: 'Today' },
    { value: 'overdue', label: 'Overdue' },
    { value: 'upcoming', label: 'Upcoming' },
    { value: 'completed', label: 'Completed' },
    { value: 'all', label: 'All' },
];

const filteredFollowUps = computed(() => {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    
    return followUps.value.filter(f => {
        const scheduled = new Date(f.scheduled_at);
        const scheduledDate = new Date(scheduled.getFullYear(), scheduled.getMonth(), scheduled.getDate());
        
        switch (activeFilter.value) {
            case 'today':
                return scheduledDate.getTime() === today.getTime() && f.status !== 'completed';
            case 'overdue':
                return scheduledDate < today && f.status !== 'completed';
            case 'upcoming':
                return scheduledDate > today && f.status !== 'completed';
            case 'completed':
                return f.status === 'completed';
            default:
                return true;
        }
    });
});

const currentMonthName = computed(() => {
    return new Date(currentYear.value, currentMonth.value).toLocaleDateString('en-US', { month: 'long' });
});

const calendarDays = computed(() => {
    const days = [];
    const firstDay = new Date(currentYear.value, currentMonth.value, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value + 1, 0);
    const today = new Date();
    
    // Add days from previous month
    for (let i = 0; i < firstDay.getDay(); i++) {
        const date = new Date(currentYear.value, currentMonth.value, -firstDay.getDay() + i + 1);
        days.push({
            date: date.getDate(),
            isCurrentMonth: false,
            isToday: false,
            events: getEventsForDate(date),
        });
    }
    
    // Add days of current month
    for (let i = 1; i <= lastDay.getDate(); i++) {
        const date = new Date(currentYear.value, currentMonth.value, i);
        days.push({
            date: i,
            isCurrentMonth: true,
            isToday: date.toDateString() === today.toDateString(),
            events: getEventsForDate(date),
        });
    }
    
    // Add days from next month
    const remainingDays = 42 - days.length;
    for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(currentYear.value, currentMonth.value + 1, i);
        days.push({
            date: i,
            isCurrentMonth: false,
            isToday: false,
            events: getEventsForDate(date),
        });
    }
    
    return days;
});

const getEventsForDate = (date) => {
    return followUps.value.filter(f => {
        const scheduled = new Date(f.scheduled_at);
        return scheduled.toDateString() === date.toDateString();
    });
};

const getDateClass = (followUp) => {
    const now = new Date();
    const scheduled = new Date(followUp.scheduled_at);
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const scheduledDate = new Date(scheduled.getFullYear(), scheduled.getMonth(), scheduled.getDate());
    
    if (followUp.status === 'completed') return 'text-slate-400';
    if (scheduledDate < today) return 'text-rose-600';
    if (scheduledDate.getTime() === today.getTime()) return 'text-indigo-600';
    return 'text-slate-600';
};

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    if (date.toDateString() === today.toDateString()) return 'Today';
    if (date.toDateString() === tomorrow.toDateString()) return 'Tomorrow';
    
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
};

const formatTime = (dateStr) => {
    return new Date(dateStr).toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
};

const getEmptyMessage = () => {
    const messages = {
        today: 'No tasks scheduled for today',
        overdue: 'No overdue tasks. Great job!',
        upcoming: 'No upcoming tasks scheduled',
        completed: 'No completed tasks yet',
        all: 'No follow-ups found',
    };
    return messages[activeFilter.value];
};

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const toggleComplete = async (followUp) => {
    try {
        if (followUp.status === 'completed') {
            // Reopen task
            followUp.status = 'pending';
        } else {
            await followUpsApi.complete(followUp.id);
            followUp.status = 'completed';
        }
        updateStats();
    } catch (error) {
        console.error('Failed to update follow-up:', error);
    }
};

const updateStats = () => {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    
    stats.value = {
        today: followUps.value.filter(f => {
            const d = new Date(f.scheduled_at);
            return new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime() === today.getTime() && f.status !== 'completed';
        }).length,
        overdue: followUps.value.filter(f => {
            const d = new Date(f.scheduled_at);
            return new Date(d.getFullYear(), d.getMonth(), d.getDate()) < today && f.status !== 'completed';
        }).length,
        upcoming: followUps.value.filter(f => {
            const d = new Date(f.scheduled_at);
            return new Date(d.getFullYear(), d.getMonth(), d.getDate()) > today && f.status !== 'completed';
        }).length,
        completed: followUps.value.filter(f => f.status === 'completed').length,
    };
};

const fetchFollowUps = async () => {
    loading.value = true;
    try {
        const response = await followUpsApi.getAll();
        followUps.value = response.data.data || [];
        updateStats();
    } catch (error) {
        console.error('Failed to fetch follow-ups:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchFollowUps);
</script>
