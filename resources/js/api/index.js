import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
});

// Request interceptor to add auth token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor for error handling
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;

// Auth API
export const authApi = {
    login: (data) => api.post('/auth/login', data),
    register: (data) => api.post('/auth/register', data),
    logout: () => api.post('/auth/logout'),
    getUser: () => api.get('/auth/user'),
    updateProfile: (data) => api.put('/auth/profile', data),
    changePassword: (data) => api.put('/auth/password', data),
};

// Dashboard API
export const dashboardApi = {
    getStats: () => api.get('/dashboard'),
    getQuickStats: () => api.get('/dashboard/quick-stats'),
};

// Leads API
export const leadsApi = {
    getAll: (params) => api.get('/leads', { params }),
    getKanban: () => api.get('/leads/kanban'),
    getSources: () => api.get('/leads/sources'),
    getOne: (id) => api.get(`/leads/${id}`),
    create: (data) => api.post('/leads', data),
    update: (id, data) => api.put(`/leads/${id}`, data),
    updateStatus: (id, statusId) => api.patch(`/leads/${id}/status`, { lead_status_id: statusId }),
    convert: (id, data) => api.post(`/leads/${id}/convert`, data),
    delete: (id) => api.delete(`/leads/${id}`),
};

// Properties API
export const propertiesApi = {
    getAll: (params) => api.get('/properties', { params }),
    getStats: () => api.get('/properties/stats'),
    getOne: (id) => api.get(`/properties/${id}`),
    create: (data) => api.post('/properties', data),
    update: (id, data) => api.put(`/properties/${id}`, data),
    updateStatus: (id, status) => api.patch(`/properties/${id}/status`, { status }),
    togglePublish: (id) => api.patch(`/properties/${id}/publish`),
    uploadImages: (id, formData) => api.post(`/properties/${id}/images`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    }),
    delete: (id) => api.delete(`/properties/${id}`),
};

// Follow-ups API
export const followUpsApi = {
    getAll: (params) => api.get('/follow-ups', { params }),
    getCalendar: (params) => api.get('/follow-ups/calendar', { params }),
    getToday: () => api.get('/follow-ups/today'),
    getOverdue: () => api.get('/follow-ups/overdue'),
    getOne: (id) => api.get(`/follow-ups/${id}`),
    create: (data) => api.post('/follow-ups', data),
    update: (id, data) => api.put(`/follow-ups/${id}`, data),
    complete: (id, data) => api.patch(`/follow-ups/${id}/complete`, data),
    reschedule: (id, data) => api.patch(`/follow-ups/${id}/reschedule`, data),
    cancel: (id) => api.patch(`/follow-ups/${id}/cancel`),
    delete: (id) => api.delete(`/follow-ups/${id}`),
};

// Clients API
export const clientsApi = {
    getAll: (params) => api.get('/clients', { params }),
    getOne: (id) => api.get(`/clients/${id}`),
    create: (data) => api.post('/clients', data),
    update: (id, data) => api.put(`/clients/${id}`, data),
    delete: (id) => api.delete(`/clients/${id}`),
};

// Deals API
export const dealsApi = {
    getAll: (params) => api.get('/deals', { params }),
    getPipeline: () => api.get('/deals/pipeline'),
    getStats: () => api.get('/deals/stats'),
    getOne: (id) => api.get(`/deals/${id}`),
    create: (data) => api.post('/deals', data),
    update: (id, data) => api.put(`/deals/${id}`, data),
    updateStage: (id, stage, reason) => api.patch(`/deals/${id}/stage`, { stage, close_reason: reason }),
    updatePayment: (id, data) => api.patch(`/deals/${id}/payment`, data),
    delete: (id) => api.delete(`/deals/${id}`),
};
