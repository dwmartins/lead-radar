const usersPage = () => import('@/pages/admin/UsersPage.vue');

export default [
    {
        path: 'users',
        name: 'admin.users',
        component: usersPage
    }
];