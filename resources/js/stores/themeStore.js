import { defineStore } from "pinia";
import { initTheme, toggleThemeHelper } from "../helpers/theme";
import { computed, ref } from "vue";
import { defaultLogoDark, defaultLogoLight } from "../helpers/constants";

/**
 * Pinia para gerenciar o tema do aplicativo (claro/escuro).
 */
export const useThemeStore = defineStore('theme', () => {
    // Reactive state indicating if the dark theme is active.
    const is_dark = ref(initTheme());

    /**
     * Alterna entre os temas claro e escuro e atualiza o estado.
     */
    const toggleTheme = () => {
        is_dark.value = toggleThemeHelper()
    } 

    /**
     * Retorna o logotipo apropriado com base no tema atual.
     */
    const logo = computed(() =>
        is_dark.value ? defaultLogoDark : defaultLogoLight
    );

    return {
        is_dark,
        toggleTheme,
        logo
    }
});