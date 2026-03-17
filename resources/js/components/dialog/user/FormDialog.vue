<script setup>
import { computed, reactive, ref, watch } from 'vue';
import i18n from "@/i18n";
import Dialog from 'primevue/dialog';
import Tag from 'primevue/tag';
import ToggleSwitch from 'primevue/toggleswitch';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Password from 'primevue/password';
import Button from 'primevue/button';
import PhoneInput from '@/components/ui/inputs/PhoneInput.vue';
import { validateForm } from '@/helpers/validations';
import { showAlert } from '@/helpers/alert';
import userService from '@/services/user.service';
import { capitalizeFirstLetter } from '@/helpers/functions';

const t = i18n.global.t;

const props = defineProps({
    modelValue: Boolean,
    plans: {
        type: Array,
        default: []
    },
    user: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['update:modelValue', 'saved']);

const plans       = ref(null);
const saving      = ref(false);
const fieldErrors = reactive({});
const roleOptions = [
    { label: t('messages.role_user'), value: 'user' }, 
    { label: t('messages.role_admin'), value: 'admin' }
];

const getDefaultUser = () => ({
    id: null,
    name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    role: 'user',
    account_status: true,
    plan_id: null
});

const form = reactive(getDefaultUser());

const visible = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

const isUpdate = computed(() => !!props.user?.id);

const resetForm = () => {
    Object.assign(form, getDefaultUser());
    Object.keys(fieldErrors).forEach(key => delete fieldErrors[key]);
}

const fillForm = (data) => {
    if(!data) return;

    Object.assign(form, {
        ...getDefaultUser(),
        ...data
    });
}

const onSubmit = async () => {
    const requiredFields = [
        {id: 'name',      label: t('messages.label_name')},
        {id: 'last_name', label: t('messages.label_last_name')},
        {id: 'email',     label: t('messages.label_email')},
    ];

    if(!isUpdate.value) {
        requiredFields.push({id: 'password',              label: t('messages.label_password')});
        requiredFields.push({id: 'password_confirmation', label: t('messages.label_confirm_password')});
    }

    if(form.role !== 'admin') {
        requiredFields.push({id: 'plan_id', label: t('messages.label_plan')});
    }

    if(!validateForm(form, requiredFields, fieldErrors)) return;

    if(form.password && form.password.length < 8) {
        showAlert('error', t('messages.password_min_length', { min: 8 }));
        return;
    }

    if(form.password || form.password_confirmation) {
        if(form.password !== form.password_confirmation) {
            showAlert('error', t('messages.password_mismatch'));
            return;
        }
    }

    try {
        saving.value = true;
        const payload = normalizeUser(form);

        if(payload.phone) {
            payload.phone = payload.phone
                .replace(/[^\d+]/g, '')
                .replace(/(?!^)\+/g, '');
        }

        const response = isUpdate.value
            ? await userService.update(payload)
            : await userService.create(payload);

        showAlert('success', response.message);
        emit('saved', response.user);
        visible.value = false;
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        saving.value = false;
    }
}

const normalizeUser = (data) => ({
    ...data,
    name: capitalizeFirstLetter(data.name),
    last_name: capitalizeFirstLetter(data.last_name),
});

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
        :style="{ width: '38rem' }"
        :draggable="false"
        :header="isUpdate ? $t('messages.title_dialog_edit_user') : $t('messages.title_dialog_new_user')"
    >
        <form @submit.prevent="onSubmit" id="userForm" class="row g-4">
            <div class="col-12 d-flex justify-content-end">
                <div class="d-flex align-items-center gap-2">
                    <Tag
                        :severity="form.account_status ? 'success' : 'danger'"
                        :value="form.account_status ? $t('messages.status_active') : $t('messages.status_inactive')" 
                    />
                    <ToggleSwitch v-model="form.account_status" />
                </div>
            </div>

            <div class="col-12">
                <h4>{{ $t('messages.section_personal_info') }}</h4>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>{{ $t('messages.label_name') }}</label>
                    <InputText 
                        v-model="form.name" 
                        :invalid="!!fieldErrors.name" 
                        @input="fieldErrors.name = null" 
                        fluid 
                    />
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>{{ $t('messages.label_last_name') }}</label>
                    <InputText 
                        v-model="form.last_name" 
                        :invalid="!!fieldErrors.last_name" 
                        @input="fieldErrors.last_name = null" 
                        fluid 
                    />
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>{{ $t('messages.label_email') }}</label>
                    <InputText 
                        v-model="form.email" 
                        :invalid="!!fieldErrors.email" 
                        @input="fieldErrors.email = null" 
                        fluid 
                    />
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>{{ $t('messages.label_optional_phone') }}</label>
                    <PhoneInput 
                        v-model="form.phone" 
                    />
                </div>
            </div>

            <div class="col-12">
                <h4>{{ $t('messages.section_access') }}</h4>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>{{ $t('messages.label_user_type') }}</label>
                    <Select 
                        v-model="form.role" 
                        :options="roleOptions" 
                        optionLabel="label" 
                        optionValue="value" 
                        fluid 
                        :invalid="!!fieldErrors.role"
                        @change="fieldErrors.role = null"
                    />
                </div>
            </div>

            <div class="col-12">
                <h4>{{ $t('messages.section_security') }}</h4>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>
                        {{ isUpdate 
                            ? $t('messages.label_new_optional_password') 
                            : $t('messages.label_password') 
                        }}
                    </label>
                    <Password
                        v-model="form.password"
                        toggleMask
                        :feedback="false"
                        fluid
                        :invalid="!!fieldErrors.password"
                        @change="fieldErrors.password = null"
                    />
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>{{ $t('messages.label_confirm_password') }}</label>
                    <Password
                        v-model="form.password_confirmation"
                        toggleMask
                        :feedback="false"
                        fluid
                        :invalid="!!fieldErrors.password_confirmation"
                        @change="fieldErrors.password_confirmation = null"
                    />
                </div>
            </div>

            <div class="col-12">
                <h4>{{ $t('messages.section_plan') }}</h4>
            </div>

            <div class="col-12 col-md-6">
                <div class="field">
                    <label>{{ $t('messages.label_plan') }}</label>
                    <Select 
                        v-model="form.plan_id" 
                        :options="props.plans" 
                        optionLabel="name" 
                        optionValue="id" 
                        fluid 
                        :invalid="!!fieldErrors.plan_id"
                        @change="fieldErrors.plan_id = null"
                        :placeholder="$t('messages.placeholder_select_plan')"
                    />
                </div>
            </div>
        </form>

        <template #footer>
            <Button
                :label="$t('messages.btn_cancel')"
                severity="secondary"
                outlined
                @click="visible = false" 
                :disabled="saving"
            />
            <Button 
                :label="saving ? $t('messages.btn_loading') : (isUpdate ? $t('messages.btn_update') : $t('messages.btn_save'))" 
                icon="pi pi-check" 
                :loading="saving"
                type="submit"
                form="userForm" 
            />
        </template>
    </Dialog>
</template>

<style scoped>
.field label {
    display: block;
    font-size: .8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--p-text-muted-color, #64748b);
    margin-bottom: .6rem;
}
</style>