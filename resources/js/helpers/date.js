import { getLocale } from "./locale";

const locale = getLocale();

/**
 * Converte uma data para o formato ISO (AAAA-MM-DD).
 *
 * @param {string|Date|null|undefined} date
 *
 * @returns {string|null}
 *  Data ISO (AAAA-MM-DD) ou nulo se a data for inválida.
 */
export function formatDateToUTC(date) {
    const d = new Date(date);
    if (isNaN(d.getTime())) return null;
    return d.toISOString().split("T")[0];
}

/**
 * Converte um datetime para o formato ISO (AAAA-MM-DD HH:mm:ss).
 *
 * @param {string|Date|null|undefined} datetime
 *
 * @returns {string|null}
 *  DateTime ISO (AAAA-MM-DD HH:mm:ss) ou nulo se o datetime for inválido.
 */
export function formatDateTimeToUTC(datetime) {
    const d = new Date(datetime);
    if (isNaN(d.getTime())) return null;
    return d.toISOString().slice(0, 19).replace('T', ' ');
}

/**
 * Formata a data para JavaScript
 *
 * @param {string|Date|null|undefined} date
 *
 * @returns {Date|null}
 */
export function parseDate(date) {
    if (date instanceof Date) {
        return date;
    }

    const [year, month, day] = date.split('-').map(Number);

    return new Date(year, month - 1, day);
}

/**
 * Formata o datetime para JavaScript
 *
 * @param {string|Date|null|undefined} datetime
 *
 * @returns {Date|null}
 */
export function parseDateTime(datetime) {
    if (datetime instanceof Date) {
        return datetime;
    }

    const dt = datetime.replace(' ', 'T');
    const d = new Date(dt);

    return isNaN(d.getTime()) ? null : d;
}

/**
 * Formata um datetime para string localizada
 *
 * @param {string|Date|null|undefined} date
 *
 * @returns {string}
 */
export function formatDateTime(date) {
    return new Intl.DateTimeFormat(locale, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: locale === 'en'
    }).format(new Date(date));
}

/**
 * Formata uma data para string localizada (sem horário)
 *
 * @param {string|Date|null|undefined} date
 *
 * @returns {string}
 */
export function formatDate(date) {
    return new Intl.DateTimeFormat(locale, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(new Date(date));
}

/**
 * Verifica se uma data está no passado (ignora horário)
 *
 * @param {string|Date|null|undefined} date
 *
 * @returns {boolean}
 */
export function isPastDate(date) {
    if (!date) return false;

    const inputDate = new Date(date);
    const today = new Date();

    inputDate.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    return inputDate < today;
};

/**
 * Verifica se uma data está no futuro (ignora horário)
 *
 * @param {string|Date|null|undefined} date
 *
 * @returns {boolean}
 */
export function isFutureDate(date) {
    if (!date) return false;

    const inputDate = new Date(date);
    const today = new Date();

    inputDate.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    return inputDate > today;
};