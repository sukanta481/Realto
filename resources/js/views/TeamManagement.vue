<template>
    <div class="space-y-8 pb-20 lg:pb-0 animate-fade-in">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Team Management</h1>
                <p class="text-slate-500 mt-1">Manage your team members and roles</p>
            </div>
            <button class="btn-primary" @click="showAddUserModal = true">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add User
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 stagger-children">
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-indigo-600">{{ stats.total }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Total Members</p>
            </div>
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-emerald-600">{{ stats.active }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Active</p>
            </div>
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-amber-600">{{ stats.agents }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Agents</p>
            </div>
            <div class="card p-5 text-center hover:scale-[1.02] transition-transform">
                <p class="text-3xl font-bold text-slate-600">{{ stats.managers }}</p>
                <p class="text-sm text-slate-500 font-medium mt-1">Managers</p>
            </div>
        </div>

        <!-- Team List -->
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-lg text-slate-900">Team Members</h3>
                <p class="text-sm text-slate-500 mt-0.5">All users in your organization</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar w-10 h-10 text-sm">
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ user.name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-slate-600">{{ user.email }}</td>
                            <td class="text-slate-600">{{ user.phone || '-' }}</td>
                            <td>
                                <span 
                                    class="badge"
                                    :class="{
                                        'badge-danger': user.role === 'admin',
                                        'badge-warning': user.role === 'manager',
                                        'badge-primary': user.role === 'agent',
                                    }"
                                >
                                    {{ user.role }}
                                </span>
                            </td>
                            <td>
                                <span 
                                    class="badge"
                                    :class="user.status === 'active' ? 'badge-success' : 'badge-danger'"
                                >
                                    {{ user.status }}
                                </span>
                            </td>
                            <td class="text-sm text-slate-500">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <button 
                                        @click="editUser(user)"
                                        class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg"
                                        title="Edit"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        v-if="user.id !== authStore.user?.id"
                                        @click="toggleStatus(user)"
                                        class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg"
                                        :title="user.status === 'active' ? 'Deactivate' : 'Activate'"
                                    >
                                        <svg v-if="user.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div v-if="!loading && users.length === 0" class="p-12 text-center">
                <div class="empty-state-icon mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 mb-1">No team members</h3>
                <p class="text-slate-500">Add team members to collaborate</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

const users = ref([]);
const loading = ref(true);
const showAddUserModal = ref(false);

const stats = computed(() => {
    return {
        total: users.value.length,
        active: users.value.filter(u => u.status === 'active').length,
        agents: users.value.filter(u => u.role === 'agent').length,
        managers: users.value.filter(u => u.role === 'manager').length,
    };
});

const getInitials = (name) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('en-IN', { 
        day: 'numeric', 
        month: 'short', 
        year: 'numeric' 
    });
};

const editUser = (user) => {
    // TODO: Implement edit functionality
    console.log('Edit user:', user);
};

const toggleStatus = async (user) => {
    // TODO: Implement status toggle
    const newStatus = user.status === 'active' ? 'inactive' : 'active';
    user.status = newStatus;
    console.log('Toggle status:', user);
};

const fetchUsers = async () => {
    loading.value = true;
    try {
        // Mock data for now - replace with actual API call
        users.value = [
            {
                id: 1,
                name: authStore.user?.name || 'Admin User',
                email: authStore.user?.email || 'admin@realto.com',
                phone: '9876543210',
                role: 'admin',
                status: 'active',
                created_at: new Date().toISOString(),
            },
        ];
    } catch (error) {
        console.error('Failed to fetch users:', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchUsers);
</script>
