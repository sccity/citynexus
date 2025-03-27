<script setup lang="ts">
import { ref, computed } from 'vue';
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
    Building2
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
} from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Command, CommandDialog, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { getInitials } from '@/composables/useInitials';
import type { Auth, BreadcrumbItem } from '@/types';
import { cn } from '@/lib/utils';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage<{ auth: Auth }>();
const auth = computed(() => page.props.auth);
const isCommandOpen = ref(false);
const userAvatar = computed(() => auth.value?.user?.avatar || '');
const userName = computed(() => auth.value?.user?.name || '');

const isAdmin = computed(() => Boolean(
    auth.value?.user?.keycloak_roles?.some(role => 
        role.role_name === 'admin' || 
        role.role_name === 'developer'
    )
));

const hasPermission = (permission?: string) => {
    if (!permission) return true;
    return auth.value?.user_permissions?.includes(permission) || 
           auth.value?.user?.keycloak_roles?.some(role => 
               role.role_name === 'admin' || 
               role.role_name === 'developer' || 
               role.role_name === permission
           );
};

const navigate = (routeName: string) => {
    router.visit(route(routeName));
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
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
                        <Button variant="ghost" size="sm" class="h-8 px-3">
                            <LayoutDashboard class="mr-2 h-4 w-4" />
                            Home
                            <ChevronDown class="ml-2 h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" class="w-52">
                        <DropdownMenuItem v-if="isAdmin" @click="navigate('admin.dashboard')">
                            <Gauge class="mr-2 h-4 w-4" />
                            <span>Admin Dashboard</span>
                            <DropdownMenuShortcut>⌘A</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="navigate('dashboard')">
                            <LayoutDashboard class="mr-2 h-4 w-4" />
                            <span>User Dashboard</span>
                            <DropdownMenuShortcut>⌘D</DropdownMenuShortcut>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <!-- Finance Dropdown -->
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm" class="h-8 px-3">
                            <Calculator class="mr-2 h-4 w-4" />
                            Finance
                            <ChevronDown class="ml-2 h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" class="w-52">
                        <DropdownMenuItem v-if="hasPermission('access-budget')" @click="navigate('budget.index')">
                            <Calculator class="mr-2 h-4 w-4" />
                            <span>Budget Tool</span>
                            <DropdownMenuShortcut>⌘B</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="hasPermission('access-business-license')" @click="navigate('business-license.index')">
                            <Building2 class="mr-2 h-4 w-4" />
                            <span>Business Licenses</span>
                            <DropdownMenuShortcut>⌘L</DropdownMenuShortcut>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <!-- Tools Dropdown -->
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" size="sm" class="h-8 px-3">
                            <Settings class="mr-2 h-4 w-4" />
                            Tools
                            <ChevronDown class="ml-2 h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start" class="w-52">
                        <DropdownMenuItem v-if="hasPermission('quick-vote-access')" @click="navigate('quick-vote.index')">
                            <Vote class="mr-2 h-4 w-4" />
                            <span>Quick Vote</span>
                            <DropdownMenuShortcut>⌘V</DropdownMenuShortcut>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="hasPermission('govtxt-config-access')" @click="navigate('govtxt-config.index')">
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
                            <DropdownMenuItem>
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
                            v-for="item in [
                                { title: 'Dashboard', href: route('dashboard'), icon: LayoutDashboard },
                                ...(isAdmin ? [{ title: 'Admin Dashboard', href: route('admin.dashboard'), icon: Gauge }] : []),
                                ...(hasPermission('access-budget') ? [{ title: 'Budget Tool', href: route('budget.index'), icon: Calculator }] : []),
                                ...(hasPermission('access-business-license') ? [{ title: 'Business Licenses', href: route('business-license.index'), icon: Building2 }] : []),
                                ...(hasPermission('quick-vote-access') ? [{ title: 'Quick Vote', href: route('quick-vote.index'), icon: Vote }] : []),
                                ...(hasPermission('govtxt-config-access') ? [{ title: 'GovTxt Config', href: route('govtxt-config.index'), icon: MessageSquare }] : [])
                            ]"
                            :key="item.title"
                            @select="() => navigate(item.href)"
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
