<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const { appearance, updateAppearance } = useAppearance();

// Watch for theme changes in the store
watch(() => appearance.value, (newTheme) => {
    updateAppearance(newTheme);
});

onMounted(() => {
    updateAppearance(appearance.value);
});

// Provide theme functionality to child components
defineExpose({
    theme: appearance,
    updateTheme: updateAppearance,
});
</script>

<template>
    <slot />
</template> 