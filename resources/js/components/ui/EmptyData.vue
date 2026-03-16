<script setup>
const emptyImage = new URL('@assets/svg/empty.svg', import.meta.url).href;
</script>

<template>
    <div class="w-100 d-flex flex-column align-items-center mb-4">
        <img :src="emptyImage" alt="Vazio" class="mb-3">
        <h3 class="mb-0">{{ $t('messages.oops_nothing_here') }}</h3>
        <p class="text-muted">{{ $t('messages.check_filters_or_adjust_search') }}</p>
    </div>
</template>

<style scoped>
img {
    width: 100%;
    max-width: 200px;
}
</style>