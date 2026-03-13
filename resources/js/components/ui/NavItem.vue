<script setup>

defineProps({
    to:        { type: String, required: true },
    icon:      { type: String, required: true },
    label:     { type: String, required: true },
    collapsed: { type: Boolean, default: false },
});

defineEmits(['click']);

</script>
<template>
    <router-link
        :to="to"
        v-slot="{ isActive, navigate }"
        custom
    >
        <button
            v-tooltip.right="collapsed ? label : ''"
            :class="['nav-item', { 'nav-item-active': isActive, 'nav-item-collapsed': collapsed }]"
            @click="() => { navigate(); $emit('click') }"
        >
            <i :class="`pi ${icon} nav-item-icon`" />
            <Transition name="fade">
                <span v-if="!collapsed" class="nav-item-label">{{ label }}</span>
            </Transition>
        </button>
    </router-link>
</template>