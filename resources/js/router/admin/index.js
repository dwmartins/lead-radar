import AppLayout from '@/components/layout/AppLayout.vue';

const DashboardPage = () => import('@/pages/admin/DashboardPage.vue');

export default [
    {
        path: '/admin/dashboard',
        component: AppLayout,
        meta: { requiresAuth:true, requireAdmin: true},
        children: [
            {
                path: 'dashboard',
                name: 'admin.dashboard',
                component: DashboardPage
            }
        ]
    }
]