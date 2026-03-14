import { Notyf } from "notyf";
import i18n from "@/i18n";

const t = i18n.global.t;

const notyf = new Notyf({
    duration: 4000,
    position: { x: "right", y: "top" },
});

/**
 * Normaliza mensagens de erro ou string para exibição.
 * @param {*} input - Objeto de erro, string ou undefined
 * @returns {string} - Mensagem pronta para exibir
 */
function normalizeMessage(input) {
    const general_error = t('messages.general_error');

    if (!input) return general_error;

    if (input.errors && typeof input.errors === "object") {
        return Object.values(input.errors)
            .flat()
            .map(msg => `• ${msg}`)
            .join("<br>");
    }

    if (input.message) return input.message;

    if (typeof input === "string") return input;

    return general_error;
}

/**
 * Exibe alerta na tela usando Notyf.
 * @param {"success"|"error"} type - Tipo de alerta
 * @param {*} message - Mensagem ou objeto de erro
 * @param {number} [duration] - Tempo em ms para exibir
 */
export function showAlert(type = "error", message, duration) {
    const finalMessage = normalizeMessage(message);

    const config = {
        message: finalMessage,
        ...(duration && { duration })
    };

    return type === "success"
        ? notyf.success(config)
        : notyf.error(config);
}
