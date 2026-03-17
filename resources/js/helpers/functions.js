import i18n from "@/i18n";
import { showAlert } from "./alert";

const t = i18n.global.t;

/**
 * 
 * @param {string} itemName Nome do item a ser copiado
 * @param {*} item o item a ser copiado
 */
export async function copyItem(itemName, item){
    try {
        await navigator.clipboard.writeText(item);
        showAlert('success', t('messages.copied_to_clipboard', { item_name: itemName }), 1500);
    } catch (err) {
        showAlert('error', t('messages.error_copying_to_clipboard'));
    }
}

/**
 * Abre o WhatsApp do usuário
 * @param {object} user 
 */
export function openWhatsApp(user){
    if(!user.phone) return;
    
    const phone = user.phone.replace(/\D/g, '');
    const message = `${t('messages.hi')} ${user.name}!`;
    const url = `https://wa.me/55${phone}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

/**
 * 
 * @param {string} phone 
 * @returns {string} Telefone formatado 
 */
export function formatPhone(phone) {
    if (!phone) return "";

    const digits = phone.replace(/\D/g, "");

    if(digits.length === 11) {
        return digits.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
    }

    if(digits.length === 10) {
        return digits.replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
    }

    return digits;
}

/**
 * @param {String} str 
 * @returns {string} retorna a string com primeira letra maiúscula e os resto minuscula.
 */
export function capitalizeFirstLetter(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}
