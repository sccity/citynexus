<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { FileCheck, Building, Calendar, Search } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Business License',
        href: '/business-license',
    },
];

defineProps<{
    user_permissions?: string[];
}>();
</script>

<template>
    <Head title="Business License" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Business License Management</h1>
                
                <div class="flex gap-2">
                    <button 
                        v-if="user_permissions?.includes('license-create')" 
                        class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex h-9 items-center justify-center rounded-md px-4 py-2 text-sm font-medium shadow-sm transition-colors"
                    >
                        New License
                    </button>
                </div>
            </div>
            
            <!-- License Summary Cards -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Active Licenses</h3>
                        <div class="bg-desert-sand text-primary-foreground flex h-10 w-10 items-center justify-center rounded-lg">
                            <FileCheck class="h-5 w-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-3xl font-bold">834</p>
                    <p class="text-muted-foreground mt-2 text-sm">Currently active</p>
                </div>
                
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">New Applications</h3>
                        <div class="bg-sage-green text-sage-green-foreground flex h-10 w-10 items-center justify-center rounded-lg">
                            <Building class="h-5 w-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-3xl font-bold">28</p>
                    <p class="text-muted-foreground mt-2 text-sm">Pending review</p>
                </div>
                
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">Renewals Due</h3>
                        <div class="bg-sandstone text-accent-foreground flex h-10 w-10 items-center justify-center rounded-lg">
                            <Calendar class="h-5 w-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-3xl font-bold">56</p>
                    <p class="text-muted-foreground mt-2 text-sm">Within 30 days</p>
                </div>
            </div>
            
            <!-- License Search -->
            <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-medium">Search Business Licenses</h3>
                </div>
                
                <div v-if="user_permissions?.includes('license-search')" class="mb-6">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <Search class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <input 
                                    type="text" 
                                    placeholder="Search by business name, license number, or address"
                                    class="border-border bg-background text-foreground placeholder:text-muted-foreground focus-visible:ring-ring block w-full rounded-md border p-2 py-2 pl-10 text-sm focus-visible:outline-none focus-visible:ring-2"
                                />
                            </div>
                        </div>
                        <button class="bg-primary text-primary-foreground hover:bg-primary/90 inline-flex h-9 items-center justify-center rounded-md px-4 py-2 text-sm font-medium shadow-sm transition-colors">
                            Search
                        </button>
                    </div>
                </div>
                
                <div v-if="!user_permissions?.includes('license-search')" class="p-6 text-center">
                    <p class="text-muted-foreground">You don't have permission to search business licenses.</p>
                </div>
                
                <!-- License Table -->
                <div v-if="user_permissions?.includes('license-view')" class="relative overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs uppercase">
                            <tr>
                                <th scope="col" class="px-6 py-3">Business Name</th>
                                <th scope="col" class="px-6 py-3">License #</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Expiration</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">City Café</td>
                                <td class="px-6 py-4">BL-2023-0042</td>
                                <td class="px-6 py-4">Restaurant</td>
                                <td class="px-6 py-4">
                                    <span class="bg-sage-green/20 text-sage-green rounded-full px-2 py-1 text-xs">Active</span>
                                </td>
                                <td class="px-6 py-4">Dec 31, 2023</td>
                                <td class="px-6 py-4">
                                    <button 
                                        v-if="user_permissions?.includes('license-edit')"
                                        class="text-sage-green hover:underline mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button class="text-primary hover:underline">
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="whitespace-nowrap px-6 py-4 font-medium">Main Street Hardware</td>
                                <td class="px-6 py-4">BL-2023-0128</td>
                                <td class="px-6 py-4">Retail</td>
                                <td class="px-6 py-4">
                                    <span class="bg-sandstone/20 text-accent rounded-full px-2 py-1 text-xs">Renewal</span>
                                </td>
                                <td class="px-6 py-4">Nov 15, 2023</td>
                                <td class="px-6 py-4">
                                    <button 
                                        v-if="user_permissions?.includes('license-edit')"
                                        class="text-sage-green hover:underline mr-2"
                                    >
                                        Edit
                                    </button>
                                    <button class="text-primary hover:underline">
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 font-medium">Valley Fitness Center</td>
                                <td class="px-6 py-4">BL-2023-0215</td>
                                <td class="px-6 py-4">Recreation</td>
                                <td class="px-6 py-4">
                                    <span class="bg-desert-sand/20 text-desert-sand rounded-full px-2 py-1 text-xs">Pending</span>
                                </td>
                                <td class="px-6 py-4">-</td>
                                <td class="px-6 py-4">
                                    <button 
                                        v-if="user_permissions?.includes('license-approve')"
                                        class="text-sage-green hover:underline mr-2"
                                    >
                                        Approve
                                    </button>
                                    <button class="text-primary hover:underline">
                                        View
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-if="!user_permissions?.includes('license-view')" class="p-6 text-center">
                    <p class="text-muted-foreground">You don't have permission to view business licenses.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 