<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type KubernetesStatus, type AirflowStatus, type Website } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { 
    CalendarDays, Users, Building, FileBarChart, 
    CheckCircle2, XCircle, AlertCircle, Server, 
    Globe2, Activity, Clock, Database 
} from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import axios from '@/lib/axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    name?: string;
}>();

const k8sStatus = ref<KubernetesStatus>({
    healthy: 0,
    warning: 0,
    error: 0,
    total: 0,
    timestamp: '',
    deployments: []
});

const airflowStatus = ref<AirflowStatus>({
    total: 0,
    running: 0,
    failed: 0,
    dags: []
});

const websiteStatus = ref<Website[]>([]);
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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container space-y-6 py-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold tracking-tight">System Status</h1>
                <div class="flex items-center gap-4">
                    <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
                    <p class="text-sm text-muted-foreground">Last updated: {{ lastUpdated }}</p>
                </div>
            </div>
            
            <!-- Service Health Overview -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Kubernetes Status -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Server class="h-5 w-5 text-primary" />
                            Kubernetes
                        </CardTitle>
                        <CardDescription>Deployment Status</CardDescription>
                    </CardHeader>
                    <CardContent>
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
                        <div class="mt-4">
                            <Button variant="outline" class="w-full" size="sm">View Details</Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Airflow Status -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Activity class="h-5 w-5 text-primary" />
                            Airflow
                        </CardTitle>
                        <CardDescription>DAG Status</CardDescription>
                    </CardHeader>
                    <CardContent>
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
                        <div class="mt-4">
                            <Button variant="outline" class="w-full" size="sm">View DAGs</Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Website Status -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Globe2 class="h-5 w-5 text-primary" />
                            Websites
                        </CardTitle>
                        <CardDescription>Service Health</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
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
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
