<script setup>
import Password from 'primevue/password';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import { useAuthStore } from '@/stores/authStore';
import { useRoute, useRouter } from 'vue-router';
import { onMounted, reactive, ref } from 'vue';
import { showAlert } from '@/helpers/alert';
import authService from '@/services/auth.service';
import { isAdmin } from '@/helpers/auth';

const auth   = useAuthStore();
const router = useRouter();
const route  = useRoute();

const loading     = ref(false);
const fieldErrors = reactive({});

const form = reactive({
    email: '',
    password: '',
    remember_me: false
});

onMounted(() => {
    const emailFromQuery = route.query.email;
    const savedEmail = localStorage.getItem('last_email');

    if(emailFromQuery) {
        form.email = emailFromQuery;
    } else if(savedEmail) {
        form.email = savedEmail;
    }
});

const onSubmit = async () => {
    try {
        loading.value = true;
        await authService.login(form);

        const redirect = route.query.redirect || (isAdmin() ? '/app/admin' : '/');
        router.push(redirect);
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        loading.value = false;
    }
}

</script>

<template>
    <section class="auth-page vh-100 item-center px-3">
        <div class="bg-white p-4 rounded-3">
            <form @submit.prevent="onSubmit">
                <div class="text-center mb-4">
                    <span class="fs-1">🎯</span>
                    <h1 class="fs-3 mb-1">LeadRadar</h1>
                    <p class="mt-2 fs-7 text-muted">Acesse sua conta para continuar</p>
                </div>

                <div class="field mb-3">
                    <label for="email">E-mail</label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="seu@email.com"
                        :invalid="!!fieldErrors.email"
                        fluid
                    />
                </div>

                <div class="field mb-3">
                    <label for="password">Senha</label>
                    <Password
                        id="password"
                        v-model="form.password"
                        placeholder="••••••••"
                        :feedback="false"
                        :invalid="!!fieldErrors.password"
                        toggleMask
                        fluid
                    />
                </div>

                <div>
                    <Checkbox v-model="form.remember_me" :binary="true" inputId="remember_me" />
                    <label for="remember_me" class="ms-2">Lembrar de mim</label>
                </div>

                <Button
                    type="submit"
                    label="Entrar"
                    icon="pi pi-sign-in"
                    :loading="loading"
                    fluid
                    class="mt-3"
                />
            </form>

            <Divider />

            <p class="text-muted">
                Não tem conta?
                <router-link to="/registrar" class="text-decoration-none link-primary">Criar conta grátis</router-link>
            </p>
        </div>
    </section>
</template>

<style scoped>
.auth-page {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
}

.auth-page > div {
    width: 100%;
    max-width: 400px;
}

.field label {
    display: block;
    font-size: .8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--p-text-muted-color, #64748b);
    margin-bottom: .4rem;
}
</style>