<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    Settings, 
    Database,
    Globe2,
    Bell,
    Shield,
    UserCog
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'Settings',
        href: '/admin/settings',
    },
];

defineProps<{
    name?: string;
}>();
</script>

<template>
    <Head title="Admin Settings" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="container space-y-6 py-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight">Admin Settings</h1>
            </div>
            
            <!-- Settings Categories -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- System Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Settings class="h-5 w-5 text-primary" />
                            System Settings
                        </CardTitle>
                        <CardDescription>General system configuration</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="text-sm text-muted-foreground">
                                Configure system-wide settings and preferences
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Database Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Database class="h-5 w-5 text-primary" />
                            Database Settings
                        </CardTitle>
                        <CardDescription>Database configuration</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="text-sm text-muted-foreground">
                                Manage database connections and backups
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- API Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Globe2 class="h-5 w-5 text-primary" />
                            API Settings
                        </CardTitle>
                        <CardDescription>External API configurations</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="text-sm text-muted-foreground">
                                Configure external API integrations
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Notification Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Bell class="h-5 w-5 text-primary" />
                            Notification Settings
                        </CardTitle>
                        <CardDescription>System notifications</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="text-sm text-muted-foreground">
                                Manage notification preferences
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Security Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Shield class="h-5 w-5 text-primary" />
                            Security Settings
                        </CardTitle>
                        <CardDescription>Security and access control</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="text-sm text-muted-foreground">
                                Configure security policies and access controls
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- User Management -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <UserCog class="h-5 w-5 text-primary" />
                            User Management
                        </CardTitle>
                        <CardDescription>User and role management</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="text-sm text-muted-foreground">
                                Manage users, roles, and permissions
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template> 