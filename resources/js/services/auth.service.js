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
     * Atualiza o usuário autenticado.
     * @param {Object} user 
     */
    setUserAuthenticate(user) {
        const authStore = useAuthStore();
        authStore.update(user);
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
        try {
            const response = await axios.post('/logout');
            this.clearAuth();
            return response;
        } catch (error) {
            throw error;
        }
    }
}