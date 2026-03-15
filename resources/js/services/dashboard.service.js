import { API_URL } from "@/helpers/constants"
import axios from "axios"

export default {
    /**
     * Retorna os dados da dashboard do admin
     */
    async adminDashboard() {
        const { data } = await axios.get(`${API_URL}/admin/dashboard`);
        return data;
    }
}