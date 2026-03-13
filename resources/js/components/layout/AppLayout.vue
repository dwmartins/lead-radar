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
                    <NavItem to="/"        icon="pi-home"        label="Dashboard"   :collapsed="sidebarCollapsed" />
                    <NavItem to="/app/search"  icon="pi-search"      label="Buscar Leads" :collapsed="sidebarCollapsed" />
                    <NavItem to="/app/leads"   icon="pi-list"        label="Meus Leads"  :collapsed="sidebarCollapsed" />
                </template>
                <template v-else>
                    <NavItem to="/admin/dashboard"        icon="pi-chart-bar"  label="Dashboard"  :collapsed="sidebarCollapsed" />
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
        <Drawer v-model:visible="mobileSidebar" position="left" :modal="true" class="mobile-drawer border-0">
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