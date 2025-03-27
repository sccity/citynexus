<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { Method } from '@inertiajs/core';
import { 
    LayoutDashboard, 
    Users, 
    Settings, 
    BarChart3, 
    Shield, 
    LogOut,
    Menu,
    X,
    Bell,
    Search,
    Gauge,
    FileText,
    Vote,
    MessageSquare,
    Calculator,
    FileCheck,
    ChevronDown,
    User,
    Building2,
    Database,
    Network,
    Server,
    Activity,
    AlertCircle,
    BellRing,
    Cog,
    HelpCircle,
    Mail,
    Package,
    ShieldCheck,
    UserCog,
    Wallet
} from 'lucide-vue-next';
import AppHeader from '@/components/AppHeader.vue';
import type { BreadcrumbItem } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

defineProps<Props>();

interface KeycloakRole {
    role_name: string;
}

interface User {
    name: string;
    avatar: string;
    keycloak_roles?: KeycloakRole[];
}

interface Auth {
    user: User;
    user_permissions?: string[];
}

interface NavigationItem {
    name: string;
    href: string;
    icon: any;
    permission?: string;
}

interface UserNavigationItem {
    name: string;
    href: string;
    icon: any;
    method?: Method;
}

const page = usePage<{ auth: Auth }>();
const auth = computed(() => page.props.auth);
const isSidebarOpen = ref(true);
const isProfileMenuOpen = ref(false);

const navigation: NavigationItem[] = [
    {
        name: 'Overview',
        href: route('admin.dashboard'),
        icon: LayoutDashboard,
    },
    {
        name: 'Users',
        href: route('users.manage'),
        icon: Users,
        permission: 'manage-users',
    },
    {
        name: 'System Health',
        href: route('admin.health'),
        icon: Activity,
        permission: 'admin-access',
    },
    {
        name: 'Business Licenses',
        href: route('business-license.index'),
        icon: FileCheck,
        permission: 'access-business-license',
    },
    {
        name: 'Budget',
        href: route('budget.index'),
        icon: Wallet,
        permission: 'access-budget',
    },
    {
        name: 'Quick Vote',
        href: route('quick-vote.index'),
        icon: Vote,
        permission: 'quick-vote-access',
    },
    {
        name: 'Settings',
        href: route('admin.settings'),
        icon: Settings,
        permission: 'admin-access',
    },
];

const userNavigation: UserNavigationItem[] = [
    { name: 'Your Profile', href: '#', icon: User },
    { name: 'Settings', href: '#', icon: Settings },
    { name: 'Sign out', href: route('logout'), method: 'post', icon: LogOut },
];

const hasPermission = (permission?: string) => {
    if (!permission) return true;
    return auth.value?.user_permissions?.includes(permission) || 
           auth.value?.user?.keycloak_roles?.some((role: KeycloakRole) => 
               role.role_name === 'admin' || 
               role.role_name === 'developer' || 
               role.role_name === permission
           );
};

const isCurrentRoute = (url: string) => {
    return page.url.startsWith(url);
};

// Filter navigation items based on permissions
const filteredNavigation = computed(() => {
    return navigation.filter(item => hasPermission(item.permission));
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Top Navigation -->
        <AppHeader :breadcrumbs="breadcrumbs" />

        <!-- Page Content -->
        <main class="flex-1">
            <div class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
.from-primary {
    --tw-gradient-from: var(--primary-color, #0EA5E9);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgb(14 165 233 / 0));
}
.to-primary-600 {
    --tw-gradient-to: var(--primary-600-color, #0284C7);
}
</style> 