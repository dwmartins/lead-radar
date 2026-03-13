import { isAdmin, isUser } from '@/helpers/auth';
import { APP_NAME } from '@/helpers/constants';
import authService from '@/services/auth.service';
import { useAuthStore } from '@/stores/authStore';
import { pageLoadingStore } from '@/stores/pageLoadingStore';
import { createRouter, createWebHistory } from 'vue-router';

const LoginPage     = () => import('@/pages/LoginPage.vue');
const NotFoundPage  = () => import('@/pages/NotFoundPage.vue');

const routes = [
    {
        path: '/entrar',
        name: 'login',
        component: LoginPage
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found-page',
        component: NotFoundPage,
        meta: { title: "Página não encontrada" }
    },
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

    const userStore = useAuthStore();

    if (to.path === '/') {
        return redirectToDashboard();
    }

    if(to.path === '/entrar') {
        if (!userStore.isAuthenticate()) {
            pageLoadingStore.show();
            const result = await authService.validate();
            pageLoadingStore.hide();

            if(result.is_valid) {
               return redirectToDashboard();
            }
        }

        // Se usuário já autenticado, redireciona
        if (userStore.isAuthenticate()) {
            return redirectToDashboard();
        }
    }

    /**
     * - Todas as rotas /app exigem autenticação.
     * - Na entrada inicial ou refresh, valida a sessão no backend.
     * - Em navegação interna, usa apenas o estado local.
     */
    if(to.path.startsWith('/app')) {
        const fromOutsideApp = !from.path.startsWith('/app') || !from.matched.length;

        if(fromOutsideApp) {
            pageLoadingStore.show();
            const result = await authService.validate();
            pageLoadingStore.hide();

            if(!result.is_valid) {
                return { name: 'login' }
            }

            return validateRoute(to);
        } else {
            return userStore.isAuthenticate() ? validateRoute(to) : { name: 'login' }
        }
    }
});

function redirectToDashboard() {
    if(isAdmin()) return { path: '/app/admin' }
    if(isUser()) return { path: '/app' }
    
    return true;
}

function validateRoute(to) {
    if(isAdmin()) return true;

    return true;
}

export default router;