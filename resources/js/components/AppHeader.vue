<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
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
    Moon,
    Sun,
    Monitor
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuLabel,
    DropdownMenuGroup,
    DropdownMenuSub,
    DropdownMenuSubTrigger,
    DropdownMenuSubContent
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Command, CommandDialog, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { getInitials } from '@/composables/useInitials';
import type { NavigationItem, UserNavigationItem } from '@/layouts/AdminLayout.vue';
import type { Auth, BreadcrumbItem, KeycloakConfig } from '@/types';
import { cn } from '@/lib/utils';
import { useSettingsStore } from '@/stores/settings';
import { useAppearance } from '@/composables/useAppearance';
import type { Appearance } from '@/composables/useAppearance';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
    navigation?: NavigationItem[];
    userNavigation?: UserNavigationItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    navigation: () => [],
    userNavigation: () => [],
});

const page = usePage<{ auth: Auth, keycloak: KeycloakConfig }>();
const auth = computed(() => page.props.auth);
const keycloakConfig = computed(() => page.props.keycloak);
const isCommandOpen = ref(false);
const userAvatar = computed(() => auth.value?.user?.avatar || '');
const userName = computed(() => auth.value?.user?.name || '');

// Add debugging
onMounted(() => {
    console.log('Full User Object:', JSON.stringify(auth.value?.user, null, 2));
    console.log('Keycloak Roles:', JSON.stringify(auth.value?.user?.keycloak_roles, null, 2));
    console.log('User Permissions:', auth.value?.user_permissions);
    console.log('Is Admin:', isAdmin.value);
    console.log('Admin check details:', {
        roles: auth.value?.user?.keycloak_roles,
        hasAdminRole: auth.value?.user?.keycloak_roles?.some(role => 
            role.role_name === 'admin' || 
            role.role_name === 'developer'
        ),
        roleNames: auth.value?.user?.keycloak_roles?.map(role => role.role_name)
    });
});

const isAdmin = computed(() => {
    const hasAdminRole = Boolean(
        auth.value?.user?.keycloak_roles?.some(role => 
            role.role_name === 'admin'
        )
    );
    console.log('Admin check:', {
        roles: auth.value?.user?.keycloak_roles,
        hasAdminRole
    });
    return hasAdminRole;
});

const hasPermission = (permission?: string) => {
    if (!permission) return true;
    
    // Check if user has admin role
    const hasAdminRole = auth.value?.user?.keycloak_roles?.some(role => 
        role.role_name === 'admin'
    );
    
    if (hasAdminRole) return true;
    
    // Check if user has the specific permission
    const hasSpecificPermission = auth.value?.user_permissions?.includes(permission);
    if (hasSpecificPermission) return true;
    
    // Check if user has a role that matches the permission
    const hasMatchingRole = auth.value?.user?.keycloak_roles?.some(role => 
        role.role_name === permission
    );
    
    return hasMatchingRole || false;
};

const navigate = (routeName: string) => {
    console.log(`[AppHeader] navigate called for routeName: ${routeName}`);
    try {
        const targetUrl = route(routeName);
        console.log(`[AppHeader] Generated URL for ${routeName}: ${targetUrl}`);
        if (!targetUrl) {
            console.error(`[AppHeader] route('${routeName}') generated an invalid URL!`);
            return; // Stop if URL is invalid
        }
        router.visit(targetUrl, {
        method: 'get',
        preserveState: true,
        preserveScroll: true,
        onError: (errors) => {
                console.error(`[AppHeader] Navigation error for ${routeName}:`, errors);
        }
    });
    } catch (e) {
        console.error(`[AppHeader] Error during route('${routeName}') generation or router.visit:`, e);
    }
};

const logout = () => {
    // Redirect to Keycloak logout endpoint
    const keycloakBaseUrl = 'https://sso.santaclarautah.gov';
    const keycloakRealm = 'SANTACLARA-DEV';
    const redirectUri = encodeURIComponent('http://127.0.0.1:8000');
    const logoutUrl = `${keycloakBaseUrl}/realms/${keycloakRealm}/protocol/openid-connect/logout?redirect_uri=${redirectUri}`;
    
    // Clear any local storage or session data
    localStorage.clear();
    sessionStorage.clear();
    
    // Redirect to Keycloak logout
    window.location.href = logoutUrl;
};

const settingsStore = useSettingsStore();
const { updateAppearance } = useAppearance();

// Add theme toggle function
const toggleTheme = (newTheme: Appearance) => {
    updateAppearance(newTheme);
};

// Add keyboard shortcuts
onMounted(() => {
    window.addEventListener('keydown', (e) => {
        // Command palette shortcut (⌘K)
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            isCommandOpen.value = true;
        }
        
        // Navigation shortcuts
        if ((e.metaKey || e.ctrlKey) && e.key === 'a' && isAdmin.value) {
            e.preventDefault();
            navigate('admin.dashboard');
        }
        if ((e.metaKey || e.ctrlKey) && e.key === 'd') {
            e.preventDefault();
            navigate('dashboard');
        }
        if ((e.metaKey || e.ctrlKey) && e.key === 'b' && hasPermission('access-budget')) {
            e.preventDefault();
            navigate('budget.index');
        }
        if ((e.metaKey || e.ctrlKey) && e.key === 'l' && hasPermission('access-business-license')) {
            e.preventDefault();
            navigate('business-license.index');
        }
        if ((e.metaKey || e.ctrlKey) && e.key === 'v' && hasPermission('quick-vote-access')) {
            e.preventDefault();
            navigate('quick-vote.index');
        }
        if ((e.metaKey || e.ctrlKey) && e.key === 'g' && hasPermission('govtxt-config-access')) {
            e.preventDefault();
            navigate('govtxt-config.index');
        }
    });
});

// Add command palette items
const commandItems = computed(() => {
    const items = [
        { title: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard },
        ...(isAdmin.value ? [{ title: 'Admin Dashboard', href: route('admin.dashboard'), icon: Gauge }] : []),
        ...(hasPermission('access-budget') ? [{ title: 'Budget Tool', href: route('budget.index'), icon: Calculator }] : []),
        ...(hasPermission('access-business-license') ? [{ title: 'Business Licenses', href: route('business-license.index'), icon: Building2 }] : []),
        ...(hasPermission('quick-vote-access') ? [{ title: 'Quick Vote', href: route('quick-vote.index'), icon: Vote }] : []),
        ...(hasPermission('govtxt-config-access') ? [{ title: 'GovTxt Config', href: route('govtxt-config.index'), icon: MessageSquare }] : [])
    ];
    return items;
});

const currentRoute = computed(() => {
    return window.location.pathname;
});

const isActive = (routeName: string) => {
    return currentRoute.value === route(routeName);
};

const isDevelopment = computed(() => {
    return window.location.hostname === 'localhost' || 
           window.location.hostname === '127.0.0.1';
});
</script>

<template>
    <!-- Debug Bar (only in dev mode) -->
    <div v-if="isDevelopment" class="bg-yellow-300 text-black text-xs p-1">
        Debug Info: 
        User: {{ userName || 'Guest' }} | 
        Admin: {{ isAdmin ? 'Yes' : 'No' }} | 
        Realm: {{ keycloakConfig?.realm }} | 
        Client: {{ keycloakConfig?.client_id }} | 
        Roles: {{ auth?.user?.keycloak_roles?.map(r => r.role_name).join(', ') || 'None' }} | 
        Specific Permissions: {{ auth?.user_permissions?.join(', ') || 'None' }}
    </div>

    <div class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container flex h-14 items-center">
            <!-- Logo -->
            <div class="mr-4">
                <Link href="/" class="flex items-center space-x-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded bg-primary/10">
                        <Settings class="h-5 w-5 text-primary" />
                    </div>
                    <span class="hidden font-semibold text-xl sm:inline-block">CityNexus</span>
                </Link>
            </div>

            <!-- Main Navigation -->
            <nav class="flex flex-1 items-center space-x-1">
                <!-- Home Dropdown -->
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button 
                            variant="ghost" 
                            size="sm" 
                            class="h-8 px-3"
                            :class="{ 'bg-accent': isActive('dashboard') || isActive('admin.dashboard') }"
                        >
                            <LayoutDashboard class="mr-2 h-4 w-4" />
                            Home
                            <ChevronDown class="ml-2 h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" class="w-52">
                        <DropdownMenuItem 
                            v-if="isAdmin" 
                            @click="navigate('admin.dashboard')"
                            :class="{ 'bg-accent': isActive('admin.dashboard') }"
                        >
                            <Gauge class="mr-2 h-4 w-4" />
                            <span>Admin Dashboard</span>
                            <DropdownMenuShortcut>⌘A</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                            @click="navigate('dashboard')"
                            :class="{ 'bg-accent': isActive('dashboard') }"
                        >
                            <LayoutDashboard class="mr-2 h-4 w-4" />
                            <span>User Dashboard</span>
                            <DropdownMenuShortcut>⌘D</DropdownMenuShortcut>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <!-- Finance Dropdown -->
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button 
                            variant="ghost" 
                            size="sm" 
                            class="h-8 px-3"
                            :class="{ 'bg-accent': isActive('budget.index') || isActive('business-license.index') }"
                        >
                            <Calculator class="mr-2 h-4 w-4" />
                            Finance
                            <ChevronDown class="ml-2 h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" class="w-52">
                        <DropdownMenuItem 
                            v-if="hasPermission('access-budget')" 
                            @click="navigate('budget.index')"
                            :class="{ 'bg-accent': isActive('budget.index') }"
                        >
                            <Calculator class="mr-2 h-4 w-4" />
                            <span>Budget Tool</span>
                            <DropdownMenuShortcut>⌘B</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                            v-if="hasPermission('access-business-license')" 
                            @click="navigate('business-license.index')"
                            :class="{ 'bg-accent': isActive('business-license.index') }"
                        >
                            <Building2 class="mr-2 h-4 w-4" />
                            <span>Business Licenses</span>
                            <DropdownMenuShortcut>⌘L</DropdownMenuShortcut>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <!-- Tools Dropdown -->
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button 
                            variant="ghost" 
                            size="sm" 
                            class="h-8 px-3"
                            :class="{ 'bg-accent': isActive('quick-vote.index') || isActive('govtxt-config.index') }"
                        >
                            <Settings class="mr-2 h-4 w-4" />
                            Tools
                            <ChevronDown class="ml-2 h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" class="w-52">
                        <DropdownMenuItem 
                            v-if="hasPermission('quick-vote-access')" 
                            @click="navigate('quick-vote.index')"
                            :class="{ 'bg-accent': isActive('quick-vote.index') }"
                        >
                            <Vote class="mr-2 h-4 w-4" />
                            <span>Quick Vote</span>
                            <DropdownMenuShortcut>⌘V</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem 
                            v-if="hasPermission('govtxt-config-access')" 
                            @click="navigate('govtxt-config.index')"
                            :class="{ 'bg-accent': isActive('govtxt-config.index') }"
                        >
                            <MessageSquare class="mr-2 h-4 w-4" />
                            <span>GovTxt Config</span>
                            <DropdownMenuShortcut>⌘G</DropdownMenuShortcut>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </nav>

            <!-- Right Side Actions -->
            <div class="flex items-center gap-2">
                <!-- Command Palette Trigger -->
                <Button
                    variant="outline"
                    size="sm"
                    class="relative hidden h-8 w-[200px] justify-start text-sm font-normal md:inline-flex"
                    @click="isCommandOpen = true"
                >
                    <Search class="mr-2 h-4 w-4" />
                    <span>Search...</span>
                    <kbd class="pointer-events-none absolute right-1.5 top-1.5 hidden h-5 select-none items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium opacity-100 sm:flex">
                        <span class="text-xs">⌘</span>K
                    </kbd>
                </Button>

                <!-- Notifications -->
                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger asChild>
                            <Button variant="ghost" size="icon" class="relative h-8 w-8">
                                <Bell class="h-4 w-4" />
                                <span class="sr-only">Notifications</span>
                                <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-primary"></span>
                            </Button>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>Notifications</p>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>

                <!-- User Menu -->
                <DropdownMenu v-if="auth?.user">
                    <DropdownMenuTrigger asChild>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="relative h-8 w-8 rounded-full"
                        >
                            <Avatar class="h-8 w-8">
                                <AvatarImage :src="userAvatar" :alt="userName" />
                                <AvatarFallback>{{ getInitials(userName) }}</AvatarFallback>
                            </Avatar>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <DropdownMenuLabel>My Account</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuGroup>
                            <DropdownMenuItem>
                                <User class="mr-2 h-4 w-4" />
                                <span>Profile</span>
                            </DropdownMenuItem>
                            <DropdownMenuSub>
                                <DropdownMenuSubTrigger>
                                    <Settings class="mr-2 h-4 w-4" />
                                    <span>Theme</span>
                                </DropdownMenuSubTrigger>
                                <DropdownMenuSubContent>
                                    <DropdownMenuItem @click="toggleTheme('light')">
                                        <Sun class="mr-2 h-4 w-4" />
                                        <span>Light</span>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem @click="toggleTheme('dark')">
                                        <Moon class="mr-2 h-4 w-4" />
                                        <span>Dark</span>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem @click="toggleTheme('system')">
                                        <Monitor class="mr-2 h-4 w-4" />
                                        <span>System</span>
                                    </DropdownMenuItem>
                                </DropdownMenuSubContent>
                            </DropdownMenuSub>
                            <DropdownMenuItem @click="settingsStore.toggleSidebar">
                                <Settings class="mr-2 h-4 w-4" />
                                <span>Settings</span>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem @click="logout">
                            <LogOut class="mr-2 h-4 w-4" />
                            <span>Log out</span>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <div v-if="props.breadcrumbs.length > 1" class="container border-t py-2">
            <Breadcrumbs :breadcrumbs="breadcrumbs" />
        </div>

        <!-- Command Palette -->
        <CommandDialog v-model:open="isCommandOpen">
            <Command>
                <CommandInput placeholder="Type a command or search..." />
                <CommandList>
                    <CommandEmpty>No results found.</CommandEmpty>
                    <CommandGroup title="Navigation">
                        <CommandItem
                            v-for="item in commandItems"
                            :key="item.title"
                            @select="() => {
                                navigate(item.href);
                                isCommandOpen = false;
                            }"
                        >
                            <component :is="item.icon" class="mr-2 h-4 w-4" />
                            {{ item.title }}
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </CommandDialog>
    </div>
</template>

<style scoped>
.container {
    @apply mx-auto max-w-7xl px-4 sm:px-6 lg:px-8;
}
</style>
