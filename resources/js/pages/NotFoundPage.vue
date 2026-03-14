<script setup>
import { onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

onMounted(() => {
    document.title = t('messages.page_not_found_title');
});
</script>

<template>
    Página não encontrada
</template>