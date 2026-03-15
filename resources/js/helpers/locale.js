/**
 * Obtém o locale atual da aplicação.
 * 
 * @returns {string} O locale atual da aplicação ("pt" ou "en").
 */
export function getLocale() {
    const savedLocale = localStorage.getItem('locale');
    const browserLocale = navigator.language.split('-')[0];

    return savedLocale || (browserLocale === 'pt' ? 'pt' : 'en');
}

/**
 * @param {string} locale - O locale que será salvo ("pt" ou "en").
 */
export function setLocale(locale) {
    localStorage.setItem('locale', locale);
}

/**
 * Verifica se o locale atual da aplicação é português.
 *
 * @returns {boolean}
 */
export function isPortuguese() {
    return getLocale() === 'pt';
}