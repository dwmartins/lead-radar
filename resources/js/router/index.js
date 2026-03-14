import { isAdmin, isUser } from '@/helpers/auth';
import { APP_NAME } from '@/helpers/constants';
import authService from '@/services/auth.service';
import { useAuthStore } from '@/stores/authStore';
import { pageLoadingStore } from '@/stores/pageLoadingStore';
import { createRouter, createWebHistory } from 'vue-router';
import admin from './admin';

const LoginPage    = () => import('@/pages/LoginPage.vue');
const NotFoundPage = () => import('@/pages/NotFoundPage.vue');

const routes = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'login',
        component: LoginPage
    },

    ...admin,

    {
        path: '/:pathMatch(.*)*',
        name: 'not-found-page',
        component: NotFoundPage
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    }
}); 

router.beforeEach(async (to) => {
    document.title = to.meta.title || APP_NAME

    const auth = useAuthStore()
    const logged = auth.isAuthenticate()

    if (to.name === 'login') {
        if (logged || await validateSession()) {
            return redirectToDashboard()
        }
        return true
    }

    if (to.meta.requiresAuth) {
        if (!logged && !(await validateSession())) {
            return { name: 'login' }
        }

        if (to.meta.requireAdmin && !isAdmin()) {
            return redirectToDashboard()
        }
    }

    return true
})

async function validateSession() {
    pageLoadingStore.show()
    const { is_valid } = await authService.validate()
    pageLoadingStore.hide()
    return is_valid
}

function redirectToDashboard() {
    if (isAdmin()) return { path: '/admin/dashboard' }
    if (isUser()) return { path: '/dashboard' }
}

export default router