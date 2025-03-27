<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    Server, 
    Activity, 
    Globe2, 
    Database,
    CheckCircle2,
    XCircle,
    AlertCircle
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'System Health',
        href: '/admin/health',
    },
];

defineProps<{
    name?: string;
}>();
</script>

<template>
    <Head title="System Health" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="container space-y-6 py-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight">System Health</h1>
            </div>
            
            <!-- Service Health Overview -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Database Status -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Database class="h-5 w-5 text-primary" />
                            Database
                        </CardTitle>
                        <CardDescription>Connection Status</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <CheckCircle2 class="h-5 w-5 text-green-500" />
                            <span class="text-sm">Connected</span>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-muted-foreground">
                                Last checked: {{ new Date().toLocaleTimeString() }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- API Status -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Globe2 class="h-5 w-5 text-primary" />
                            External APIs
                        </CardTitle>
                        <CardDescription>Integration Status</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Finance API</span>
                                <Badge variant="success">Connected</Badge>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">GovTxt API</span>
                                <Badge variant="success">Connected</Badge>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-muted-foreground">
                                Last checked: {{ new Date().toLocaleTimeString() }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- System Resources -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Server class="h-5 w-5 text-primary" />
                            System Resources
                        </CardTitle>
                        <CardDescription>Server Status</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">CPU Usage</span>
                                <Badge variant="default">45%</Badge>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Memory Usage</span>
                                <Badge variant="default">62%</Badge>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">Disk Space</span>
                                <Badge variant="default">78%</Badge>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-muted-foreground">
                                Last updated: {{ new Date().toLocaleTimeString() }}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Errors -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <AlertCircle class="h-5 w-5 text-destructive" />
                        Recent Errors
                    </CardTitle>
                    <CardDescription>System errors and warnings</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="text-sm text-muted-foreground">
                            No recent errors to display
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AdminLayout>
</template> 