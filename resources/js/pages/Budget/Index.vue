<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { DollarSign, CreditCard, Banknote, LineChart, Database } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Budget',
        href: '/budget',
    },
];

defineProps<{
    user_permissions?: string[];
}>();
</script>

<template>
    <Head title="Budget Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Budget Management</h1>
                
                <div class="flex gap-2">
                    <Link
                        v-if="user_permissions?.includes('budget-view')"
                        :href="route('finance.dashboard')"
                        class="border-border bg-sage-green text-white hover:bg-sage-green/90 inline-flex h-9 items-center justify-center rounded-md px-4 py-2 text-sm font-medium shadow-sm transition-colors"
                    >
                        <Database class="mr-2 h-4 w-4" />
                        Finance API Data
                    </Link>
                    
                    <button 
                        v-if="user_permissions?.includes('budget-create')" 
                        class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex h-9 items-center justify-center rounded-md px-4 py-2 text-sm font-medium shadow-sm transition-colors"
                    >
                        Create Budget
                    </button>
                </div>
            </div>
            
            <!-- Budget Summary Cards -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Total Budget</h3>
                        <div class="bg-desert-sand text-primary-foreground flex h-10 w-10 items-center justify-center rounded-lg">
                            <DollarSign class="h-5 w-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-3xl font-bold">$2.4M</p>
                    <p class="text-muted-foreground mt-2 text-sm">Fiscal Year 2023</p>
                </div>
                
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Expenditures</h3>
                        <div class="bg-sage-green text-sage-green-foreground flex h-10 w-10 items-center justify-center rounded-lg">
                            <CreditCard class="h-5 w-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-3xl font-bold">$1.1M</p>
                    <p class="text-muted-foreground mt-2 text-sm">45% of total budget</p>
                </div>
                
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Revenue</h3>
                        <div class="bg-sandstone text-accent-foreground flex h-10 w-10 items-center justify-center rounded-lg">
                            <Banknote class="h-5 w-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-3xl font-bold">$1.8M</p>
                    <p class="text-muted-foreground mt-2 text-sm">75% of annual target</p>
                </div>
            </div>
            
            <!-- Finance API Integration Banner -->
            <div class="bg-card rounded-xl border border-border p-6 shadow-sm bg-desert-sand/10">
                <div class="flex items-center">
                    <Database class="h-6 w-6 mr-3 text-sage-green" />
                    <div>
                        <h3 class="text-lg font-medium">External Finance API Integration</h3>
                        <p class="text-muted-foreground mt-1">
                            Access real-time financial data from the external Finance API including budget information, 
                            employee details, expense breakdowns, past due accounts, and revenue details.
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <Link 
                        :href="route('finance.dashboard')" 
                        class="bg-sage-green text-white hover:bg-sage-green/90 inline-flex h-9 items-center justify-center rounded-md px-4 py-2 text-sm font-medium shadow-sm transition-colors"
                    >
                        View Finance API Dashboard
                    </Link>
                </div>
            </div>
            
            <!-- Budget Data Table -->
            <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-medium">Department Budgets</h3>
                    
                    <div v-if="user_permissions?.includes('budget-export')" class="flex items-center gap-2">
                        <button class="border-border hover:bg-accent hover:text-accent-foreground inline-flex h-8 items-center justify-center rounded-md border px-3 text-sm transition-colors">
                            Export
                        </button>
                    </div>
                </div>
                
                <div class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs uppercase">
                            <tr>
                                <th scope="col" class="px-6 py-3">Department</th>
                                <th scope="col" class="px-6 py-3">Allocation</th>
                                <th scope="col" class="px-6 py-3">Spent</th>
                                <th scope="col" class="px-6 py-3">Remaining</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">Public Works</td>
                                <td class="px-6 py-4">$750,000</td>
                                <td class="px-6 py-4">$350,000</td>
                                <td class="px-6 py-4">$400,000</td>
                                <td class="px-6 py-4">
                                    <button 
                                        v-if="user_permissions?.includes('budget-edit')"
                                        class="text-sage-green hover:underline mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        v-if="user_permissions?.includes('budget-view')"
                                        class="text-primary hover:underline"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">Parks & Recreation</td>
                                <td class="px-6 py-4">$500,000</td>
                                <td class="px-6 py-4">$275,000</td>
                                <td class="px-6 py-4">$225,000</td>
                                <td class="px-6 py-4">
                                    <button 
                                        v-if="user_permissions?.includes('budget-edit')"
                                        class="text-sage-green hover:underline mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        v-if="user_permissions?.includes('budget-view')"
                                        class="text-primary hover:underline"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-medium">Administration</td>
                                <td class="px-6 py-4">$350,000</td>
                                <td class="px-6 py-4">$190,000</td>
                                <td class="px-6 py-4">$160,000</td>
                                <td class="px-6 py-4">
                                    <button 
                                        v-if="user_permissions?.includes('budget-edit')"
                                        class="text-sage-green hover:underline mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button 
                                        v-if="user_permissions?.includes('budget-view')"
                                        class="text-primary hover:underline"
                                    >
                                        View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 