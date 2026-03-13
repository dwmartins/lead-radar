import { isAdmin, isUser } from '@/helpers/auth';
import { APP_NAME } from '@/helpers/constants';
import authService from '@/services/auth.service';
import { useAuthStore } from '@/stores/authStore';
import { pageLoadingStore } from '@/stores/pageLoadingStore';
import { createRouter, createWebHistory } from 'vue-router';
import admin from './admin';

const RootRedirect  = () => import('@/pages/RootRedirect.vue');
const LoginPage     = () => import('@/pages/LoginPage.vue');
const NotFoundPage  = () => import('@/pages/NotFoundPage.vue');

const routes = [
    {
        path: '/',
        name: 'root',
        component: RootRedirect
    },
    {
        path: '/entrar',
        name: 'login',
        component: LoginPage
    },

    ...admin,

    {
        path: '/:pathMatch(.*)*',
        name: 'not-found-page',
        component: NotFoundPage,
        meta: { title: "Página não encontrada" }
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

router.beforeEach(async (to, from) => {
    document.title = to.meta.title || APP_NAME;

    const authStore = useAuthStore();

    /**
     * Página de login
     */
    if(to.name === 'login') {
        // Se usuário já autenticado, redireciona
        if (authStore.isAuthenticate()) {
            return redirectToDashboard(to);
        }

        pageLoadingStore.show();
        const result = await authService.validate();
        pageLoadingStore.hide();

        if(result.is_valid) {
            return redirectToDashboard(to);
        }
    }

    /**
     * - Todas as rotas /app exigem autenticação.
     * - Na entrada inicial ou refresh, valida a sessão no backend.
     * - Em navegação interna, usa apenas o estado local.
     */
    if(to.meta.requiresAuth) {
        const fromOutside = !authStore.isAuthenticate();

        if(fromOutside) {
            pageLoadingStore.show();
            const result = await authService.validate();
            pageLoadingStore.hide();

            if(!result.is_valid) {
                return { name: 'login' }
            }
        }

        return validateRoute(to);
    }
});

function redirectToDashboard(to) {
    if(isAdmin() && to.path !== '/admin/dashboard') {
        return { path: '/admin/dashboard' }
    }

    if(isUser() && to.path !== '/dashboard') {
        return { path: '/dashboard' }
    }

    return true
}

function validateRoute(to) {
    if(to.meta.requireAdmin && !isAdmin()) {
        return redirectToDashboard();
    }

    return true;
}

export default router;