<script setup>
import i18n from "@/i18n";
import { computed, reactive, ref, watch } from "vue";
import { getLocale } from "@/helpers/locale";
import Dialog from "primevue/dialog";
import Select from "primevue/select";
import DatePicker from "primevue/datepicker";
import Button from "primevue/button";
import Textarea from "primevue/textarea";
import { validateForm } from "@/helpers/validations";
import { formatDateTimeToUTC, isFutureDate, isPastDate } from "@/helpers/date";
import { showAlert } from "@/helpers/alert";
import subscriptionService from "@/services/subscription.service";
import { Divider } from "primevue";

const t = i18n.global.t;
const dateFormat = getLocale() === 'pt' ? 'dd/mm/yy' : 'mm/dd/yy';

const props = defineProps({
    modelValue: Boolean,
    plans: Array,
    users: Array
});

const emit = defineEmits(['update:modelValue', 'saved']);

const visible = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

const getDefaultSubscription = () => ({
    user_id: null,
    plan_id: null,
    plan_price_id: null,
    status: 'active',
    expires_at: null,
    note: null,

    payment_status: null,
    payment_method: null,
    payment_paid_at: null
});

const getDefaultUser = () => ({
    id: null,
    name: '',
    email: '',
    last_name: '',
    full_name: '',
    avatar_url: ''
});

const subscription = reactive(getDefaultSubscription());
const saving       = ref(false);
const fieldErrors  = reactive({});

const user         = reactive(getDefaultUser());
const plan         = ref(false);
const prices       = ref(false);

const subscription_status = [
    { name: t('messages.subscription_active'),   code: 'active' },
    { name: t('messages.subscription_canceled'), code: 'canceled' },
    { name: t('messages.subscription_expired'),  code: 'expired' },
    { name: t('messages.subscription_pending'),  code: 'pending' },
];

const resetForm = () => {
    Object.assign(subscription, getDefaultSubscription());
    Object.assign(user, getDefaultUser());
    Object.keys(fieldErrors).forEach(key => delete fieldErrors[key]);

    plan.value = null;
    prices.value = null;
}

const setUser = (event) => {
    const selectedUser = props.users.find(user => user.id === event.value);
    Object.keys(user).forEach(key => user[key] = selectedUser[key]);
}

const formatPrice = (price, currency) => {
    return new Intl.NumberFormat(getLocale(), {
        style: 'currency',
        currency: currency
    }).format(price);
};

const setPlan = (event) => {
    prices.value = null;
    fieldErrors.plan_id = null;

    const selectedPlan = props.plans.find(plan => plan.id === event.value);

    prices.value = (selectedPlan?.prices || []).map(price => ({
        ...price,
        label: `${formatPrice(price.price, price.currency)} (${price.currency})`
    }));
}

const setPrice = (event) => {
    subscription.expires_at = null;
    const priceSelected = prices.value.find(price => price.id === event.value);

    const expiresAt = new Date();
    expiresAt.setMonth(expiresAt.getMonth() + priceSelected.interval_count);

    subscription.expires_at = expiresAt;
    fieldErrors.expires_at = null;
}

const countDescription = () => {
    if(subscription.note.length > 255) {
        subscription.note = subscription.note.substring(0, 255);
    }
}

const onSubmit = async () => {
    const requiredFields = [
        {id: 'user_id', label: t('messages.label_user')},
        {id: 'plan_id',   label: t('messages.label_plan')},
        {id: 'plan_price_id',   label: t('messages.label_currency')},
        {id: 'status',    label: 'Status'},
        {id: 'expires_at',    label: t('messages.label_renewal')},

        {id: 'payment_status', label: t('messages.label_payment_status')},
    ];

    if(subscription.payment_status === 'paid') {
        requiredFields.push(
            {id: 'payment_method', label: t('messages.label_payment_method')},
            {id: 'payment_paid_at', label: t('messages.label_payment_paid_at')},
        );
    }

    if(!validateForm(subscription, requiredFields, fieldErrors)) return; 

    if(isPastDate(subscription.expires_at)) {
        showAlert('error', t('messages.renewal_date_past'));
        return;
    }

    if(subscription.payment_paid_at && isFutureDate(subscription.payment_paid_at)) {
        showAlert('error', t('messages.payment_date_future'));
        return;
    }

    try {
        saving.value = true;
        const payload = {
            ...subscription,
            expires_at: formatDateTimeToUTC(subscription.expires_at),
            payment_paid_at: subscription.payment_paid_at ? formatDateTimeToUTC(subscription.payment_paid_at) : null
        }

        const response = await subscriptionService.create(payload);
        
        showAlert('success', response.message);
        emit('saved', response.subscription);
        visible.value = false;
    } catch (error) {
        showAlert('error', error.response?.data);
    } finally {
        saving.value = false;
    }
}

watch(() => props.modelValue, (opened) => {
    if(!opened) return;

    resetForm();
});
</script>

<template>
    <Dialog
        v-model:visible="visible"
        modal
        :style="{ width: '45rem' }"
        :draggable="false"
        :header="$t('messages.title_dialog_new_subscription')"
    >

        <form @submit.prevent="onSubmit" id="addSubscription" class="row g-4">
            <div v-if="user.id" class="col-12">
                <div class="card border-0 bg-light p-3">
                    <div class="d-flex align-items-center gap-3">
                        <img
                            :src="user?.avatar_url"
                            class="rounded-circle"
                            width="48"
                            height="48"
                        />
                        <div>
                            <div class="fw-semibold">
                                {{ user?.full_name }}
                            </div>
                            <small class="text-muted">
                                {{ user?.email }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
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
                        @change="fieldErrors.plan_id = null, setPlan($event)"
                        class="w-100"
                        :placeholder="$t('messages.placeholder_select_plan')"
                    />
                </div>
            </div>

            <div class="col-md-4">
                <div class="field">
                    <label>
                        {{ $t('messages.label_currency') }}
                    </label>

                    <Select
                        v-model="subscription.plan_price_id"
                        :options="prices"
                        optionLabel="label"
                        optionValue="id"
                        :invalid="!!fieldErrors.plan_price_id"
                        @change="fieldErrors.plan_price_id = null, setPrice($event)"
                        fluid
                        :placeholder="$t('messages.label_currency')"
                        :disabled="!subscription.plan_id"
                    />
                </div>
            </div>

            <div class="col-md-3">
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
                        {{ $t('messages.label_renewal') }}
                    </label>
                    <DatePicker
                        v-model="subscription.expires_at"
                        :dateFormat="dateFormat"
                        showIcon 
                        fluid 
                        iconDisplay="input" 
                        :placeholder="$t('messages.placeholder_renewal_date')"
                        :invalid="!!fieldErrors.expires_at"
                        @update:modelValue="fieldErrors.expires_at = null"
                    />
                </div>
            </div>

            <div class="col-md-6">
                <div class="field">
                    <label>
                        {{ $t('messages.label_user') }}
                    </label>
                    <Select
                        v-model="subscription.user_id"
                        :options="props.users"
                        optionLabel="full_name"
                        optionValue="id"
                        filter
                        :invalid="!!fieldErrors.user_id"
                        @change="fieldErrors.user_id = null, setUser($event)"
                        :placeholder="$t('messages.placeholder_select_user')"
                        class="w-100"
                    />
                </div>
            </div>

            <div class="col-12">
                <Divider/>
            </div>

            <div class="col-md-4">
                <div class="field">
                    <label>
                        {{ $t('messages.label_payment_status') }}
                    </label>
                    <Select
                        v-model="subscription.payment_status"
                        :options="[
                            {label: $t('messages.payment_status_pending'), code: 'pending'},
                            {label: $t('messages.payment_status_paid'), code: 'paid'},
                        ]"
                        optionLabel="label"
                        optionValue="code"
                        :invalid="!!fieldErrors.payment_status"
                        @change="fieldErrors.payment_status = null"
                        class="w-100"
                    />
                </div>
            </div>

            <div class="col-md-4">
                <div class="field">
                    <label>
                        {{ $t('messages.label_payment_method') }}
                    </label>
                    <Select
                        v-model="subscription.payment_method"
                        :options="[
                            {label: $t('messages.payment_method_credit_card'), code: 'credit_card'},
                            {label: $t('messages.payment_method_pix'), code: 'pix'},
                            {label: $t('messages.payment_method_bank_slip'), code: 'bank_slip'},
                            {label: $t('messages.payment_method_debit_card'), code: 'debit_card'},
                        ]"
                        optionLabel="label"
                        optionValue="code"
                        :invalid="!!fieldErrors.payment_method"
                        @change="fieldErrors.payment_method = null"
                        class="w-100"
                    />
                </div>
            </div>

            <div class="col-md-4">
                <div class="field">
                    <label>
                        {{ $t('messages.label_payment_paid_at') }}
                    </label>
                    <DatePicker
                        v-model="subscription.payment_paid_at"
                        :dateFormat="dateFormat"
                        showIcon 
                        fluid 
                        iconDisplay="input" 
                        :invalid="!!fieldErrors.payment_paid_at"
                        @update:modelValue="fieldErrors.payment_paid_at = null"
                    />
                </div>
            </div>

            <div class="col-12">
                <div class="field">
                    <label>{{ $t('messages.label_note') }}</label>
                    <div class="position-relative mt-2">
                        <Textarea 
                            v-model="subscription.note" 
                            @input="countDescription" 
                            autoResize 
                            rows="5" 
                            cols="30" 
                            maxlength="500" 
                            class="w-100" 
                            id="description" 
                            :placeholder="$t('messages.placeholder_subscription_note')"
                        />
                        <span class="text-muted fs-7">{{ subscription.note?.length ?? 0}} / 255</span>  
                    </div>
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
                :label="saving ? $t('messages.btn_loading') : $t('messages.btn_save')" 
                icon="pi pi-check" 
                :loading="saving"
                type="submit"
                form="addSubscription" 
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