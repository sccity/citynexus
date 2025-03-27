<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useSettingsStore } from '@/stores/settings';

const page = usePage();
const settingsStore = useSettingsStore();

const updateTheme = (newTheme: string) => {
    settingsStore.updateTheme(newTheme);
    
    if (newTheme === 'system') {
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', systemTheme === 'dark');
    } else {
        document.documentElement.classList.toggle('dark', newTheme === 'dark');
    }
};

// Watch for system theme changes
const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
const handleChange = (e: MediaQueryListEvent) => {
    if (settingsStore.theme === 'system') {
        document.documentElement.classList.toggle('dark', e.matches);
    }
};

// Watch for theme changes in the store
watch(() => settingsStore.theme, (newTheme) => {
    updateTheme(newTheme);
});

onMounted(() => {
    updateTheme(settingsStore.theme);
    mediaQuery.addEventListener('change', handleChange);
});

// Clean up
onUnmounted(() => {
    mediaQuery.removeEventListener('change', handleChange);
});

// Provide theme functionality to child components
defineExpose({
    theme: settingsStore.theme,
    updateTheme,
});
</script>

<template>
    <slot />
</template> 