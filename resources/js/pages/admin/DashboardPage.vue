<script setup>
import StatsCard from '@/components/ui/StatsCard.vue';
import { showAlert } from '@/helpers/alert';
import dashboardService from '@/services/dashboard.service';
import { computed, onMounted, ref } from 'vue';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Avatar from 'primevue/avatar';
import Tag from 'primevue/tag';
import { copyItem } from '@/helpers/functions';
import Button from 'primevue/button';
import i18n from "@/i18n";
import TopUsersSkeleton from '@/components/skeletons/TopUsersSkeleton.vue';

const t = i18n.global.t;

const empty_img = new URL('@assets/svg/empty.svg', import.meta.url).href;
const today = new Date().toLocaleDateString('pt-BR', { dateStyle: 'long' });

const loading   = ref(false);
const medals    = ['🥇', '🥈', '🥉'];
const data      = ref(null);
const stats     = ref({});
const top_users = ref([]);

const skeletonRows = Array.from({ length: 8  });

const statsCards = computed(() => [
    { label: t('messages.users'),            value: stats.value.total_users      ?? 0, icon: 'pi-users',       color: 'indigo', route_name: '' },
    { label: t('messages.total_leads'),      value: stats.value.total_leads      ?? 0, icon: 'pi-database',    color: 'green',  route_name: '' },
    { label: t('messages.leads_this_month'), value: stats.value.leads_this_month ?? 0, icon: 'pi-calendar',    color: 'orange', route_name: '' },
    { label: t('messages.active_plans'),     value: stats.value.active_plans     ?? 0, icon: 'pi-credit-card', color: 'blue',   route_name: '' },
]);

onMounted(async () => {
    await handle();
});

const handle = async () => {
    try {
        loading.value = true;
        const response = await dashboardService.adminDashboard();
        data.value = response;
        stats.value = response.stats;
        top_users.value = response.top_users?.map(u => ({ ...u, leads_this_month: u.leads_this_month })) ?? [];
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        loading.value = false;
    }
}

</script>

<template>
    <section class="row g-4">
        <div class="col-12">
            <div class="page-header">
                <div>
                    <h1 class="page-title-h mb-2">⚙️ {{ $t('messages.title_admin_panel') }}</h1>
                    <p class="page-subtitle">{{ $t('messages.system_overview') }} — {{ today }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3" v-for="status in statsCards" :key="status.label">
            <StatsCard v-bind="status" :is_loading="loading"/>
        </div>

        <div class="col-12">
            <Card>
                <template #title>
                    <div class="d-flex align-items-center gap-2">
                        <i class="pi pi-trophy text-warning" />
                        {{ $t('messages.user_ranking_this_month') }}
                    </div>
                </template>
                <template #content>
                    <TopUsersSkeleton v-if="loading"/>

                    <DataTable v-if="!loading && data" :value="top_users" stripedRows class="mt-2">
                        <Column header="#" style="width:50px">
                            <template #body="{ index }">
                                <span class="rank-medal">{{ medals[index] ?? index + 1 }}</span>
                            </template>
                        </Column>
                        <Column field="name" :header="$t('messages.label_user')" style="min-width: 100px">
                            <template #body="{ data }">
                                <div class="d-flex gap-2 align-items-center">
                                    <Avatar 
                                        :image="data.avatar_url" 
                                        shape="circle" 
                                    />

                                    <div class="d-flex flex-column">
                                        <span class="text-truncate d-inline-block" v-tooltip.top="data.full_name">{{ data.full_name }}</span>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column field="email" :header="$t('messages.label_email')" style="min-width: 100px">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    <Button 
                                        icon="pi pi-copy" 
                                        variant="text" 
                                        aria-label="Filter" 
                                        severity="secondary"
                                        rounded
                                        @click="copyItem($t('messages.label_email'), data.email)"
                                    />
                                    <span>{{ data.email }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column :header="$t('messages.label_plan')">
                            <template #body="{ data }">
                                <Tag v-if="data.plan" :value="data.plan.name" severity="info" />
                                <span v-else class="text-muted">—</span>
                            </template>
                        </Column>
                        <Column :header="$t('messages.leads_month')" style="width:140px">
                            <template #body="{ data }">
                                <span class="fw-semibold text-primary">{{ data.leads_this_month ?? 0 }}</span>
                                <span v-if="data.plan" class="text-muted"> / {{ data.plan.monthly_leads_limit }}</span>
                            </template>
                        </Column>
                        <Column header="" style="width:80px">
                            <template #body="{ data }">
                                <router-link to="">
                                    <Button icon="pi pi-pen-to-square" text rounded />
                                </router-link>
                            </template>
                        </Column>
                    </DataTable>

                    <div v-if="!loading && !data" class="empty_result text-muted">
                        <img :src="empty_img" alt="empty">
                        <p>{{ $t('messages.no_results_found') }}</p>
                    </div>
                </template>
            </Card>
        </div>
    </section>
</template>

<style scoped>
.empty_result {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px 20px;
}

.empty_result img {
    max-width: 200px;
}
</style>