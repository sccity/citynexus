<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();

// Function to check if user has permission
function hasPermission(permission?: string): boolean {
    if (!permission) return true;
    
    // Get the user object from the page props
    const user = page.props.auth.user;
    
    // Check if the user is a developer - developers get access to everything
    const userRoles = user?.roles || [];
    const keycloakRoles = user?.keycloak_roles || [];
    
    // If user has developer role, grant access to everything
    if (userRoles.some((r) => r.name === 'developer') || 
        keycloakRoles.some((r) => r.role_name === 'developer')) {
        return true;
    }
    
    // Check if the permission is a role check (prefixed with 'role:')
    if (permission.startsWith('role:')) {
        const role = permission.substring(5); // Remove 'role:' prefix
        
        // Check both local and Keycloak roles
        return userRoles.some((r) => r.name === role) || 
               keycloakRoles.some((r) => r.role_name === role);
    }
    
    // For regular permissions
    const userPermissions = page.props.auth.user_permissions || [];
    return userPermissions.includes(permission);
}
</script>

<template>
    <SidebarGroup v-for="(item, index) in items" :key="index" class="px-2 mb-4">
        <template v-if="item.section">
            <SidebarGroupLabel class="px-3 mb-2 text-sm font-medium uppercase tracking-wider sidebar-group-label">
                {{ item.section }}
            </SidebarGroupLabel>
            <SidebarMenu>
                <template v-for="subItem in item.items" :key="subItem.title">
                    <SidebarMenuItem v-if="hasPermission(subItem.permission)" class="mb-1">
                        <SidebarMenuButton 
                            as-child 
                            :is-active="subItem.href === page.url"
                            class="w-full sidebar-nav-item rounded-md"
                        >
                            <Link 
                                :href="subItem.href"
                                class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200"
                            >
                                <component 
                                    :is="subItem.icon" 
                                    class="h-5 w-5 mr-2 icon transition-colors duration-200"
                                />
                                <span>{{ subItem.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </template>
            </SidebarMenu>
        </template>
        <template v-else>
            <SidebarGroupLabel v-if="index === 0" class="px-3 mb-2 text-sm font-medium uppercase tracking-wider sidebar-group-label">
                Platform
            </SidebarGroupLabel>
            <SidebarMenu>
                <SidebarMenuItem v-if="hasPermission(item.permission)" class="mb-1">
                    <SidebarMenuButton 
                        as-child 
                        :is-active="item.href === page.url"
                        class="w-full sidebar-nav-item rounded-md"
                    >
                        <Link 
                            :href="item.href"
                            class="flex items-center px-3 py-2 text-sm font-medium transition-colors duration-200"
                        >
                            <component 
                                :is="item.icon" 
                                class="h-5 w-5 mr-2 icon transition-colors duration-200"
                            />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </template>
    </SidebarGroup>
</template>
