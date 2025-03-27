import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useSettingsStore = defineStore('settings', () => {
    const theme = ref(localStorage.getItem('theme') || 'system');
    const sidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true');
    const notificationsEnabled = ref(localStorage.getItem('notificationsEnabled') !== 'false');

    function updateTheme(newTheme: string) {
        theme.value = newTheme;
        localStorage.setItem('theme', newTheme);
    }

    function toggleSidebar() {
        sidebarCollapsed.value = !sidebarCollapsed.value;
        localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value.toString());
    }

    function toggleNotifications() {
        notificationsEnabled.value = !notificationsEnabled.value;
        localStorage.setItem('notificationsEnabled', notificationsEnabled.value.toString());
    }

    return {
        theme,
        sidebarCollapsed,
        notificationsEnabled,
        updateTheme,
        toggleSidebar,
        toggleNotifications,
    };
}); 