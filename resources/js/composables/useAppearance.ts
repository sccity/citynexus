import { onMounted, ref } from 'vue';

export type Appearance = 'light' | 'dark' | 'system';

export function updateTheme(value: Appearance) {
    if (value === 'system') {
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', value === 'dark');
    }
}

export function useAppearance() {
    const appearance = ref<Appearance>('light');

    onMounted(() => {
        // Get saved theme or default to light
        const savedAppearance = localStorage.getItem('appearance') as Appearance | null;
        appearance.value = savedAppearance || 'light';
        updateTheme(appearance.value);

        // Listen for system theme changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', () => {
            if (appearance.value === 'system') {
                updateTheme('system');
            }
        });
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;
        if (value === 'light') {
            localStorage.removeItem('appearance');
        } else {
            localStorage.setItem('appearance', value);
        }
        updateTheme(value);
    }

    return {
        appearance,
        updateAppearance,
    };
}
