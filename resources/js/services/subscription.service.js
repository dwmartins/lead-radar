import { API_URL } from "@/helpers/constants";
import axios from "axios";

export default {
    /**
     * Lista as subscriptions com paginação e filtros opcionais.
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
     * - data: Lista de subscriptions
     * - pagination: Informações de paginação
     */
    async index(page = 1, perPage = 7, filters = {}) {
        const response = await axios.get(`${API_URL}/subscription`, {
            params: {
                page,
                perPage,
                ...filters
            }
        });

        return response.data;
    },
}