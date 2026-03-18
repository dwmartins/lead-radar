<script setup>
import Toast from 'primevue/toast';
import PageLoading from '@/components/shared/PageLoading.vue';
import { ConfirmDialog } from 'primevue';

</script>

<template>
    <section>
        <PageLoading/>
        <Toast/>
        <ConfirmDialog />
        <router-view></router-view>
    </section>
</template>