<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { 
    CalendarDays, 
    FileText, 
    MessageSquare, 
    Vote,
    Building2,
    Shield
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';
import type { Page } from '@inertiajs/core';

interface KeycloakRole {
    role_name: string;
}

interface User {
    name: string;
    email: string;
    keycloak_roles?: KeycloakRole[];
}

interface Auth {
    user: User;
    user_permissions?: string[];
}

interface PageProps {
    auth: Auth;
    [key: string]: unknown;
}

const page = usePage<PageProps>();
const auth = computed(() => page.props.auth);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    name?: string;
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container space-y-6 py-8">
            <!-- Debug Widget -->
            <Card class="bg-muted/50">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Shield class="h-5 w-5 text-primary" />
                        Debug: User Roles & Permissions
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-medium mb-2">Roles:</h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="role in auth.user?.keycloak_roles" :key="role.role_name"
                                    class="px-2 py-1 rounded-md bg-primary/10 text-primary text-sm">
                                    {{ role.role_name }}
                                </span>
                                <span v-if="!auth.user?.keycloak_roles?.length" class="text-muted-foreground text-sm">
                                    No roles assigned
                                </span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Permissions:</h4>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="permission in auth.user_permissions" :key="permission"
                                    class="px-2 py-1 rounded-md bg-secondary/10 text-secondary-foreground text-sm">
                                    {{ permission }}
                                </span>
                                <span v-if="!auth.user_permissions?.length" class="text-muted-foreground text-sm">
                                    No permissions assigned
                                </span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight">Welcome Back!</h1>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Business Licenses -->
                <Card v-if="auth.user_permissions?.includes('access-business-license')">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Building2 class="h-5 w-5 text-primary" />
                            Business Licenses
                        </CardTitle>
                        <CardDescription>Manage business licenses</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button asChild class="w-full">
                            <Link :href="route('business-license.index')">View Licenses</Link>
                        </Button>
                    </CardContent>
                </Card>

                <!-- Quick Vote -->
                <Card v-if="auth.user_permissions?.includes('quick-vote-access')">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Vote class="h-5 w-5 text-primary" />
                            Quick Vote
                        </CardTitle>
                        <CardDescription>Rapid voting and decision making</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button asChild class="w-full">
                            <Link :href="route('quick-vote.index')">Access Quick Vote</Link>
                        </Button>
                    </CardContent>
                </Card>

                <!-- GovTxt Config -->
                <Card v-if="auth.user_permissions?.includes('govtxt-config-access')">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageSquare class="h-5 w-5 text-primary" />
                            GovTxt Config
                        </CardTitle>
                        <CardDescription>Message configuration system</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Button asChild class="w-full">
                            <Link :href="route('govtxt-config.index')">Configure Messages</Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template> 