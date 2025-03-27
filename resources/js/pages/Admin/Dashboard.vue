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
        
        console.log('Fetching system status...');
        const response = await axios.get('system-status');
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
        <div class="container space-y-6 py-8">
            <!-- Welcome Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Admin Dashboard</h1>
                    <p class="text-muted-foreground">System overview and health status</p>
                </div>
                <div class="flex items-center gap-2">
                    <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
                    <p class="text-sm text-muted-foreground">Last updated: {{ lastUpdated }}</p>
                </div>
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
                <!-- System Health -->
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>System Health</CardTitle>
                        <CardDescription>Infrastructure and service status</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-6">
                            <!-- Kubernetes Status -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-medium">Kubernetes Deployments</h3>
                                    <Badge :variant="k8sStatus.error > 0 ? 'destructive' : k8sStatus.warning > 0 ? 'warning' : 'success'">
                                        {{ k8sStatus.healthy }}/{{ k8sStatus.total }} Healthy
                                    </Badge>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="flex flex-col items-center">
                                        <Badge variant="success" class="mb-1">{{ k8sStatus.healthy }}</Badge>
                                        <span class="text-xs text-muted-foreground">Healthy</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <Badge variant="warning" class="mb-1">{{ k8sStatus.warning }}</Badge>
                                        <span class="text-xs text-muted-foreground">Warning</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <Badge variant="destructive" class="mb-1">{{ k8sStatus.error }}</Badge>
                                        <span class="text-xs text-muted-foreground">Error</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Airflow Status -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-medium">Airflow DAGs</h3>
                                    <Badge :variant="airflowStatus.failed > 0 ? 'destructive' : 'success'">
                                        {{ airflowStatus.running }}/{{ airflowStatus.total }} Running
                                    </Badge>
                                </div>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="flex flex-col items-center">
                                        <Badge variant="default" class="mb-1">{{ airflowStatus.total }}</Badge>
                                        <span class="text-xs text-muted-foreground">Total</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <Badge variant="success" class="mb-1">{{ airflowStatus.running }}</Badge>
                                        <span class="text-xs text-muted-foreground">Running</span>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <Badge variant="destructive" class="mb-1">{{ airflowStatus.failed }}</Badge>
                                        <span class="text-xs text-muted-foreground">Failed</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Website Status -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-medium">Website Health</h3>
                                    <Badge variant="success">All Systems Operational</Badge>
                                </div>
                                <div class="space-y-2">
                                    <div v-for="site in websiteStatus" :key="site.name" class="flex items-center justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium">{{ site.name }}</span>
                                                <Badge :variant="site.status === 'healthy' ? 'success' : 'destructive'">
                                                    {{ site.status }}
                                                </Badge>
                                            </div>
                                            <div class="mt-1 flex items-center gap-2 text-xs text-muted-foreground">
                                                <span>{{ site.latency }}</span>
                                                <span>·</span>
                                                <span>Uptime {{ site.uptime }}</span>
                                            </div>
                                        </div>
                                        <Button variant="ghost" size="sm" asChild>
                                            <a :href="'https://' + site.name" target="_blank" rel="noopener noreferrer">
                                                Visit →
                                            </a>
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Quick Actions -->
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Quick Actions</CardTitle>
                        <CardDescription>Common admin tasks and shortcuts</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <Button variant="outline" class="w-full justify-start">
                                <Users class="mr-2 h-4 w-4" />
                                Manage Users
                            </Button>
                            <Button variant="outline" class="w-full justify-start">
                                <Settings class="mr-2 h-4 w-4" />
                                System Settings
                            </Button>
                            <Button variant="outline" class="w-full justify-start">
                                <Activity class="mr-2 h-4 w-4" />
                                View Health Details
                            </Button>
                            <Button variant="outline" class="w-full justify-start">
                                <Database class="mr-2 h-4 w-4" />
                                Database Management
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AdminLayout>
</template> 