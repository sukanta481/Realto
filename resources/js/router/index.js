import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Auth views
import Login from '../views/auth/Login.vue';
import Register from '../views/auth/Register.vue';

// Public views
import Home from '../views/public/Home.vue';

// Main views
import Dashboard from '../views/Dashboard.vue';
import LeadList from '../views/leads/LeadList.vue';
import LeadDetail from '../views/leads/LeadDetail.vue';
import PropertyList from '../views/properties/PropertyList.vue';
import PropertyDetail from '../views/properties/PropertyDetail.vue';
import DealList from '../views/deals/DealList.vue';
import FollowUpList from '../views/follow-ups/FollowUpList.vue';
import TeamManagement from '../views/TeamManagement.vue';
import Settings from '../views/Settings.vue';
import Reports from '../views/Reports.vue';

const routes = [
    // Public routes
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: { public: true },
    },

    // Auth routes (no layout)
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { guest: true },
    },

    // Protected routes with layout (CRM Dashboard)
    {
        path: '/app',
        component: () => import('../components/layout/AppLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: Dashboard,
            },
            {
                path: 'leads',
                name: 'leads',
                component: LeadList,
            },
            {
                path: 'leads/:id',
                name: 'lead-detail',
                component: LeadDetail,
            },
            {
                path: 'properties',
                name: 'properties',
                component: PropertyList,
            },
            {
                path: 'properties/:id',
                name: 'property-detail',
                component: PropertyDetail,
            },
            {
                path: 'deals',
                name: 'deals',
                component: DealList,
            },
            {
                path: 'follow-ups',
                name: 'follow-ups',
                component: FollowUpList,
            },
            {
                path: 'team',
                name: 'team',
                component: TeamManagement,
            },
            {
                path: 'settings',
                name: 'settings',
                component: Settings,
            },
            {
                path: 'reports',
                name: 'reports',
                component: Reports,
            },
        ],
    },

    // 404
    {
        path: '/:pathMatch(.*)*',
        redirect: '/',
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        // Scroll to top on route change
        if (savedPosition) {
            return savedPosition;
        }
        return { top: 0 };
    },
});

// Navigation guard
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    // Public routes don't need auth check
    if (to.meta.public) {
        return next();
    }

    // Check if route requires auth
    if (to.meta.requiresAuth && !authStore.token) {
        return next({ name: 'login' });
    }

    // Check if route is for guests only
    if (to.meta.guest && authStore.token) {
        return next({ name: 'dashboard' });
    }

    next();
});

export default router;
