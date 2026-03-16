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
     * @returns {Promise} Retorna a resposta da API com a lista de síndicos
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
    }
}