<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import Button from 'primevue/button'
import Avatar from 'primevue/avatar'
import Tag from 'primevue/tag'
import Drawer from 'primevue/drawer'
import NavItem from '../ui/NavItem.vue'
import { useAuthStore } from '@/stores/authStore'
import { isAdmin } from '@/helpers/auth'

const auth           = useAuthStore()
const router         = useRouter()
const route          = useRoute()

const sidebarCollapsed = ref(false)
const mobileSidebar    = ref(false)

const avatarLabel = computed(() =>
    auth.user?.name?.charAt(0)?.toUpperCase() ?? 'U'
)

const pageTitles = {
    'dashboard':      'Dashboard',
    'search':         'Buscar Leads',
    'leads':          'Meus Leads',
    'admin.dashboard':'Painel Admin',
    'admin.users':    'Gerenciar Usuários',
    'admin.plans':    'Gerenciar Planos',
    'admin.leads':    'Todos os Leads',
}

const pageTitle = computed(() => pageTitles[route.name] ?? 'LeadRadar')
</script>

<template>
    <div class="layout-wrapper" :class="{ 'sidebar-collapsed': sidebarCollapsed }">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <router-link to="/" class="brand">
                    <span class="brand-icon">🎯</span>
                    <Transition name="fade">
                        <span v-if="!sidebarCollapsed" class="brand-name">LeadRadar</span>
                    </Transition>
                </router-link>
                <Button
                    :icon="sidebarCollapsed ? 'pi pi-angle-right' : 'pi pi-angle-left'"
                    text rounded
                    class="collapse-btn"
                    @click="sidebarCollapsed = !sidebarCollapsed"
                />
            </div>

            <nav class="sidebar-nav">
                <template v-if="!isAdmin()">
                    <NavItem to="/app"        icon="pi-home"        label="Dashboard"   :collapsed="sidebarCollapsed" />
                    <NavItem to="/app/search"  icon="pi-search"      label="Buscar Leads" :collapsed="sidebarCollapsed" />
                    <NavItem to="/app/leads"   icon="pi-list"        label="Meus Leads"  :collapsed="sidebarCollapsed" />
                </template>
                <template v-else>
                    <NavItem to="/app/admin"        icon="pi-chart-bar"  label="Dashboard"  :collapsed="sidebarCollapsed" />
                    <NavItem to="/app/admin/users"  icon="pi-users"      label="Usuários"   :collapsed="sidebarCollapsed" />
                    <NavItem to="/app/admin/plans"  icon="pi-credit-card" label="Planos"    :collapsed="sidebarCollapsed" />
                    <NavItem to="/app/admin/leads"  icon="pi-database"   label="Todos Leads" :collapsed="sidebarCollapsed" />
                </template>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="user-info" v-if="!sidebarCollapsed">
                    <Avatar :label="avatarLabel" class="user-avatar" shape="circle" />
                    <div class="user-meta">
                        <span class="user-name">{{ auth.user?.name }}</span>
                        <span class="user-role">{{ auth.isAdmin ? 'Admin' : auth.user?.plan?.name }}</span>
                    </div>
                </div>
                <Button
                    icon="pi pi-sign-out"
                    :label="sidebarCollapsed ? '' : 'Sair'"
                    text severity="secondary"
                    class="logout-btn"
                    @click="handleLogout"
                />
            </div>
        </aside>

        <!-- Main Content -->
        <div class="layout-main">
            <!-- Topbar (mobile) -->
            <header class="topbar">
                <Button icon="pi pi-bars" text rounded @click="mobileSidebar = true" class="d-md-none" />
                <h2 class="page-title">{{ pageTitle }}</h2>
                <div class="topbar-right">
                    <Tag v-if="!auth.isAdmin && auth.user?.stats" severity="info" class="quota-tag">
                        <i class="pi pi-chart-pie me-1" />
                        {{ auth.user.stats.leads_used }}/{{ auth.user.stats.leads_limit }} leads
                    </Tag>
                    <Avatar :label="avatarLabel" shape="circle" class="topbar-avatar" />
                </div>
            </header>

            <main class="page-content">
                <router-view></router-view>
            </main>
        </div>

        <!-- Mobile Sidebar Drawer -->
        <Drawer v-model:visible="mobileSidebar" position="left" :modal="true" class="mobile-drawer">
            <template #header>
                <span class="brand-name">🎯 LeadRadar</span>
            </template>
            <nav class="sidebar-nav mobile-nav">
                <template v-if="!isAdmin()">
                    <NavItem to="/app/"        icon="pi-home"         label="Dashboard"    @click="mobileSidebar = false" />
                    <NavItem to="/app/search"  icon="pi-search"       label="Buscar Leads" @click="mobileSidebar = false" />
                    <NavItem to="/app/leads"   icon="pi-list"         label="Meus Leads"   @click="mobileSidebar = false" />
                </template>
                <template v-else>
                    <NavItem to="/app/admin"        icon="pi-chart-bar"   label="Dashboard"   @click="mobileSidebar = false" />
                    <NavItem to="/app/admin/users"  icon="pi-users"       label="Usuários"    @click="mobileSidebar = false" />
                    <NavItem to="/app/admin/plans"  icon="pi-credit-card" label="Planos"      @click="mobileSidebar = false" />
                    <NavItem to="/app/admin/leads"  icon="pi-database"    label="Todos Leads" @click="mobileSidebar = false" />
                </template>
            </nav>
        </Drawer>
    </div>
</template>

<style scoped>
:root {
    --sidebar-width:           240px;
    --sidebar-collapsed-width: 68px;
    --topbar-height:           60px;

    --color-sidebar-bg:    #0f172a;
    --color-sidebar-hover: rgba(255,255,255,.07);
    --color-sidebar-active:rgba(99,102,241,.2);
    --color-sidebar-border:rgba(255,255,255,.06);
}

.layout-wrapper {
    --sidebar-width:           240px;
    --sidebar-collapsed-width: 68px;
    --topbar-height:           60px;

    --color-sidebar-bg:    #0f172a;
    --color-sidebar-hover: rgba(255,255,255,.07);
    --color-sidebar-active:rgba(99,102,241,.2);
    --color-sidebar-border:rgba(255,255,255,.06);
    display: flex;
    min-height: 100vh;
}

.layout-wrapper.sidebar-collapsed .sidebar {
    width: var(--sidebar-collapsed-width);
}

.layout-wrapper.sidebar-collapsed .layout-main {
    margin-left: var(--sidebar-collapsed-width);
}

.sidebar {
    width: var(--sidebar-width);
    min-height: 100vh;
    background: var(--color-sidebar-bg) !important;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    transition: width var(--transition);
    border-right: 1px solid var(--color-sidebar-border);
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem .875rem;
    border-bottom: 1px solid var(--color-sidebar-border);
    min-height: var(--topbar-height);
}

.brand {
    display: flex;
    align-items: center;
    gap: .6rem;
    text-decoration: none;
    overflow: hidden;
}

.brand-icon {
    font-size: 1.4rem;
    flex-shrink: 0;
}

.brand-name {
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    white-space: nowrap;
    letter-spacing: -.3px;
}

.collapse-btn {
    color: rgba(255,255,255,.4) !important;
    flex-shrink: 0;
}

.sidebar-nav {
    flex: 1;
    padding: .75rem .5rem;
    display: flex;
    flex-direction: column;
    gap: 6px;
    overflow-x: hidden;
}

.sidebar-footer {
    padding: .75rem .5rem;
    border-top: 1px solid var(--color-sidebar-border);
}

.user-info {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .5rem .75rem;
    margin-bottom: .4rem;
    overflow: hidden;
}

.user-avatar {
    background: var(--color-indigo) !important;
    color: #fff !important;
    flex-shrink: 0;
}

.user-meta {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.user-name {
    font-size: .875rem;
    font-weight: 600;
    color: rgba(255,255,255,.9);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role {
    font-size: .75rem;
    color: rgba(255,255,255,.4);
}

.logout-btn {
    width: 100%;
    justify-content: flex-start !important;
    padding: .6rem .75rem !important;
    color: rgba(255,255,255,.5) !important;
    border-radius: 10px !important;
}

.logout-btn:hover {
    background: var(--color-sidebar-hover) !important;
    color: rgba(255,255,255,.8) !important;
}

.layout-main {
    flex: 1;
    margin-left: var(--sidebar-width);
    transition: margin-left var(--transition);
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.topbar {
    height: var(--topbar-height);
    background: var(--p-surface-card, #fff);
    border-bottom: 1px solid var(--p-surface-border, #e2e8f0);
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
    gap: 1rem;
    position: sticky;
    top: 0;
    z-index: 50;
}

.page-title {
    font-size: 1rem;
    font-weight: 600;
    flex: 1;
    margin: 0;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: .75rem;
    margin-left: auto;
}

.topbar-avatar {
    cursor: pointer;
}

.page-content {
    flex: 1;
    padding: 1.5rem;
}

.mobile-nav {
    background: transparent !important;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity var(--transition);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@media (max-width: 768px) {

    .sidebar {
        transform: translateX(-100%);
        pointer-events: none;
    }

    .layout-main {
        margin-left: 0 !important;
    }

    .page-content {
        padding: 1rem;
    }

}
</style>