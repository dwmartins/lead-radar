/** Nome do aplicativo */
export const APP_NAME = import.meta.env.VITE_APP_NAME;

/** URL base da API */
export const API_URL = '/api';

/** Logos e avatar padrão */
export const defaultLogoDark = new URL('@assets/images/logo_dark.webp', import.meta.url).href;
export const defaultLogoLight = new URL('@assets/images/logo_light.webp', import.meta.url).href;
export const defaultAvatar = new URL('@assets/images/avatar.webp', import.meta.url).href;
export const emptyImg = new URL('@assets/svg/empty.svg', import.meta.url).href;

/** Fuso horário padrão */
export const DEFAULT_TIMEZONE = 'America/Sao_Paulo';
