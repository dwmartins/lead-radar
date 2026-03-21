<script setup>
import i18n from "@/i18n";
import { computed, reactive, ref, watch } from "vue";
import { getLocale } from "@/helpers/locale";
import Dialog from "primevue/dialog";
import Tag from "primevue/tag";
import Button from "primevue/button";
import Select from "primevue/select";
import DatePicker from "primevue/datepicker";
import { formatDateTimeToUTC, isPastDate, parseDateTime } from "@/helpers/date";
import AlertBox from "@/components/ui/AlertBox.vue";
import { validateForm } from "@/helpers/validations";
import { showAlert } from "@/helpers/alert";
import subscriptionService from "@/services/subscription.service";

const t = i18n.global.t;

const dateFormat = getLocale() === 'pt' ? 'dd/mm/yy' : 'mm/dd/yy';

const props = defineProps({
    modelValue: Boolean,
    subscription: Object,
    plans: Array,
    users: Array
});

const emit = defineEmits(['update:modelValue', 'saved']);

const visible = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

const getDefaultSubscription = () => ({
    id: null,
    user_id: null,
    plan_id: null,
    status: 'active',
    started_at: null,
    expires_at: null,
    canceled_at: null,
    trial_ends_at: null,
    billing_cycle: 'monthly',
    plan: null,
    user: null
});

const subscription = reactive(getDefaultSubscription());
const isUpdate     = computed(() => !!props.subscription?.id);
const saving       = ref(false);
const fieldErrors  = reactive({});

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

const resetForm = () => {
    Object.assign(subscription, getDefaultSubscription());
    Object.keys(fieldErrors).forEach(key => delete fieldErrors[key]);
}

const fillForm = (data) => {
    if(!data) return;

    const defaults = getDefaultSubscription();

    Object.assign(subscription, {
        ...defaults,
        ...data,
        plan: {
            ...defaults.plan,
            ...data.plan
        },
        user: {
            ...defaults.user,
            ...data.user
        }
    });

    subscription.expires_at = parseDateTime(data.expires_at)
}

const onSubmit = async () => {
    const requiredFields = [
        {id: 'plan_id',   label: t('messages.label_plan')},
        {id: 'status',    label: 'Status'},
        {id: 'billing_cycle', label: t('messages.billing_cycle')},
        {id: 'user_id', label: t('messages.label_user')}
    ];

    if(!validateForm(subscription, requiredFields, fieldErrors)) return; 

    if(isPastDate(subscription.expires_at)) {
        showAlert('error', t('messages.renewal_date_past'));
        return;
    }

    try {
        saving.value = true;
        const payload = {
            ...subscription,
            expires_at: formatDateTimeToUTC(subscription.expires_at)
        }

        const response = isUpdate.value
            ? await subscriptionService.update(payload)
            : await subscriptionService.create(payload);

        showAlert('success', response.message);
        emit('saved', response.subscription);
        visible.value = false;
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        saving.value = false;
    }
}

const setUser = (event) => {
    subscription.user = props.users.find(user => user.id === event.value);
}

watch(() => props.modelValue, (opened) => {
    if(!opened) return;

    resetForm();
    fillForm(props.subscription);
});

</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :style="{ width: '38rem' }"
        :draggable="false"
        :header="isUpdate ? $t('messages.title_dialog_edit_subscription') : $t('messages.title_dialog_new_subscription')"
    >
        <form @submit.prevent="onSubmit" id="subscriptionForm" class="row g-4">
            <div v-if="subscription.id" class="col-12">
                <div class="d-flex align-items-center gap-2">
                    <h3>#{{ subscription.id }}</h3>
                    <Tag
                        :severity="subscriptionStatusMap[subscription.status]"
                        :value="$t(`messages.subscription_${subscription.status}`)"
                    />
                </div>
                <span class="text-muted fs-7">
                    {{ $t('messages.placeholder_renewal') }}: {{ new Date(subscription.expires_at).toLocaleDateString(getLocale(), { dateStyle: 'long' }) }}
                </span>
            </div>

            <div v-if="subscription.user" class="col-12">
                <div class="card border-0 bg-light p-3">
                    <div class="d-flex align-items-center gap-3">
                        <img
                            :src="subscription.user?.avatar_url"
                            class="rounded-circle"
                            width="48"
                            height="48"
                        />
                        <div>
                            <div class="fw-semibold">
                                {{ subscription.user?.full_name }}
                            </div>
                            <small class="text-muted">
                                {{ subscription.user?.email }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="field">
                    <label>
                        {{ $t('messages.label_plan') }}
                    </label>

                    <Select
                        v-model="subscription.plan_id"
                        :options="props.plans"
                        optionLabel="name"
                        optionValue="id"
                        :invalid="!!fieldErrors.plan_id"
                        @change="fieldErrors.plan_id = null"
                        class="w-100"
                        :placeholder="$t('messages.placeholder_select_plan')"
                    />

                    <small class="text-muted" v-if="subscription.plan?.monthly_search_limit">
                        {{ subscription.plan.monthly_search_limit }} buscas/mês
                    </small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="field">
                    <label>
                        Status
                    </label>

                    <Select
                        v-model="subscription.status"
                        :options="subscription_status"
                        optionLabel="name"
                        optionValue="code"
                        :invalid="!!fieldErrors.status"
                        @change="fieldErrors.status = null"
                        class="w-100"
                    />
                </div>
            </div>

            <div class="col-md-6">
                <div class="field">
                    <label>
                        {{ $t('messages.billing_cycle') }}
                    </label>

                    <Select
                        v-model="subscription.billing_cycle"
                        :options="[
                            { label: $t(`messages.cycle_monthly`), value: 'monthly' },
                            { label: $t(`messages.cycle_semiannual`), value: 'semiannual' },
                            { label: $t(`messages.cycle_yearly`), value: 'yearly' }
                        ]"
                        optionLabel="label"
                        optionValue="value"
                        :invalid="!!fieldErrors.billing_cycle"
                        class="w-100"
                        @change="subscription.expires_at = null, fieldErrors.status = null"
                    />
                </div>
            </div>

            <div v-if="!isUpdate" class="col-md-6">
                <div class="field">
                    <label>
                        {{ $t('messages.label_user') }}
                    </label>
                    <Select
                        v-model="subscription.user_id"
                        :options="props.users"
                        optionLabel="full_name"
                        optionValue="id"
                        :invalid="!!fieldErrors.user_id"
                        @change="fieldErrors.user_id = null, setUser"
                        :placeholder="$t('messages.placeholder_select_user')"
                        class="w-100"
                    />
                </div>
            </div>

            <div class="col-12">
                <AlertBox v-if="isUpdate" type="warning">
                    {{ $t('messages.subscription_edit_hint') }}
                </AlertBox>
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
                form="subscriptionForm" 
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