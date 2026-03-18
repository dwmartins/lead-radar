<script setup>
import { computed, reactive, ref, watch } from "vue";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import { showAlert } from "@/helpers/alert";
import userService from "@/services/user.service";

const props = defineProps({
    modelValue: Boolean,
    user: {
        type: Object,
    }
});

const emit = defineEmits(['update:modelValue', 'deleted']);

const visible = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

const deleting = ref(false);

const getDefaultUser = () => ({
    id: null,
    name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    account_status: true
});

const fillForm = (data) => {
    if(!data) return;

    Object.assign(form, {
        ...getDefaultUser(),
        ...data
    });
}

const form = reactive(getDefaultUser());

const resetForm = () => {
    Object.assign(form, getDefaultUser());
}

const onSubmit = async () => {
    try {
        deleting.value = true;

        const response = await userService.delete(form.id);
        showAlert('success', response.message);
        emit('deleted');
        visible.value = false;
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        deleting.value = false;
    }
}

watch(() => props.modelValue, (opened) => {
    if(!opened) return;

    resetForm();
    fillForm(props.user);
});
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :style="{ width: '32rem' }"
        :draggable="false"
        :header="$t('messages.title_dialog_delete_user')"
    >
        <div class="d-flex align-items-center gap-3 mb-3 mt-1">
            <i class="pi pi-info-circle text-danger" style="font-size: 1.8rem"></i>
            <p class="m-0">{{ $t('messages.confirm_delete_user', {name: form.name}) }}</p>
        </div>

        <template #footer>
            <Button 
                :label="$t('messages.btn_cancel')" 
                severity="secondary"
                variant="outlined"
                :disabled="deleting" 
                @click="visible = false" 
            />

            <Button 
                :label="$t('messages.btn_confirm_delete')"
                icon="pi pi-trash"
                severity="danger"
                :loading="deleting"
                @click="onSubmit()" 
            />
        </template>
    </Dialog>
</template>