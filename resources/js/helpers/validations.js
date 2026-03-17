import { showAlert } from "./alert";
import i18n from "@/i18n";

const t = i18n.global.t;

/**
 * Valida os campos obrigatórios em um formulário e atualiza os campos com erros.
 *
 * @param {Object} formData - Formulário com dados contendo valores de campo.
 * @param {Array<{id: string, label: string}>} requiredFields - Campos que devem ser preenchidos.
 * @param {Object} fieldErrorsRef - Objeto para armazenar mensagens de erro.
 *
 * @returns {boolean} Retorna Verdadeiro se todos os campos obrigatórios estiverem preenchidos, falso caso contrário.
 */
export function validateForm(formData, requiredFields, fieldErrorsRef) {
    let isValid = true;
    const new_errors = {};

    for (const {id, label, empty_message} of requiredFields) {
        if (!formData[id]) {
            isValid = false;

            new_errors[id] = [
                empty_message || t('messages.required', { label })
            ];
        }
    }

    Object.assign(fieldErrorsRef, new_errors);

    if(!isValid) {
        const filteredErrors = Object.entries(fieldErrorsRef).reduce((acc, [key, value]) => {
            if (value !== null) acc[key] = value;
            return acc;
        }, {});

        showAlert('error', {
            errors: filteredErrors
        }, 4000);
    }

    return isValid;
}