<script setup>
import i18n from "@/i18n";
import { getLocale } from "@/helpers/locale";
import { onMounted, reactive, ref } from 'vue';
import DatePicker from "primevue/datepicker";
import Card from 'primevue/card';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import planService from "@/services/plan.service";
import Column from "primevue/column";
import { useQueryFilters } from "@/composables/useQueryFilters";
import { showAlert } from "@/helpers/alert";
import subscriptionService from "@/services/subscription.service";
import Tag from "primevue/tag";
import { formatDate, formatDateTime } from "@/helpers/date";
import { useMobile } from "@/composables/useMobile";
import SubscriptionsSkeleton from "@/components/skeletons/SubscriptionsSkeleton.vue";
import SubscriptionFormDialog from "@/components/dialog/subscription/SubscriptionFormDialog.vue";
import userService from "@/services/user.service";
import EmptyData from "@/components/ui/EmptyData.vue";

const t = i18n.global.t;
const dateFormat   = getLocale() === 'pt' ? 'dd/mm/yy' : 'mm/dd/yy';
const { isMobile } = useMobile();

const filters = reactive({
    id:         {value: null, type: 'number'},
    status:     {value: null, type: 'boolean'},
    plan_id:    {value: null, type: 'number'},
    expires_at: {value: null, type: 'date'},
    user_id:    {value: null, type: 'number'}
});

const subscription_status = [
    { name: t('messages.subscription_active'),   code: 'active' },
    { name: t('messages.subscription_canceled'), code: 'canceled' },
    { name: t('messages.subscription_expired'),  code: 'expired' },
    { name: t('messages.subscription_pending'),  code: 'pending' },
    { name: t('messages.subscription_trial'),    code: 'trial' }
];

const subscriptionStatusMap = {
    active: 'success',
    canceled: 'danger',  
    expired: 'danger', 
    pending: 'warn',  
    trial: 'info'
};

const subscriptions = ref([]);
const subscription  = ref(null);
const users         = ref([]);
const plans         = ref([]);

const loading      = ref(false);
const loadingUsers = ref(false);
const loadingPlans = ref(false);

const pagination   = ref({});
const currentPage  = ref(1);
const itemsPerPage = ref(7);

const dialogVisible = reactive({
    form: false,
    detail: false
});

const { applyFromRoute, syncToRoute, buildApiFilters } = useQueryFilters(filters, currentPage);

onMounted(async () => {
    applyFromRoute();
    await fetchSubscriptions(currentPage.value);
    fetchPlans();
    fetchUsers();
});

const fetchSubscriptions = async (page = 1) => {
    syncToRoute(page);

    try {
        loading.value = true;
        const response = await subscriptionService.index(page, itemsPerPage.value, buildApiFilters());
        subscriptions.value = response?.data ?? [];
        handlePagination(response);
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        loading.value = false;
    }
}

const fetchPlans = async () => {
    try {
        loadingPlans.value = true;
        const response = await planService.index();
        plans.value = response;
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        loadingPlans.value = false;
    }
}

const fetchUsers = async () => {
    try {
        loadingUsers.value = true;
        const response = await userService.getAll(['id', 'name', 'last_name', 'email']);
        users.value = response;
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        loadingUsers.value = false;
    }
}

const handlePagination = (response) => {
    pagination.value = response.pagination ?? {};
    currentPage.value = (response.pagination?.current_page) ?? 1;
}

const onPage = (event) => {
    const page = event.page + 1;
    itemsPerPage.value = event.rows;
    fetchSubscriptions(page);
}

const onClearSearch = () => {
    for (const key in filters) {
        const { value, type } = filters[key];
        filters[key].value = null;
    }

    fetchSubscriptions();
}

const openDialog = (dialogType, data = null) => {
    subscription.value = null

    subscription.value = data ? {...data} : null;
    dialogVisible[dialogType] = true;
}

const onCloseDialog = async () => {
    await fetchSubscriptions();
}

</script>

<template>
    <section class="row g-4">
        <div class="col-12">
            <div class="page-header">
                <div>
                    <h1 class="page-title-h mb-2"><i class="pi pi pi-refresh me-2 fs-4 text-primary"></i>{{ $t('messages.title_subscriptions') }}</h1>
                    <p class="page-subtitle">{{ $t('messages.manage_subscriptions') }}</p>
                </div>
                <Button
                    :label="isMobile ? '' : $t('messages.btn_new_subscription')"
                    icon="pi pi-plus"
                    size="small"
                    @click="openDialog('form')"
                />
            </div>
        </div>

        <div class="col-12">
            <Card>
                <template #content>
                    <form @submit.prevent="fetchSubscriptions()" class="row g-3 mb-4">
                        <div class="col-12 col-md-3">
                            <InputNumber
                                :useGrouping="false"
                                fluid
                                v-model="filters.id.value"
                                :placeholder="$t('messages.placeholder_subscription_id')"
                            />
                        </div>
                        <div class="col-12 col-md-3">
                            <Select
                                v-model="filters.status.value"
                                optionLabel="name"
                                optionValue="code"
                                :options="subscription_status"
                                placeholder="Status"
                                class="w-100"
                            />
                        </div>
                        <div class="col-12 col-md-3">
                            <Select
                                v-model="filters.plan_id.value"
                                optionLabel="name"
                                optionValue="id"
                                :options="plans"
                                :placeholder="$t('messages.placeholder_plan')"
                                class="w-100"
                                :loading="loadingPlans"
                            />
                        </div>
                        <div class="col-12 col-md-3">
                            <DatePicker
                                v-model="filters.expires_at.value"
                                :dateFormat="dateFormat"
                                :placeholder="$t('messages.placeholder_renewal')"
                                fluid
                            />
                        </div>
                        <div class="col-12 col-md-4">
                            <Select
                                v-model="filters.user_id.value"
                                optionLabel="full_name"
                                optionValue="id"
                                :options="users"
                                :placeholder="$t('messages.placeholder_user')"
                                class="w-100"
                                :loading="loadingUsers"
                            />
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <div class="d-flex gap-3 justify-content-end justify-content-md-start">
                                <Button
                                    icon="pi pi-filter"
                                    :label="$t('messages.btn_filter')"
                                    type="submit"
                                />
                                <Button
                                    severity="secondary"
                                    icon="pi pi-filter-slash"
                                    :label="$t('messages.btn_clean_filter')"
                                    @click="onClearSearch()"
                                />
                            </div>
                        </div>
                    </form>

                    <SubscriptionsSkeleton v-show="loading"/>

                    <DataTable
                        v-show="!loading && subscriptions.length"
                        :value="subscriptions"
                        :lazy="true"
                        :totalRecords="pagination.total"
                        :first="(currentPage - 1) * itemsPerPage"
                        :rows="itemsPerPage"
                        :paginatorDropdown="true"
                        :rowsPerPageOptions="[5, 7, 10, 20]"
                        @page="onPage"
                        paginator
                        scrollable
                        stripedRows
                    >
                        <Column header="#" field="id"/>
                        <Column field="name" :header="$t('messages.label_user')" style="min-width: 100px">
                            <template #body="{ data }">
                                <div class="d-flex flex-column">
                                    <span class="text-truncate d-inline-block mb-1" v-tooltip.top="data.user.email">{{ data.user.email }}</span>
                                    <span class="text-muted fs-7">{{ formatDateTime(data.created_at) }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column :header="$t('messages.label_plan')" >
                            <template #body="{ data }">
                                <Tag 
                                    v-if="data.plan" 
                                    :value="data.plan.name" 
                                    severity="info" 
                                    style="font-size: 12px; padding: 2px 6px;"
                                />
                                <span v-else class="text-muted text-center w-100 d-block">—</span>
                            </template>
                        </Column>
                        <Column>
                            <template #header>
                                <div class="fw-semibold text-center w-100">
                                    <span>Status</span>
                                </div>
                            </template>
                            <template #body="{ data }">
                                <div class="text-center">
                                    <Tag
                                        :severity="subscriptionStatusMap[data.status]"
                                        :value="$t(`messages.subscription_${data.status}`)"
                                        style="font-size: 12px; padding: 2px 6px;"
                                    />
                                </div>
                            </template>
                        </Column>
                        <Column :header="$t('messages.label_billing_cycle')">
                            <template #body="{ data }">
                                <span class="text-capitalize">
                                    {{ $t(`messages.cycle_${data.billing_cycle}`) }}
                                </span>
                            </template>
                        </Column>
                        <Column>
                            <template #header>
                                <div class="fw-semibold text-center w-100">
                                    <span>{{ $t('messages.label_renewal') }}</span>
                                </div>
                            </template>
                            <template #body="{ data }">
                                <div class="text-center">
                                    <template v-if="data.expires_at">
                                        {{ formatDate(data.expires_at) }}
                                    </template>
                                    <template v-else>
                                        -
                                    </template>
                                </div>
                            </template>
                        </Column>
                        <Column header-class="text-center">
                            <template #header>
                                <span class="fw-semibold w-100">{{ $t('messages.label_actions') }}</span>
                            </template>
                            <template #body="{ data }">
                                <div class="d-flex justify-content-center gap-2">
                                    <Button
                                        icon="pi pi-pen-to-square" 
                                        variant="text" 
                                        aria-label="Filter" 
                                        rounded
                                        @click="openDialog('form', data)"
                                    />
                                    <Button 
                                        icon="pi pi-eye" 
                                        variant="text" 
                                        aria-label="Filter" 
                                        severity="secondary"
                                        rounded
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                    <EmptyData v-if="!subscriptions.length && !loading" />
                </template>
            </Card>
        </div>

        <SubscriptionFormDialog
            v-model="dialogVisible.form"
            :plans="plans"
            :users="users"
            :subscription="subscription"
            @saved="onCloseDialog"
        />
    </section>
</template>