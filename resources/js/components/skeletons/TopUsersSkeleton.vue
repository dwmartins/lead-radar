<script setup>
import Skeleton from 'primevue/skeleton';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

const props = defineProps({
    rows: {
        type: Number,
        default: 8
    }
});

const skeletonRows = Array.from({ length: props.rows });
</script>

<template>
    <DataTable :value="skeletonRows" stripedRows class="mt-2">
        <Column header="#" style="width:50px">
            <template #body>
                <Skeleton width="20px" height="20px" />
            </template>
        </Column>

        <Column :header="$t('messages.label_user')" style="min-width:100px">
            <template #body>
                <div class="d-flex gap-2 align-items-center">
                    <Skeleton shape="circle" size="32px" />
                    <Skeleton width="120px" height="14px" />
                </div>
            </template>
        </Column>

        <Column :header="$t('messages.label_email')" style="min-width:100px">
            <template #body>
                <Skeleton width="180px" height="14px" />
            </template>
        </Column>

        <Column :header="$t('messages.label_plan')">
            <template #body>
                <Skeleton width="80px" height="20px" />
            </template>
        </Column>

        <Column :header="$t('messages.leads_month')" style="width:140px">
            <template #body>
                <Skeleton width="70px" height="14px" />
            </template>
        </Column>

        <Column header="" style="width:80px">
            <template #body>
                <Skeleton shape="circle" size="25px" />
            </template>
        </Column>
    </DataTable>
</template>