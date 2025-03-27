<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    CalendarDays, 
    FileText, 
    MessageSquare, 
    Vote,
    Building2
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

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
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight">Welcome Back!</h1>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Business Licenses -->
                <Card v-if="$page.props.auth.user_permissions?.includes('access-business-license')">
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
                <Card v-if="$page.props.auth.user_permissions?.includes('quick-vote-access')">
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
                <Card v-if="$page.props.auth.user_permissions?.includes('govtxt-config-access')">
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