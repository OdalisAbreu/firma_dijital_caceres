import { ref, onMounted, watch } from 'vue';

// Estado global compartido
const isDark = ref(false);

// Aplicar modo oscuro al DOM
const applyDarkMode = (dark) => {
    if (typeof document !== 'undefined') {
        if (dark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
};

// Inicializar desde localStorage (sincrónico)
if (typeof window !== 'undefined') {
    const saved = localStorage.getItem('darkMode');
    if (saved !== null) {
        isDark.value = saved === 'true';
    } else {
        isDark.value = false;
    }
    // Aplicar inmediatamente
    applyDarkMode(isDark.value);
}

export function useDarkMode() {
    // Asegurar que se aplique al montar
    onMounted(() => {
        applyDarkMode(isDark.value);
    });

    // Toggle del modo oscuro
    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
        if (typeof window !== 'undefined') {
            localStorage.setItem('darkMode', isDark.value.toString());
        }
        applyDarkMode(isDark.value);
    };

    // Watch para cambios
    watch(isDark, (newValue) => {
        applyDarkMode(newValue);
    });

    return {
        isDark,
        toggleDarkMode,
    };
}
