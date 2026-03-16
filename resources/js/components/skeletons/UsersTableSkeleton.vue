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

        <Column header="Nome" style="min-width:100px">
            <template #body>
                <div class="d-flex gap-2 align-items-center">
                    <Skeleton shape="circle" size="32px" />
                    <div class="d-flex flex-column gap-1">
                        <Skeleton width="120px" height="14px" />
                    </div>
                </div>
            </template>
        </Column>

        <Column header="E-mail" style="min-width:100px">
            <template #body>
                <div class="d-flex align-items-center gap-2">
                    <Skeleton shape="circle" size="28px" />
                    <Skeleton width="180px" height="14px" />
                </div>
            </template>
        </Column>

        <Column header="Status">
            <template #body>
                <div class="text-center">
                    <Skeleton width="60px" height="18px" />
                </div>
            </template>
        </Column>

        <Column header="Plano">
            <template #body>
                <Skeleton width="70px" height="18px" />
            </template>
        </Column>

        <Column header="Leads" style="width:80px">
            <template #body>
                <div class="text-center">
                    <Skeleton width="30px" height="14px" />
                </div>
            </template>
        </Column>

        <Column header="Cadastro" class="d-none d-xl-table-cell" style="width:120px">
            <template #body>
                <Skeleton width="80px" height="14px" />
            </template>
        </Column>

        <Column header="Ações" header-class="text-center">
            <template #body>
                <div class="d-flex justify-content-center gap-2">
                    <Skeleton shape="circle" size="30px" />
                    <Skeleton shape="circle" size="30px" />
                </div>
            </template>
        </Column>

    </DataTable>
</template>