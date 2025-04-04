<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    Users, 
    FileText, 
    Vote, 
    MessageSquare,
    TrendingUp,
    AlertCircle,
    CheckCircle,
    Clock,
    ArrowUpRight,
    ArrowDownRight,
    Activity,
    CalendarDays,
    Building,
    FileBarChart,
    CheckCircle2,
    XCircle,
    Server,
    Globe2,
    LayoutDashboard,
    Bell,
    Wallet,
    Settings,
    Database,
    FileCheck
} from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from '@/lib/axios';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Avatar } from "@/components/ui/avatar";
import { Skeleton } from "@/components/ui/skeleton";

interface User {
    name: string;
    avatar: string;
}

interface Auth {
    user: User;
}

const page = usePage<{ auth: Auth }>();
const auth = computed(() => page.props.auth);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
];

const stats = [
    {
        name: 'Total Users',
        value: '2,543',
        change: '+12%',
        changeType: 'increase',
        icon: Users,
        trend: 'up',
        description: 'Active users this month',
    },
    {
        name: 'Active Licenses',
        value: '1,234',
        change: '+8%',
        changeType: 'increase',
        icon: FileText,
        trend: 'up',
        description: 'Valid business licenses',
    },
    {
        name: 'Active Votes',
        value: '12',
        change: '-2',
        changeType: 'decrease',
        icon: Vote,
        trend: 'down',
        description: 'Ongoing voting sessions',
    },
    {
        name: 'Messages Sent',
        value: '3,456',
        change: '+23%',
        changeType: 'increase',
        icon: MessageSquare,
        trend: 'up',
        description: 'System notifications',
    },
];

const recentActivity = [
    {
        id: 1,
        type: 'user',
        title: 'New user registration',
        description: 'John Doe has registered as a new user',
        timestamp: '2 minutes ago',
        icon: Users,
        status: 'success',
    },
    {
        id: 2,
        type: 'license',
        title: 'License renewal',
        description: 'Business license #12345 has been renewed',
        timestamp: '15 minutes ago',
        icon: FileText,
        status: 'success',
    },
    {
        id: 3,
        type: 'vote',
        title: 'New vote created',
        description: 'City Council Meeting Vote has been created',
        timestamp: '1 hour ago',
        icon: Vote,
        status: 'info',
    },
    {
        id: 4,
        type: 'message',
        title: 'System message sent',
        description: 'Bulk notification sent to 500 users',
        timestamp: '2 hours ago',
        icon: MessageSquare,
        status: 'warning',
    },
];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'success':
            return 'text-green-500 dark:text-green-400';
        case 'warning':
            return 'text-yellow-500 dark:text-yellow-400';
        case 'info':
            return 'text-blue-500 dark:text-blue-400';
        default:
            return 'text-gray-500 dark:text-gray-400';
    }
};

const k8sStatus = ref({
    healthy: 0,
    warning: 0,
    error: 0,
    total: 0,
    timestamp: '',
    deployments: []
});

const airflowStatus = ref({
    total: 0,
    running: 0,
    failed: 0,
    dags: []
});

const websiteStatus = ref([]);
const lastUpdated = ref(new Date().toLocaleTimeString());
const updateInterval = ref<number | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

const fetchSystemStatus = async () => {
    try {
        loading.value = true;
        error.value = null;
        
        console.log('[Admin/Dashboard.vue] Axios instance baseURL before request:', axios.defaults.baseURL);
        console.log('Fetching system status...');
        const response = await axios.get('/api/system-status');
        console.log('System status response:', response.data);
        
        // Check if we have an error in the response
        if (response.data.error) {
            throw new Error(response.data.details || response.data.error);
        }
        
        k8sStatus.value = response.data.kubernetes;
        airflowStatus.value = response.data.airflow;
        websiteStatus.value = response.data.websites;
        lastUpdated.value = new Date(response.data.timestamp).toLocaleTimeString();
    } catch (e: any) {
        console.error('Error details:', {
            response: e.response?.data,
            status: e.response?.status,
            headers: e.response?.headers,
            message: e.message
        });
        error.value = e.response?.data?.details || e.response?.data?.error || e.message || 'Failed to fetch system status';
        console.error('Error fetching system status:', e);
        
        // Set default values on error
        k8sStatus.value = {
            healthy: 0,
            warning: 0,
            error: 0,
            total: 0,
            timestamp: '',
            deployments: []
        };
        airflowStatus.value = {
            total: 0,
            running: 0,
            failed: 0,
            dags: []
        };
        websiteStatus.value = [];
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchSystemStatus();
    // Update every 120 seconds
    updateInterval.value = window.setInterval(fetchSystemStatus, 120000);
});

onUnmounted(() => {
    if (updateInterval.value) {
        clearInterval(updateInterval.value);
    }
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout :breadcrumbs="breadcrumbs">
        <div class="container space-y-6 py-8 md:space-y-8">
            <!-- Welcome Section -->
            <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight md:text-3xl">Admin Dashboard</h1>
                    <p class="text-muted-foreground">System overview and health status.</p>
                </div>
                <div class="flex w-full items-center justify-end gap-2 md:w-auto">
                    <p v-if="error" class="text-xs text-destructive md:text-sm">{{ error }}</p>
                    <p class="text-xs text-muted-foreground md:text-sm">Last updated: {{ lastUpdated }}</p>
                    <!-- Consider adding a manual refresh button here -->
                </div>
            </div>

            <!-- Top Stats Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6">
                <!-- Hardcoded Stat Cards - Replace with dynamic data if available -->
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
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-7">
                
                <!-- System Health Card -->
                <Card class="col-span-1 lg:col-span-4">
                    <CardHeader>
                        <CardTitle>System Health</CardTitle>
                        <CardDescription>Overview of infrastructure and service status.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-8"> 
                        <!-- Skeleton Loader for the entire section -->
                        <div v-if="loading" class="space-y-8">
                            <div class="space-y-2">
                                <Skeleton class="h-5 w-1/2" />
                                <div class="flex justify-between">
                                    <Skeleton class="h-4 w-1/4" />
                                    <Skeleton class="h-6 w-20" />
                                </div>
                            </div>
                             <div class="space-y-2">
                                <Skeleton class="h-5 w-1/2" />
                                <div class="flex justify-between">
                                    <Skeleton class="h-4 w-1/4" />
                                    <Skeleton class="h-6 w-20" />
                                </div>
                            </div>
                             <div class="space-y-2">
                                <Skeleton class="h-5 w-1/2" />
                                <Skeleton class="h-4 w-1/3" />
                                <div class="space-y-2 pt-2">
                                    <Skeleton class="h-8 w-full" />
                                    <Skeleton class="h-8 w-full" />
                                    <Skeleton class="h-8 w-full" />
                                </div>
                            </div>
                        </div>

                        <!-- Actual Content -->
                        <div v-else class="space-y-8">
                            <!-- Kubernetes Section -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <Server class="h-5 w-5 text-muted-foreground" />
                                        <h3 class="font-semibold">Kubernetes Deployments</h3>
                                    </div>
                                    <Badge :variant="k8sStatus.error > 0 ? 'destructive' : k8sStatus.warning > 0 ? 'warning' : 'success'">
                                        {{ k8sStatus.healthy }}/{{ k8sStatus.total }} Healthy
                                    </Badge>
                                </div>
                                <!-- Optional: Add more detailed K8s stats if needed -->
                            </div>
                            
                            <!-- Airflow Section -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <Activity class="h-5 w-5 text-muted-foreground" /> 
                                        <h3 class="font-semibold">Airflow DAGs</h3>
                                    </div>
                                    <Badge :variant="airflowStatus.failed > 0 ? 'destructive' : 'success'">
                                        {{ airflowStatus.running }}/{{ airflowStatus.total }} Running
                                    </Badge>
                                </div>
                                <!-- Optional: Add more detailed Airflow stats if needed -->
                            </div>

                            <!-- Website Status Table -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <Globe2 class="h-5 w-5 text-muted-foreground" />
                                        <h3 class="font-semibold">Website Health</h3>
                                    </div>
                                    <!-- Maybe add overall status? -->
                                </div>
                                <Table v-if="websiteStatus.length > 0">
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Site</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead>Latency</TableHead>
                                            <TableHead>Uptime</TableHead>
                                            <TableHead class="text-right">Link</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="site in websiteStatus" :key="site.name">
                                            <TableCell class="font-medium">{{ site.name }}</TableCell>
                                            <TableCell>
                                                <Badge :variant="site.status === 'healthy' ? 'success' : 'destructive'">
                                                    {{ site.status }}
                                                </Badge>
                                            </TableCell>
                                            <TableCell>{{ site.latency }}</TableCell>
                                            <TableCell>{{ site.uptime }}</TableCell>
                                            <TableCell class="text-right">
                                                <Button variant="ghost" size="sm" asChild>
                                                    <a :href="'https://' + site.name" target="_blank" rel="noopener noreferrer">
                                                        Visit
                                                    </a>
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                                <p v-else class="text-sm text-muted-foreground">
                                    Website health data not available.
                                </p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Activity Card -->
                <Card class="col-span-1 lg:col-span-3">
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                        <CardDescription>Latest actions performed in the system.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-3">
                                <Avatar class="h-8 w-8 border">
                                    <component :is="activity.icon" class="h-4 w-4 m-auto text-muted-foreground" />
                                </Avatar>
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium leading-none">{{ activity.title }}</p>
                                        <p class="text-xs text-muted-foreground">{{ activity.timestamp }}</p>
                                    </div>
                                    <p class="text-sm text-muted-foreground">{{ activity.description }}</p>
                                </div>
                            </div>
                            <p v-if="recentActivity.length === 0" class="text-sm text-muted-foreground">
                                No recent activity found.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template> 