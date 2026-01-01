<template>
    <div class="space-y-8 pb-20 lg:pb-0 animate-fade-in">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Settings</h1>
            <p class="text-slate-500 mt-1">Manage your company and CRM configuration</p>
        </div>

        <!-- Settings Tabs -->
        <div class="card p-2">
            <div class="flex gap-2 overflow-x-auto scrollbar-hide">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.value"
                    @click="activeTab = tab.value"
                    class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-colors"
                    :class="activeTab === tab.value ? 'bg-indigo-100 text-indigo-600' : 'text-slate-500 hover:bg-slate-100'"
                >
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <!-- Company Settings -->
        <div v-if="activeTab === 'company'" class="card">
            <div class="card-header">
                <h3 class="font-semibold text-lg text-slate-900">Company Information</h3>
                <p class="text-sm text-slate-500 mt-0.5">Update your company details</p>
            </div>
            <div class="card-body">
                <form @submit.prevent="saveCompanySettings" class="space-y-5 max-w-2xl">
                    <FormField
                        v-model="companyForm.name"
                        label="Company Name"
                        placeholder="Your Company Name"
                        required
                    />
                    <div class="grid sm:grid-cols-2 gap-5">
                        <FormField
                            v-model="companyForm.email"
                            type="email"
                            label="Company Email"
                            placeholder="contact@company.com"
                        />
                        <FormField
                            v-model="companyForm.phone"
                            type="tel"
                            label="Company Phone"
                            placeholder="9876543210"
                        />
                    </div>
                    <FormField
                        v-model="companyForm.address"
                        type="textarea"
                        label="Address"
                        placeholder="Complete address..."
                        :rows="3"
                    />
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="submit" class="btn-primary">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lead Statuses -->
        <div v-if="activeTab === 'statuses'" class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg text-slate-900">Lead Statuses</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Customize your lead pipeline stages</p>
                </div>
                <button class="btn-primary btn-sm" @click="addStatus">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Status
                </button>
            </div>
            <div class="divide-y divide-slate-100">
                <div 
                    v-for="(status, index) in leadStatuses" 
                    :key="status.id"
                    class="p-5 flex items-center gap-4 group"
                >
                    <div class="flex items-center gap-3 flex-1">
                        <div 
                            class="w-4 h-4 rounded-full"
                            :style="{ backgroundColor: status.color }"
                        ></div>
                        <input 
                            v-model="status.name"
                            class="form-input flex-1 max-w-xs"
                            placeholder="Status name"
                        />
                        <input 
                            v-model="status.color"
                            type="color"
                            class="w-12 h-10 rounded-lg border border-slate-200 cursor-pointer"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            v-if="index > 0"
                            @click="moveStatus(index, -1)"
                            class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button 
                            v-if="index < leadStatuses.length - 1"
                            @click="moveStatus(index, 1)"
                            class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <button 
                            @click="removeStatus(index)"
                            class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body border-t border-slate-100">
                <button @click="saveStatuses" class="btn-primary">
                    Save Statuses
                </button>
            </div>
        </div>

        <!-- Property Types -->
        <div v-if="activeTab === 'types'" class="card">
            <div class="card-header flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg text-slate-900">Property Types</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Manage property categories</p>
                </div>
                <button class="btn-primary btn-sm" @click="addPropertyType">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Type
                </button>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 p-6">
                <div 
                    v-for="(type, index) in propertyTypes" 
                    :key="type.id"
                    class="card p-4 flex items-center justify-between group"
                >
                    <input 
                        v-model="type.name"
                        class="form-input"
                        placeholder="Type name"
                    />
                    <button 
                        @click="removePropertyType(index)"
                        class="ml-2 p-2 text-rose-500 hover:bg-rose-50 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="card-body border-t border-slate-100">
                <button @click="savePropertyTypes" class="btn-primary">
                    Save Property Types
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import FormField from '../components/common/FormField.vue';

const activeTab = ref('company');

const tabs = [
    { value: 'company', label: 'Company' },
    { value: 'statuses', label: 'Lead Statuses' },
    { value: 'types', label: 'Property Types' },
];

const companyForm = reactive({
    name: 'My Real Estate Company',
    email: 'contact@mycompany.com',
    phone: '9876543210',
    address: '',
});

const leadStatuses = ref([
    { id: 1, name: 'New', color: '#3b82f6', order: 1 },
    { id: 2, name: 'Contacted', color: '#10b981', order: 2 },
    { id: 3, name: 'Qualified', color: '#f59e0b', order: 3 },
    { id: 4, name: 'Site Visit', color: '#8b5cf6', order: 4 },
    { id: 5, name: 'Negotiation', color: '#ef4444', order: 5 },
    { id: 6, name: 'Won', color: '#059669', order: 6 },
    { id: 7, name: 'Lost', color: '#6b7280', order: 7 },
]);

const propertyTypes = ref([
    { id: 1, name: 'Apartment' },
    { id: 2, name: 'Villa' },
    { id: 3, name: 'Plot' },
    { id: 4, name: 'Commercial' },
    { id: 5, name: 'Office Space' },
    { id: 6, name: 'Shop' },
]);

const saveCompanySettings = () => {
    console.log('Save company settings:', companyForm);
    // TODO: Implement API call
};

const addStatus = () => {
    const newId = Math.max(...leadStatuses.value.map(s => s.id)) + 1;
    leadStatuses.value.push({
        id: newId,
        name: 'New Status',
        color: '#6366f1',
        order: leadStatuses.value.length + 1,
    });
};

const removeStatus = (index) => {
    leadStatuses.value.splice(index, 1);
};

const moveStatus = (index, direction) => {
    const newIndex = index + direction;
    if (newIndex >= 0 && newIndex < leadStatuses.value.length) {
        const temp = leadStatuses.value[index];
        leadStatuses.value[index] = leadStatuses.value[newIndex];
        leadStatuses.value[newIndex] = temp;
    }
};

const saveStatuses = () => {
    console.log('Save statuses:', leadStatuses.value);
    // TODO: Implement API call
};

const addPropertyType = () => {
    const newId = Math.max(...propertyTypes.value.map(t => t.id)) + 1;
    propertyTypes.value.push({
        id: newId,
        name: 'New Type',
    });
};

const removePropertyType = (index) => {
    propertyTypes.value.splice(index, 1);
};

const savePropertyTypes = () => {
    console.log('Save property types:', propertyTypes.value);
    // TODO: Implement API call
};
</script>
