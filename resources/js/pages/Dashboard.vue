<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    LayoutDashboard, 
    Calculator,
    FileCheck,
    Vote,
    MessageSquare,
    Bell,
    Calendar,
    FileText,
    Users,
    Building2,
    Wallet,
    BarChart3
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    name?: string;
    permissions?: string[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container space-y-6 py-8">
            <!-- Welcome Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Welcome back!</h1>
                    <p class="text-muted-foreground">Here's what's happening in your city today.</p>
                </div>
                <Button variant="outline" size="sm">
                    <Calendar class="mr-2 h-4 w-4" />
                    View Calendar
                </Button>
            </div>

            <!-- Quick Stats -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Projects</CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">12</div>
                        <p class="text-xs text-muted-foreground">+2 from last month</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Approvals</CardTitle>
                        <FileCheck class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">5</div>
                        <p class="text-xs text-muted-foreground">3 require attention</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Active Voters</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">2,345</div>
                        <p class="text-xs text-muted-foreground">+180 this week</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Notifications</CardTitle>
                        <Bell class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">3</div>
                        <p class="text-xs text-muted-foreground">2 unread messages</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-7">
                <!-- Recent Activity -->
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                        <CardDescription>Latest updates from your city</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                    <Building2 class="h-4 w-4 text-primary" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">New Business License Application</p>
                                    <p class="text-sm text-muted-foreground">Downtown Coffee Shop submitted a new application</p>
                                </div>
                                <Badge variant="secondary">Pending</Badge>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                    <Vote class="h-4 w-4 text-primary" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">Vote Results Available</p>
                                    <p class="text-sm text-muted-foreground">Community Park Renovation Project results are in</p>
                                </div>
                                <Badge variant="secondary">Completed</Badge>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10">
                                    <Wallet class="h-4 w-4 text-primary" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">Budget Update</p>
                                    <p class="text-sm text-muted-foreground">Q1 budget report has been generated</p>
                                </div>
                                <Badge variant="secondary">New</Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Actions -->
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>Common tasks and shortcuts</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Button v-if="permissions?.includes('access-budget')" variant="outline" class="w-full justify-start">
                                <Calculator class="mr-2 h-4 w-4" />
                                View Budget
                            </Button>
                            <Button v-if="permissions?.includes('access-business-license')" variant="outline" class="w-full justify-start">
                                <FileCheck class="mr-2 h-4 w-4" />
                                Manage Licenses
                            </Button>
                            <Button v-if="permissions?.includes('quick-vote-access')" variant="outline" class="w-full justify-start">
                                <Vote class="mr-2 h-4 w-4" />
                                Create Quick Vote
                            </Button>
                            <Button v-if="permissions?.includes('govtxt-config-access')" variant="outline" class="w-full justify-start">
                                <MessageSquare class="mr-2 h-4 w-4" />
                                Configure GovTxt
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
