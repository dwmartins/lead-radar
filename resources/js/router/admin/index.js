import AppLayout from '@/components/layout/AppLayout.vue';
import user from './user';

const DashboardPage = () => import('@/pages/admin/DashboardPage.vue');

export default [
    {
        path: '/admin',
        component: AppLayout,
        meta: { requiresAuth:true, requireAdmin: true},
        children: [
            {
                path: 'dashboard',
                name: 'admin.dashboard',
                component: DashboardPage
            },
            ...user
        ]
    }
]