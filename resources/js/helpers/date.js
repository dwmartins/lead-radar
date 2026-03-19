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
export function safeISOToDate(date) {
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
export function safeISOToDateTime(datetime) {
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

export function formateDateTime(date) {
    return new Intl.DateTimeFormat(locale, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: locale === 'en'
    }).format(new Date(date));
}

export function formateDate(date) {
    return new Intl.DateTimeFormat(locale, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    }).format(new Date(date));
}