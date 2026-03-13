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

<style scoped>
:root {
    --color-sidebar-hover: rgba(255,255,255,.07);
    --color-sidebar-active:rgba(99,102,241,.2);
}

.nav-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .6rem .75rem;
    border-radius: 10px;
    border: none;
    background: transparent;
    color: rgba(255,255,255,.6);
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
    white-space: nowrap;
    font-size: .9rem;
}

.nav-item:hover {
    background: var(--color-sidebar-hover);
    color: rgba(255,255,255,.9);
}

.nav-item-active {
    background: var(--color-sidebar-active);
    color: #fff;
}

.nav-item-active .nav-item-icon {
    color: var(--color-indigo);
}

.nav-item-collapsed {
    justify-content: center;
    padding: .7rem;
}

.nav-item-icon {
    font-size: 1rem;
    flex-shrink: 0;
}

.nav-item-label {
    font-weight: 500;
}
</style>