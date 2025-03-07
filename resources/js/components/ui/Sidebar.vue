<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { 
    LayoutGrid, 
    Vote, 
    FileText, 
    ChevronRight,
    Gauge,
    Settings2,
    Users,
    Building2,
    MessageSquare,
    Boxes
} from 'lucide-vue-next';
import { computed } from 'vue';
import type { Auth } from '@/types';
import type { FunctionalComponent } from 'vue';
import type { LucideProps } from 'lucide-vue-next';

interface NavItem {
    title: string;
    href: string;
    icon: FunctionalComponent<LucideProps>;
    permission?: string;
    description?: string;
}

interface NavSection {
    title: string;
    items: NavItem[];
}

const page = usePage<{ auth: Auth }>();
const auth = computed(() => page.props.auth);

const isCurrentRoute = (url: string) => {
    return page.url === url;
};

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

const userMode = computed(() => isAdmin.value ? 'Admin Mode' : 'User Mode');
const userModeDescription = computed(() => isAdmin.value 
    ? 'You have full administrative access' 
    : 'Welcome to CityNexus'
);

// Navigation sections with items
const navSections = computed<NavSection[]>(() => [
    {
        title: 'Overview',
        items: [
            {
                title: isAdmin.value ? 'Admin Dashboard' : 'Dashboard',
                href: route('dashboard'),
                icon: isAdmin.value ? Gauge : LayoutGrid,
            }
        ]
    },
    {
        title: 'Tools',
        items: [
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
        ]
    },
    // Only show admin section to admins
    ...(isAdmin.value ? [{
        title: 'Administration',
        items: [
            {
                title: 'Users',
                href: route('users.index'),
                icon: Users,
                description: 'Manage system users'
            },
            {
                title: 'Organizations',
                href: route('organizations.index'),
                icon: Building2,
                description: 'Manage organizations'
            },
            {
                title: 'System Settings',
                href: route('settings.index'),
                icon: Settings2,
                description: 'Configure system settings'
            }
        ]
    }] : [])
]);
</script>

<template>
    <div class="flex h-screen w-[280px] flex-col border-r bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <!-- Logo -->
        <div class="flex h-16 items-center border-b px-6">
            <Link href="/" class="flex items-center space-x-2">
                <span class="font-bold text-lg text-foreground">CityNexus</span>
            </Link>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto">
            <div class="space-y-6 p-4">
                <div v-for="section in navSections" :key="section.title" class="space-y-2">
                    <h2 class="px-3 text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                        {{ section.title }}
                    </h2>
                    
                    <div class="space-y-1">
                        <template v-for="item in section.items" :key="item.title">
                            <Link
                                v-if="hasPermission(item?.permission)"
                                :href="item.href"
                                class="group relative flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm transition-colors hover:bg-accent hover:text-accent-foreground"
                                :class="[
                                    isCurrentRoute(item.href)
                                        ? 'bg-primary/10 text-primary dark:bg-primary/20'
                                        : 'text-muted-foreground hover:text-foreground'
                                ]"
                            >
                                <component :is="item.icon" class="h-4 w-4 shrink-0" />
                                <div class="flex-1 truncate">
                                    <span>{{ item.title }}</span>
                                    <p 
                                        v-if="item.description" 
                                        class="truncate text-xs text-muted-foreground"
                                    >
                                        {{ item.description }}
                                    </p>
                                </div>
                                <ChevronRight 
                                    class="h-4 w-4 opacity-0 transition-opacity group-hover:opacity-100"
                                    :class="{ 'opacity-100 text-primary': isCurrentRoute(item.href) }"
                                />
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Bottom section -->
        <div class="border-t p-4">
            <div class="rounded-lg bg-muted p-4">
                <div class="flex items-center gap-x-3">
                    <Boxes class="h-5 w-5 text-primary" />
                    <h3 class="text-sm font-medium text-foreground">{{ userMode }}</h3>
                </div>
                <p class="mt-1 text-xs text-muted-foreground">{{ userModeDescription }}</p>
            </div>
        </div>
    </div>
</template> 