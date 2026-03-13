<script setup>
import Toast from 'primevue/toast';
import PageLoading from '@/components/shared/PageLoading.vue';

</script>

<template>
    <section>
        <PageLoading/>
        <Toast/>
        <router-view></router-view>
    </section>
</template>