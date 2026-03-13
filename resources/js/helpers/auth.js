import { useAuthStore } from "@/stores/authStore";

/** Papéis de usuário */
export const ROLE_ADMIN = 'admin';
export const ROLE_USER = 'user';

/** Definição legível dos papéis */
export const ROLE_DEFINITIONS = {
    admin: 'Administrador',
    suporte: 'Usuário',
}

/**
 * Retorna o nome legível do papel do usuário atual.
 * @returns {string} - Ex: "Administrador"
 */
export function showUserRole() {
    const userStore = useAuthStore();
    const role = userStore.user.role;
    return ROLE_DEFINITIONS[role];
}

/**
 * Verifica se o usuário atual é Administrador.
 * @returns {boolean}
 */
export function isAdmin() {
    const { user } = useAuthStore();
    return user?.role === ROLE_ADMIN;
}

/**
 * Verifica se é um usuário comum.
 * @returns {boolean}
 */
export function isUser() {
    const { user } = useAuthStore();
    return user?.role === ROLE_USER;
}