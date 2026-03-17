<script setup>
import { ref, watch, computed } from 'vue';
import { parsePhoneNumberFromString, getCountries, getCountryCallingCode } from 'libphonenumber-js';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';

const props = defineProps({
    modelValue: String,
    size: String
});

const emit = defineEmits(['update:modelValue']);

const selectedCountry = ref('BR');
const phone = ref('');

const countryOptions = getCountries().map(code => ({
    label: `${code} (+${getCountryCallingCode(code)})`,
    value: code,
    flag: code.toLowerCase()
}));

watch([phone, selectedCountry], () => {
    if (!phone.value) {
        emit('update:modelValue', null);
        return;
    }

    try {
        const parsed = parsePhoneNumberFromString(phone.value, selectedCountry.value);

        if (parsed) {
            emit('update:modelValue', parsed.number); // 🔥 E.164
        }
    } catch (e) {
        emit('update:modelValue', phone.value);
    }
});

watch(() => props.modelValue, (val) => {
    if (!val) return;

    const parsed = parsePhoneNumberFromString(val);

    if (parsed) {
        selectedCountry.value = parsed.country;
        phone.value = parsed.nationalNumber;
    }
}, { immediate: true });

</script>

<template>
    <div class="d-flex gap-2">
        <Select
            v-model="selectedCountry"
            :options="countryOptions"
            optionLabel="label"
            optionValue="value"
            style="width: 75px"
            :size="props.size"
            filter
        >
            <template #option="slotProps">
                <div class="flex align-items-center gap-2">
                    <img
                        :src="`https://flagcdn.com/w20/${slotProps.option.flag}.webp`"
                        width="20"
                        class="me-1"
                    />
                    <span>{{ slotProps.option.label }}</span>
                </div>
            </template>

            <template #value="slotProps">
                <div v-if="slotProps.value" class="flex align-items-center gap-2">
                    <img
                        :src="`https://flagcdn.com/w20/${slotProps.value.toLowerCase()}.webp`"
                        width="20"
                        class="me-1"
                    />
                </div>
                <span v-else>Select</span>
            </template>
        </Select>

        <InputText
            v-model="phone"
            class="w-full"
            :size="props.size"
            fluid
        />
    </div>
</template>