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

interface WebsiteStatus {
    name: string;
    status: 'healthy' | 'error' | string; // Be more specific if possible
    latency: string;
    uptime: string;
}

interface K8sDeployment {
    name: string;
    status: 'healthy' | 'warning' | 'error' | string; // Be more specific if possible
    namespace?: string;
    // Add other relevant fields from your API response
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
    deployments: [] as K8sDeployment[] // Type the deployments array
});

const airflowStatus = ref({
    total: 0,
    running: 0,
    failed: 0,
    dags: []
});

const websiteStatus = ref<WebsiteStatus[]>([]); // Type the websiteStatus array
const lastUpdated = ref(new Date().toLocaleTimeString());
const updateInterval = ref<number | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

const sortedWebsiteStatus = computed(() => {
    return [...websiteStatus.value].sort((a, b) => {
        const aIsHealthy = a.status === 'healthy';
        const bIsHealthy = b.status === 'healthy';
        if (aIsHealthy && !bIsHealthy) {
            return 1; // a (healthy) comes after b (not healthy)
        }
        if (!aIsHealthy && bIsHealthy) {
            return -1; // a (not healthy) comes before b (healthy)
        }
        return a.name.localeCompare(b.name); // Otherwise, sort by name
    });
});

const problemDeployments = computed(() => {
    return k8sStatus.value.deployments.filter(d => d.status !== 'healthy');
});

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

            <!-- Main Content Grid: Changed to 2x2 on large screens -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                
                <!-- Kubernetes Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Server class="h-5 w-5" />
                            Kubernetes Status
                        </CardTitle>
                        <CardDescription>Deployment health overview.</CardDescription>
                    </CardHeader>
                    <CardContent>
                         <!-- Skeleton for K8s -->
                         <div v-if="loading" class="space-y-4">
                            <div class="flex justify-between">
                                <Skeleton class="h-4 w-1/4" />
                                <Skeleton class="h-6 w-20" />
                            </div>
                            <Skeleton class="h-4 w-1/3 mt-2" /> 
                            <div class="space-y-2 pt-2">
                                <Skeleton class="h-5 w-full" />
                                <Skeleton class="h-5 w-full" />
                            </div>
                        </div>
                         <!-- Actual K8s Content -->
                         <div v-else class="space-y-4">
                             <div class="flex items-center justify-between">
                                 <span class="text-sm text-muted-foreground">Overall Deployment Status</span>
                                <Badge :variant="k8sStatus.error > 0 ? 'destructive' : k8sStatus.warning > 0 ? 'warning' : 'success'">
                                    {{ k8sStatus.healthy }}/{{ k8sStatus.total }} Healthy
                                </Badge>
                             </div>
                             <!-- List Problematic Deployments -->
                             <div v-if="problemDeployments.length > 0" class="pt-2">
                                <h4 class="mb-2 text-sm font-medium text-amber-600 dark:text-amber-500">Issues Detected:</h4>
                                <ul class="space-y-1">
                                    <li v-for="dep in problemDeployments" :key="dep.name" class="text-xs flex items-center gap-2" :class="{'animate-pulse': dep.status === 'error' || dep.status === 'warning'}">
                                        <Badge :variant="dep.status === 'error' ? 'destructive' : 'warning'" size="sm">{{ dep.status }}</Badge>
                                        <span>{{ dep.name }} <span v-if="dep.namespace" class="text-muted-foreground">({{ dep.namespace }})</span></span>
                                    </li>
                                </ul>
                             </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Airflow Card -->
                <Card>
                    <CardHeader>
                         <CardTitle class="flex items-center gap-2">
                            <Activity class="h-5 w-5" /> 
                            Airflow Status
                        </CardTitle>
                        <CardDescription>DAG execution summary.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="loading" class="space-y-2">
                            <Skeleton class="h-6 w-24" /> 
                            <Skeleton class="h-4 w-1/2" />
                        </div>
                         <div v-else class="flex items-center justify-between">
                             <span class="text-sm text-muted-foreground">DAG Run Status</span>
                            <Badge :variant="airflowStatus.failed > 0 ? 'destructive' : 'success'">
                                {{ airflowStatus.running }}/{{ airflowStatus.total }} Running
                            </Badge>
                        </div>
                    </CardContent>
                </Card>

                <!-- Website Status Card -->
                <Card>
                    <CardHeader>
                         <CardTitle class="flex items-center gap-2">
                            <Globe2 class="h-5 w-5" />
                            Website Health
                        </CardTitle>
                         <CardDescription>Public website availability and performance.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <!-- Skeleton for Table -->
                         <div v-if="loading" class="space-y-2 pt-2">
                            <Skeleton class="h-8 w-full" />
                            <Skeleton class="h-8 w-full" />
                            <Skeleton class="h-8 w-full" />
                        </div>
                        <!-- Scrollable Table Container -->
                        <div v-else class="max-h-[350px] overflow-y-auto relative">
                            <!-- Use sortedWebsiteStatus -->
                            <Table v-if="sortedWebsiteStatus.length > 0">
                                <TableHeader class="sticky top-0 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                                    <TableRow>
                                        <TableHead>Site</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead class="hidden sm:table-cell">Latency</TableHead>
                                        <TableHead class="hidden md:table-cell">Uptime</TableHead>
                                        <TableHead class="text-right">Link</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                     <!-- Use sortedWebsiteStatus -->
                                    <TableRow v-for="site in sortedWebsiteStatus" :key="site.name">
                                        <TableCell class="font-medium">{{ site.name }}</TableCell>
                                        <TableCell>
                                            <!-- Add conditional pulse animation -->
                                            <Badge 
                                                :variant="site.status === 'healthy' ? 'success' : 'destructive'" 
                                                class="text-xs" 
                                                :class="{'animate-pulse': site.status !== 'healthy'}"
                                            >
                                                {{ site.status }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="hidden sm:table-cell">{{ site.latency }}</TableCell>
                                        <TableCell class="hidden md:table-cell">{{ site.uptime }}</TableCell>
                                        <TableCell class="text-right">
                                            <!-- Fix button size -->
                                            <Button variant="ghost" size="sm" asChild> 
                                                <a :href="'https://' + site.name" target="_blank" rel="noopener noreferrer">
                                                    Visit
                                                </a>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                            <p v-else class="text-sm text-muted-foreground pt-4">
                                Website health data not available.
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Activity Card (Remains the same) -->
                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                        <CardDescription>Latest actions performed in the system.</CardDescription>
                    </CardHeader>
                    <CardContent>
                         <div v-if="loading" class="space-y-4">
                             <div class="flex items-start space-x-3">
                                <Skeleton class="h-8 w-8 rounded-full" />
                                <div class="flex-1 space-y-1">
                                    <Skeleton class="h-4 w-3/4" />
                                    <Skeleton class="h-4 w-1/2" />
                                </div>
                            </div>
                             <div class="flex items-start space-x-3">
                                <Skeleton class="h-8 w-8 rounded-full" />
                                <div class="flex-1 space-y-1">
                                    <Skeleton class="h-4 w-3/4" />
                                    <Skeleton class="h-4 w-1/2" />
                                </div>
                            </div>
                        </div>
                         <div v-else class="space-y-4">
                            <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start space-x-3">
                                <Avatar class="h-8 w-8 border">
                                    <!-- Assuming activity.icon is a Vue component -->
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