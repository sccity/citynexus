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
    Activity
} from 'lucide-vue-next';

interface User {
    name: string;
    avatar: string;
}

interface Auth {
    user: User;
}

const page = usePage<{ auth: Auth }>();
const auth = computed(() => page.props.auth);

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
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Admin Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Welcome back, {{ auth.user.name }}. Here's what's happening with your system.
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <Activity class="mr-2 h-4 w-4" />
                        Generate Report
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="stat in stats"
                    :key="stat.name"
                    class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800"
                >
                    <dt>
                        <div class="absolute rounded-lg bg-primary/10 p-3 dark:bg-primary/20">
                            <component
                                :is="stat.icon"
                                class="h-6 w-6 text-primary"
                                aria-hidden="true"
                            />
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ stat.name }}
                        </p>
                    </dt>
                    <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ stat.value }}
                        </p>
                        <p
                            :class="[
                                stat.changeType === 'increase' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400',
                                'ml-2 flex items-baseline text-sm font-semibold'
                            ]"
                        >
                            <component
                                :is="stat.trend === 'up' ? ArrowUpRight : ArrowDownRight"
                                :class="[
                                    stat.changeType === 'increase' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400',
                                    'h-5 w-5 flex-shrink-0 self-center'
                                ]"
                                aria-hidden="true"
                            />
                            {{ stat.change }}
                        </p>
                    </dd>
                    <p class="ml-16 text-sm text-gray-500 dark:text-gray-400">
                        {{ stat.description }}
                    </p>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow-sm rounded-xl dark:bg-gray-800">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                        Recent Activity
                    </h3>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div
                        v-for="activity in recentActivity"
                        :key="activity.id"
                        class="px-6 py-4"
                    >
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="relative">
                                    <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center dark:bg-primary/20">
                                        <component
                                            :is="activity.icon"
                                            class="h-5 w-5 text-primary"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <div
                                        class="absolute -top-1 -right-1 h-3 w-3 rounded-full"
                                        :class="getStatusColor(activity.status)"
                                    >
                                        <div
                                            class="h-full w-full rounded-full"
                                            :class="getStatusColor(activity.status)"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ activity.title }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ activity.description }}
                                </p>
                            </div>
                            <div>
                                <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{ activity.timestamp }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template> 