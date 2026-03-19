<script setup>
import Skeleton from 'primevue/skeleton';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

const props = defineProps({
    rows: {
        type: Number,
        default: 7
    }
});

const skeletonRows = Array.from({ length: props.rows });
</script>

<template>
    <DataTable :value="skeletonRows" stripedRows scrollable class="mt-2">

        <Column header="#" style="width:60px">
            <template #body>
                <Skeleton width="30px" height="14px" />
            </template>
        </Column>

        <Column :header="$t('messages.label_user')" style="min-width:100px">
            <template #body>
                <div class="d-flex flex-column gap-1">
                    <Skeleton width="160px" height="14px" />
                    <Skeleton width="100px" height="12px" />
                </div>
            </template>
        </Column>

        <Column :header="$t('messages.label_plan')">
            <template #body>
                <Skeleton width="70px" height="18px" />
            </template>
        </Column>

        <Column header="Status">
            <template #body>
                <div class="text-center">
                    <Skeleton width="80px" height="18px" />
                </div>
            </template>
        </Column>

        <Column :header="$t('messages.label_billing_cycle')">
            <template #body>
                <Skeleton width="90px" height="14px" />
            </template>
        </Column>

        <Column :header="$t('messages.label_renewal')">
            <template #body>
                <div class="text-center">
                    <Skeleton width="80px" height="14px" />
                </div>
            </template>
        </Column>

        <Column>
            <template #header>
                <span class="fw-semibold text-center w-100">{{ $t('messages.label_actions') }}</span>
            </template>
            <template #body>
                <div class="d-flex justify-content-center gap-2">
                    <Skeleton shape="circle" size="30px" />
                    <Skeleton shape="circle" size="30px" />
                </div>
            </template>
        </Column>

    </DataTable>
</template>