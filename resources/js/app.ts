import '../css/app.css';
// import axios from 'axios'; // Remove direct import of base axios
import axiosInstance from '@/lib/axios'; // Import the custom instance

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
// @ts-expect-error - TS cannot find the ZiggyVue type, but it works at runtime
import { ZiggyVue } from 'ziggy-js';
import { createPinia } from 'pinia';
import ThemeProvider from './providers/ThemeProvider.vue';
import shadcnPlugin from './plugins/shadcn';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string;
        [key: string]: string | boolean | undefined;
    }

    interface ImportMeta {
        readonly env: ImportMetaEnv;
        readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Set Axios base URL from Ziggy config ON THE CUSTOM INSTANCE
        const initialProps = props.initialPage.props as any;
        console.log('[app.ts] Trying to set Axios baseURL from initialProps.ziggy.url:', initialProps.ziggy?.url);
        if (initialProps.ziggy?.url) {
            axiosInstance.defaults.baseURL = initialProps.ziggy.url; // Set on the imported instance
            console.log('[app.ts] Axios INSTANCE baseURL set to:', axiosInstance.defaults.baseURL);
        } else {
            console.warn('Ziggy configuration or base URL not found in initial props. Axios baseURL not set.');
        }
        
        createApp({ render: () => h(ThemeProvider, null, { default: () => h(App, props) }) })
            .use(plugin)
            .use(ZiggyVue, initialProps.ziggy) // Still passing Ziggy config here for the Vue plugin
            .use(pinia)
            .use(shadcnPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
