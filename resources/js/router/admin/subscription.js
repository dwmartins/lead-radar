const subscriptionPage = () => import('@/pages/admin/activity/SubscriptionPage.vue');

export default [
    {
        path: 'subscriptions',
        name: 'admin.subscriptions',
        component: subscriptionPage
    }
];