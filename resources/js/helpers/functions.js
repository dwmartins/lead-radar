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