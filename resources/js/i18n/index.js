import { createI18n } from 'vue-i18n'

import ptMessages from './locales/pt/messages'

import enMessages from './locales/en/messages'

const messages = {
    pt: {
        messages: ptMessages,
    },
    en: {
        messages: enMessages,
    }
}

const savedLocale = localStorage.getItem('locale')

const browserLocale = navigator.language.split('-')[0]

const locale = savedLocale || (['pt','en'].includes(browserLocale) ? browserLocale : 'en')

export default createI18n({
    legacy: false,
    locale: locale,
    fallbackLocale: 'en',
    messages
})