<script setup>
import { getLocale } from '@/helpers/locale';
import { Skeleton } from 'primevue';
import { computed } from 'vue'

const props = defineProps({
    label:      { type: String, required: true },
    value:      { type: [Number, String], required: true },
    icon:       { type: String, required: true },
    color:      { type: String, default: 'blue' },
    route_name: { type: String},

    is_loading: { type: Boolean, required: true}
});

const locale = getLocale();

const formattedValue = computed(() =>
    typeof props.value === 'number'
        ? props.value.toLocaleString(locale)
        : props.value
);

</script>

<template>
    <router-link :to="{name: props.route_name}">
        <div :class="['stats-card', `stats-card-${color}`]">
            <div class="stats-card-icon">
                <i :class="`pi ${icon}`" />
            </div>
            <div class="stats-card-body">
                <span class="stats-card-label">{{ label }}</span>
                <span v-if="!props.is_loading" class="stats-card-value">{{ formattedValue }}</span>
                <Skeleton v-else width="25px" height="22px" class="mt-2"/>
            </div>
        </div>
    </router-link>
</template>
