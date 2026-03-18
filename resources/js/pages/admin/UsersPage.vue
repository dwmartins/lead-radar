<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useMobile } from '@/composables/useMobile';
import { copyItem } from '@/helpers/functions';
import Card from 'primevue/card';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Avatar from 'primevue/avatar';
import Tag from 'primevue/tag';
import { getLocale } from '@/helpers/locale';
import { useQueryFilters } from '@/composables/useQueryFilters';
import { showAlert } from '@/helpers/alert';
import userService from '@/services/user.service';
import UsersTableSkeleton from '@/components/skeletons/UsersTableSkeleton.vue';
import EmptyData from '@/components/ui/EmptyData.vue';
import planService from '@/services/plan.service';
import FormDialog from '@/components/dialog/user/FormDialog.vue';
import { useConfirm } from 'primevue';
import DeleteUserDialog from '@/components/dialog/user/DeleteUserDialog.vue';

const { isMobile } = useMobile();
const locale       = getLocale();
const confirm      = useConfirm();

const loadingUsers = ref(false);
const loadingPlans = ref(false);
const users        = ref([]);
const user         = ref(null);
const plans        = ref([]);

const pagination   = ref({});
const currentPage  = ref(1);
const itemsPerPage = ref(7);

const filters = reactive({
    keyword: {value: null, type: 'string'},
    account_status: {value: null, type: 'boolean'},
    plan_id: {value: null, type: 'number'},
});

const { applyFromRoute, syncToRoute, buildApiFilters } = useQueryFilters(filters, currentPage);

const dialogVisible = reactive({
    form: false,
    delete: false
});

onMounted( async () => {
    applyFromRoute();
    await fetchUsers(currentPage.value);
    fetchPlans();
});

const fetchUsers = async (page = 1) => {
    syncToRoute(page);

    try {
        loadingUsers.value = true;
        const response = await userService.index(page, itemsPerPage.value, buildApiFilters());
        users.value = response?.data ?? [];
        handlePagination(response);
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        loadingUsers.value = false;
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

const handlePagination = (response) => {
    pagination.value = response.pagination ?? {};
    currentPage.value = (response.pagination?.current_page) ?? 1;
}

const onPage = (event) => {
    const page = event.page + 1;
    itemsPerPage.value = event.rows;
    fetchUsers(page);
}

const onClearSearch = () => {
    for (const key in filters) {
        const { value, type } = filters[key];
        filters[key].value = null;
    }

    fetchUsers();
}

const openDialog = (dialogType, data = null) => {
    user.value = null

    user.value = data ? {...data} : null;
    dialogVisible[dialogType] = true;
}

const onCloseDialog = async () => {
    await fetchUsers();
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString(locale) : '—';

</script>

<template>
    <section class="row g-4">
        <div class="col-12">
            <div class="page-header">
                <div>
                    <h1 class="page-title-h mb-2">👥 {{ $t('messages.title_manage_users') }}</h1>
                    <p class="page-subtitle">{{ $t('messages.registered_users') }}</p>
                </div>
                <Button
                    :label="isMobile ? '' : $t('messages.btn_new_user')"
                    icon="pi pi-plus"
                    @click="openDialog('form')"
                    size="small"
                />
            </div>
        </div>

        <div class="col-12">
            <Card>
                <template #content>
                    <form @submit.prevent="fetchUsers()" class="row mb-4">
                        <div class="col-12 col-md-4 mb-3">
                            <InputText
                                v-model="filters.keyword.value" 
                                :placeholder="$t('messages.placeholder_search_user')" 
                                fluid
                            />
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <Select
                                v-model="filters.plan_id.value"
                                optionLabel="name"
                                optionValue="id"
                                :options="plans"
                                :placeholder="$t('messages.placeholder_plan')"
                                :loading="loadingPlans"
                                class="w-100"
                            >
                            </Select>
                        </div>
                        <div class="col-12 col-md-2 mb-3">
                            <Select
                                v-model="filters.account_status.value"
                                optionLabel="name"
                                optionValue="code"
                                :options="[
                                    { name: $t('messages.status_active'), code: 1 },
                                    { name: $t('messages.status_inactive'), code: 0 },
                                ]"
                                placeholder="Status"
                                class="w-100"
                            />
                        </div>
                        <div class="col-12 col-md-3">
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

                    <UsersTableSkeleton v-if="loadingUsers"/>

                    <DataTable
                        v-if="!loadingUsers && users.length"
                        :value="users"
                        :lazy="true"
                        :totalRecords="pagination.total"
                        :first="(currentPage - 1) * itemsPerPage"
                        :rows="itemsPerPage"
                        :paginatorDropdown="true"
                        :rowsPerPageOptions="[5, 7, 10, 20, 50]"
                        @page="onPage"
                        paginator
                        scrollable
                        stripedRows
                    >
                        <Column field="name" :header="$t('messages.label_name')" style="min-width: 100px">
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
                        <Column>
                            <template #header>
                                <div class="fw-semibold text-center w-100">
                                    <span>Status</span>
                                </div>
                            </template>
                            <template #body="{ data }">
                                <div class="text-center">
                                    <Tag
                                        :severity="data.account_status ? 'success' : 'danger'"
                                        :value="data.account_status ? $t('messages.status_active') : $t('messages.status_inactive')"
                                        style="font-size: 12px; padding: 2px 6px;"
                                    />
                                </div>
                            </template>
                        </Column>
                        <Column :header="$t('messages.label_plan')">
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
                        <Column :header="$t('messages.label_register')" class="d-none d-xl-table-cell" style="width:120px">
                            <template #body="{ data }">
                                <span class="small">{{ formatDate(data.created_at) }}</span>
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
                                        icon="pi pi-trash" 
                                        variant="text" 
                                        aria-label="Filter" 
                                        severity="danger"
                                        rounded
                                        @click="openDialog('delete', data)"
                                    />
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                    <EmptyData v-if="!loadingUsers && !users.length"/>
                </template>
            </Card>
        </div>

        <FormDialog
            v-model="dialogVisible.form"
            :plans="plans"
            :user="user"
            @saved="onCloseDialog"
        />
        
        <DeleteUserDialog
            v-model="dialogVisible.delete"
            :user="user"
            @deleted="onCloseDialog"
        />
    </section>
</template>