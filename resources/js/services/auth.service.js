import { API_URL } from "@/helpers/constants";
import { useAuthStore } from "@/stores/authStore";
import axios from "axios"

export default {
    /**
     * Valida no backend se o usuário realmente está autenticado.
     * @returns {Promise<{ message: string, is_valid: boolean, user: Object|null }>}
     */
    async validate() {
        try {
            await axios.get('/sanctum/csrf-cookie', {
                withCredentials: true
            });

            const { data } = await axios.get(`${API_URL}/auth/validate`, {
                withCredentials: true
            });

            this.setUserAuthenticate(data.user);
            this.addSettingsToAxios();
            return data;
        } catch (error) {
            this.clearAuth();

            return { 
                message: null,
                is_valid: false, 
                user: null 
            };
        }
    },
    
    /**
     * Realiza o login do usuário.
     *
     * @param {Object} userData
     * @param {string} userData.email - E-mail do usuário
     * @param {string} userData.password - Senha do usuário
     * @param {boolean} userData.remember_me - Mantém a sessão ativa
     *
     * @returns {Promise<{ message: string, user: Object|null }>}
     */
    async login(userData) {
        try {
            // Gerar o CSRF antes do login
            await axios.get(`/sanctum/csrf-cookie`, {
                withCredentials: true
            });

            const { data } = await axios.post(`${API_URL}/login`, userData);

            localStorage.setItem('last_email', userData.email);

            this.setUserAuthenticate(data.user);
            this.addSettingsToAxios();
            return data;
        } catch (error) {
            this.clearAuth();
            throw error;
        }
    },

    /**
     * Atualiza o usuário autenticado.
     * @param {Object} user 
     */
    setUserAuthenticate(user) {
        const authStore = useAuthStore();
        authStore.update(user);
        authStore.setIsAuthenticate(true);
    },

    /**
     * Limpa todos os dados relacionado ao usuário autenticado.
     * @returns {void}
     */
    clearAuth() {
        const authStore = useAuthStore();
        authStore.clean();
    },

    /**
     * Configura o axios para sempre enviar withCredentials
     * @returns {void}
     */
    addSettingsToAxios() {
        axios.defaults.withCredentials = true;
    },

    /**
     * Realiza o logou e limpa todos os dados relacionado ao usuário autenticado.
     * 
     * @returns {void}
     */
    async logout() {
        const { data } = await axios.post(`${API_URL}/logout`);
        this.clearAuth()
        return data
    }
}