import { API_URL } from "@/helpers/constants"
import axios from "axios"

export default {
    /**
     * Lista os usuários com paginação e filtros opcionais.
     * 
     * @param {Number} page - Página atual (padrão: 1)
     * @param {Number} perPage - Quantidade de registros por página (padrão: 7)
     * @param {Object} filters - Filtros adicionais enviados como query params
     * 
     * @returns {Promise<{
     *   data: Object[],
     *   pagination: {
     *     current_page: number,
     *     per_page: number,
     *     total: number,
     *     last_page: number
     *   }
     * }>}
     * Retorna:
     * - data: Lista de usuários
     * - pagination: Informações de paginação
     */
    async index(page = 1, perPage = 7, filters = {}) {
        const response = await axios.get(`${API_URL}/user`, {
            params: {
                page,
                perPage,
                ...filters
            }
        });

        return response.data;
    },

    /**
     * Registra um novo usuário.
     * 
     * @param {Object} data 
     * @returns {Promise<{ message: string, user: Object }>}
     * Retorna um objeto contendo:
     * - message: Mensagem de sucesso da API
     * - user: Usuário criado
     */
    async create(data) {
        const response = await axios.post(`${API_URL}/user`, data);
        return response.data;
    },

    /**
     * Atualiza um usuário existente.
     * 
     * @param {Object} data 
     * @returns {Promise<{ message: string, user: Object }>}
     * Retorna um objeto contendo:
     * - message: Mensagem de sucesso da API
     * - user: Usuário atualizado
     */
    async update(data) {
        const response = await axios.put(`${API_URL}/user`, data);
        return response.data;
    },
    
    /**
     * Exclui um usuário
     * 
     * @param {Number} user_id 
     * @returns {Promise<{ message: string}>}
     * Retorna um objeto contendo:
     * - message: Mensagem de sucesso da API
     */
    async delete(user_id) {
        const response = await axios.delete(`${API_URL}/user/${user_id}`);
        return response.data;
    }
}