<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Command, CommandDialog, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, CommandSeparator } from '@/components/ui/command';
import { DropdownMenu, DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuShortcut, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Menubar, MenubarContent, MenubarItem, MenubarMenu, MenubarSeparator, MenubarShortcut, MenubarTrigger, MenubarPortal } from '@/components/ui/menubar';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import type { Auth, BreadcrumbItem, NavItemLink } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    BookOpen, 
    Folder, 
    LayoutGrid, 
    Menu, 
    Search, 
    CheckSquare, 
    Settings, 
    User, 
    LogOut, 
    Bell, 
    ChevronDown, 
    FileText, 
    Vote, 
    Gauge, 
    Settings2, 
    Users, 
    Building2, 
    MessageSquare,
    Boxes,
    Wallet,
    Calculator,
    FileCheck
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useEventListener } from '@vueuse/core';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

interface NavItemWithDescription extends NavItemLink {
    description?: string;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage<{ auth: Auth }>();
const auth = computed(() => page.props.auth);
const isCommandOpen = ref(false);

const isCurrentRoute = (url: string) => {
    return page.url === url;
};

const isAdmin = computed(() => Boolean(
    auth.value?.user?.keycloak_roles?.some(role => 
        role.role_name === 'admin' || 
        role.role_name === 'developer'
    )
));

const activeItemStyles = computed(() => (url: string) => (
    isCurrentRoute(url) 
        ? 'text-primary bg-primary/10 dark:bg-primary/20 dark:text-primary-foreground' 
        : 'text-foreground/70 hover:text-foreground dark:text-foreground/70 dark:hover:text-foreground'
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

const mainNavItems = computed<NavItemLink[]>(() => [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
    },
]);

const toolsNavItems = computed<NavItemWithDescription[]>(() => [
    {
        title: 'Quick Vote',
        href: route('quick-vote.index'),
        icon: Vote,
        permission: 'quick-vote-access',
        description: 'Rapid voting and decision making'
    },
    {
        title: 'GovTxt Config',
        href: route('govtxt-config.index'),
        icon: MessageSquare,
        permission: 'govtxt-config-access',
        description: 'Message configuration system'
    }
]);

const rightNavItems = computed<NavItemLink[]>(() => [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
]);

// Command palette keyboard shortcut
useEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        isCommandOpen.value = !isCommandOpen.value;
    }
});

// All available commands for the command palette
const commands = computed<NavItemLink[]>(() => [
    ...mainNavItems.value.filter(item => hasPermission(item.permission)),
    ...toolsNavItems.value.filter(item => hasPermission(item.permission)),
    ...rightNavItems.value,
]);

// Navigation helper
const navigate = (href: string) => {
    if (href.startsWith('http')) {
        window.location.assign(href);
    } else {
        window.location.href = href;
    }
};
</script>

<template>
    <div class="sticky top-0 z-40 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="flex h-16 items-center px-4">
            <!-- Logo -->
            <div class="flex items-center">
                <Link href="/" class="flex items-center space-x-2">
                    <AppLogoIcon class="h-6 w-6 text-primary" />
                    <span class="font-bold">CityNexus</span>
                </Link>
            </div>

            <!-- Main Navigation -->
            <div class="flex flex-1 items-center justify-center">
                <Menubar class="MenubarRoot">
                    <!-- Home Menu -->
                    <MenubarMenu>
                        <MenubarTrigger class="MenubarTrigger">
                            Home
                        </MenubarTrigger>
                        <MenubarPortal>
                            <MenubarContent class="MenubarContent" align="start" sideOffset={5} alignOffset={-3}>
                                <MenubarItem v-if="isAdmin" class="MenubarItem">
                                    <Gauge class="h-4 w-4 mr-2" />
                                    Admin Dashboard
                                    <div class="RightSlot">⌘A</div>
                                </MenubarItem>
                                <MenubarSeparator class="MenubarSeparator" />
                                <MenubarItem class="MenubarItem">
                                    <LayoutGrid class="h-4 w-4 mr-2" />
                                    Dashboard
                                    <div class="RightSlot">⌘D</div>
                                </MenubarItem>
                            </MenubarContent>
                        </MenubarPortal>
                    </MenubarMenu>

                    <!-- Finance Menu -->
                    <MenubarMenu>
                        <MenubarTrigger class="MenubarTrigger">
                            Finance
                        </MenubarTrigger>
                        <MenubarPortal>
                            <MenubarContent class="MenubarContent" align="start" sideOffset={5} alignOffset={-3}>
                                <MenubarItem class="MenubarItem">
                                    <Calculator class="h-4 w-4 mr-2" />
                                    Budget Tool
                                    <div class="RightSlot">⌘B</div>
                                </MenubarItem>
                                <MenubarSeparator class="MenubarSeparator" />
                                <MenubarItem class="MenubarItem">
                                    <FileCheck class="h-4 w-4 mr-2" />
                                    Business Licenses
                                    <div class="RightSlot">⌘L</div>
                                </MenubarItem>
                            </MenubarContent>
                        </MenubarPortal>
                    </MenubarMenu>

                    <!-- Tools Menu -->
                    <MenubarMenu>
                        <MenubarTrigger class="MenubarTrigger">
                            Tools
                        </MenubarTrigger>
                        <MenubarPortal>
                            <MenubarContent class="MenubarContent" align="start" sideOffset={5} alignOffset={-3}>
                                <MenubarItem v-if="hasPermission('quick-vote-access')" class="MenubarItem">
                                    <Vote class="h-4 w-4 mr-2" />
                                    Quick Vote
                                    <div class="RightSlot">⌘V</div>
                                </MenubarItem>
                                <MenubarSeparator v-if="hasPermission('quick-vote-access') && hasPermission('govtxt-config-access')" class="MenubarSeparator" />
                                <MenubarItem v-if="hasPermission('govtxt-config-access')" class="MenubarItem">
                                    <MessageSquare class="h-4 w-4 mr-2" />
                                    GovTxt Config
                                    <div class="RightSlot">⌘G</div>
                                </MenubarItem>
                            </MenubarContent>
                        </MenubarPortal>
                    </MenubarMenu>
                </Menubar>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center space-x-2">
                <!-- Command Palette Trigger -->
                <Button
                    variant="outline"
                    size="sm"
                    class="relative h-9 w-60 px-3 py-2"
                    @click="isCommandOpen = true"
                >
                    <Search class="mr-2 h-4 w-4" />
                    <span>Search...</span>
                    <kbd class="pointer-events-none absolute right-1.5 top-2 h-5 select-none items-center gap-1 rounded border bg-muted px-1.5 font-mono text-[10px] font-medium opacity-100">
                        <span class="text-xs">⌘</span>K
                    </kbd>
                </Button>

                <!-- Notifications -->
                <Button variant="ghost" size="icon" class="relative h-9 w-9">
                    <Bell class="h-4 w-4" />
                    <span class="sr-only">Notifications</span>
                    <span class="absolute right-1 top-1 h-2 w-2 rounded-full bg-primary"></span>
                </Button>

                <!-- User Menu -->
                <DropdownMenu v-if="auth?.user">
                    <DropdownMenuTrigger :as-child="true">
                        <Button
                            variant="ghost"
                            size="sm"
                            class="relative h-8 gap-2 rounded-full"
                        >
                            <Avatar class="h-8 w-8">
                                <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                <AvatarFallback class="bg-primary/10 text-primary">
                                    {{ getInitials(auth.user?.name) }}
                                </AvatarFallback>
                            </Avatar>
                            <span class="hidden lg:inline-flex">{{ auth.user.name }}</span>
                            <ChevronDown class="hidden h-4 w-4 lg:inline-flex" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <DropdownMenuLabel class="font-normal">
                            <div class="flex flex-col space-y-1">
                                <p class="text-sm font-medium leading-none">{{ auth.user.name }}</p>
                                <p class="text-xs leading-none text-muted-foreground">
                                    {{ auth.user.email }}
                                </p>
                            </div>
                        </DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuGroup>
                            <Link :href="route('profile.edit')" class="w-full">
                                <DropdownMenuItem class="cursor-pointer">
                                    <User class="mr-2 h-4 w-4" />
                                    <span>Profile</span>
                                    <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
                                </DropdownMenuItem>
                            </Link>
                            <Link :href="route('appearance')" class="w-full">
                                <DropdownMenuItem class="cursor-pointer">
                                    <Settings class="mr-2 h-4 w-4" />
                                    <span>Settings</span>
                                    <DropdownMenuShortcut>⌘,</DropdownMenuShortcut>
                                </DropdownMenuItem>
                            </Link>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <Link :href="route('logout')" method="post" as="button" class="w-full">
                            <DropdownMenuItem class="cursor-pointer">
                                <LogOut class="mr-2 h-4 w-4" />
                                <span>Log out</span>
                                <DropdownMenuShortcut>⇧⌘Q</DropdownMenuShortcut>
                            </DropdownMenuItem>
                        </Link>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <!-- Breadcrumbs -->
        <div v-if="props.breadcrumbs.length > 1" class="border-t py-2 px-4">
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
                            v-for="item in commands"
                            :key="item.title"
                            @select="() => {
                                if ('href' in item) {
                                    navigate(item.href);
                                }
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

<style>
.MenubarRoot {
    display: flex;
    background-color: white;
    padding: 3px;
    gap: 2px;
    border-radius: 6px;
    border: 1px solid var(--gray-6);
}

.MenubarTrigger {
    padding: 8px 12px;
    outline: none;
    user-select: none;
    font-weight: 500;
    line-height: 1;
    border-radius: 4px;
    color: var(--gray-11);
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2px;
}

.MenubarTrigger[data-highlighted],
.MenubarTrigger[data-state='open'] {
    background-color: var(--gray-4);
}

.MenubarContent {
    min-width: 220px;
    background-color: white;
    border-radius: 6px;
    padding: 5px;
    box-shadow: 0 2px 10px var(--gray-a7);
    animation-duration: 400ms;
    animation-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
}

.MenubarItem {
    font-size: 13px;
    line-height: 1;
    color: var(--gray-11);
    border-radius: 4px;
    display: flex;
    align-items: center;
    height: 25px;
    padding: 0 10px;
    position: relative;
    user-select: none;
    outline: none;
}

.MenubarItem[data-highlighted] {
    background-color: var(--gray-4);
}

.MenubarItem[data-disabled] {
    color: var(--gray-8);
    pointer-events: none;
}

.RightSlot {
    margin-left: auto;
    padding-left: 20px;
    color: var(--gray-9);
}

.MenubarSeparator {
    height: 1px;
    background-color: var(--gray-6);
    margin: 5px;
}
</style>
