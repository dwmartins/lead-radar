import { defineStore } from "pinia";
import { computed, reactive, ref } from "vue";

export const useAuthStore = defineStore('auth', () => {
    const user = reactive({
        id: null,
        name: null,
        last_name: null,
        email: null,
        role: null,
        plan_id: null,
        email_verified_at: null,
    });

    const isLoggedIn = computed(() => !!user.value);

    /**
     * Retorna os dados do usuário autenticado
     */
    function getUser() {
        return user;
    }

    /**
     * Retorna se o usuário está autenticado.
     * @returns {boolean}
     */
    function isAuthenticate() {
        return isLoggedIn;
    }

    /**
     * Atualiza os dados do usuário com as informações fornecidas.
     * @param {Object} data - Objeto contendo novos dados do usuário.
     */
    function update(data) {
        if (!data || typeof data !== 'object') return;

        Object.keys(user).forEach(key => {
            user[key] = data[key];
        });
    }

    /**
     * Atualiza o avatar do usuário
     * 
     * @param {String} avatar 
     * @param {String} avatar_url 
     */
    function updateAvatar(avatar, avatar_url) {
        user.avatar = avatar;
        user.avatar_url = avatar_url;
    }

    /**
     * Limpa todos os dados do usuário, definindo-os como nulos, e redefine o status de login.
     */
    function clean() {
        Object.keys(user).forEach(key => {
            user[key] = null;
        });
    }

    /**
     * Retorna a função do usuário com a primeira letra maiúscula.
     * @returns {string} A função de usuário.
     */
    function getRole() {
        return user.role.charAt(0).toUpperCase() + user.role.slice(1)
    }

    return {
        user,
        getUser,
        isAuthenticate,
        update,
        updateAvatar,
        clean,
        getRole
    }
});