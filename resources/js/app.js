import './bootstrap';
import './axios';
import 'notyf/notyf.min.css';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';

import 'bootstrap/dist/css/bootstrap-utilities.min.css'
import 'bootstrap/dist/css/bootstrap-grid.min.css'

import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import { initTheme } from './helpers/theme';
import { pt } from './locales/primevue/pt';
import Tooltip from 'primevue/tooltip';
import 'primeicons/primeicons.css';
import PageLoading from '@/components/shared/PageLoading.vue';

const pageLoading = createApp(PageLoading);
pageLoading.mount('#pageLoading');

const app = createApp(App);
const pinia = createPinia();

app.use(router);
app.use(pinia);

app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: '.dark-mode',
        }
    },
    locale: pt
});

app.directive('tooltip', Tooltip);

initTheme();

app.mount('#app');