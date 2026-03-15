<script setup>
import Spinner from '../ui/Spinner.vue';


</script>

<template>
    <div class="d-flex align-items-center gap-3 loading-data">
        <div>
            <Spinner />
        </div>
        <span>
            <slot></slot>
        </span>
    </div>
</template>

<style scoped>
.loading-data {
    background-color: var(--p-primary-100);
    padding: 10px;
    border-radius: 12px;
    font-weight: 500;
}

html.dark-mode .loading-data{
    background-color: var(--p-primary-800);
}
</style>