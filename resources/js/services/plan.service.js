import { API_URL } from "@/helpers/constants"
import axios from "axios"

export default {
    /**
     * Busca todos os planos com suas respectivas features.
     * 
     * @returns {Promise<Array<Object>>} Lista de planos retornada pela API.
     */
    async index() {
        const response = await axios.get(`${API_URL}/plan`);
        return response.data;
    }
}