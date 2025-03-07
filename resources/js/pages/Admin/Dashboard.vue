<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Users, Lock, Settings, FileBarChart, TrendingUp, AlertCircle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: '/admin',
    },
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
];

defineProps<{
  permissions?: string[];
}>();
</script>

<template>
  <Head title="Admin Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 p-6">
      <h1 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">Admin Dashboard</h1>
      
      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- User Management Card -->
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
          <div class="flex items-center gap-4">
            <div class="bg-sage-green text-sage-green-foreground flex h-12 w-12 items-center justify-center rounded-lg">
              <Users class="h-6 w-6" />
            </div>
            <div>
              <h3 class="text-lg font-medium">User Management</h3>
              <p class="text-muted-foreground text-sm">Manage users, roles and permissions</p>
            </div>
          </div>
          <div class="mt-6">
            <a href="/users/manage" class="bg-sage-green/10 hover:bg-sage-green/20 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors">
              Manage Users
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="m9 18 6-6-6-6"/></svg>
            </a>
          </div>
        </div>
        
        <!-- Permissions Card -->
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
          <div class="flex items-center gap-4">
            <div class="bg-desert-sand text-primary-foreground flex h-12 w-12 items-center justify-center rounded-lg">
              <Lock class="h-6 w-6" />
            </div>
            <div>
              <h3 class="text-lg font-medium">Permissions</h3>
              <p class="text-muted-foreground text-sm">Configure role-based access control</p>
            </div>
          </div>
          <div class="mt-6">
            <a href="#" class="bg-desert-sand/10 hover:bg-desert-sand/20 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors">
              Manage Permissions
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="m9 18 6-6-6-6"/></svg>
            </a>
          </div>
        </div>
        
        <!-- Settings Card -->
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
          <div class="flex items-center gap-4">
            <div class="bg-sandstone text-accent-foreground flex h-12 w-12 items-center justify-center rounded-lg">
              <Settings class="h-6 w-6" />
            </div>
            <div>
              <h3 class="text-lg font-medium">System Settings</h3>
              <p class="text-muted-foreground text-sm">Configure application settings</p>
            </div>
          </div>
          <div class="mt-6">
            <a href="#" class="bg-sandstone/10 hover:bg-sandstone/20 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors">
              System Settings
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="m9 18 6-6-6-6"/></svg>
            </a>
          </div>
        </div>
      </div>
      
      <!-- Stats and Analytics -->
      <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="text-xl font-medium">System Analytics</h3>
          <div class="flex gap-2">
            <button class="bg-muted text-muted-foreground hover:bg-muted/80 rounded-md px-3 py-1 text-sm">Daily</button>
            <button class="bg-primary text-primary-foreground hover:bg-primary/90 rounded-md px-3 py-1 text-sm">Weekly</button>
            <button class="bg-muted text-muted-foreground hover:bg-muted/80 rounded-md px-3 py-1 text-sm">Monthly</button>
          </div>
        </div>
        
        <div class="grid gap-6 md:grid-cols-3">
          <div class="flex flex-col items-center rounded-lg border border-border p-4">
            <div class="text-sage-green mb-2">
              <TrendingUp class="h-6 w-6" />
            </div>
            <p class="text-2xl font-bold">87%</p>
            <p class="text-muted-foreground text-center text-sm">System Uptime</p>
          </div>
          
          <div class="flex flex-col items-center rounded-lg border border-border p-4">
            <div class="text-desert-sand mb-2">
              <Users class="h-6 w-6" />
            </div>
            <p class="text-2xl font-bold">24</p>
            <p class="text-muted-foreground text-center text-sm">Active Users Today</p>
          </div>
          
          <div class="flex flex-col items-center rounded-lg border border-border p-4">
            <div class="text-sandstone mb-2">
              <FileBarChart class="h-6 w-6" />
            </div>
            <p class="text-2xl font-bold">142</p>
            <p class="text-muted-foreground text-center text-sm">Reports Generated</p>
          </div>
        </div>
      </div>
      
      <!-- Admin Permissions -->
      <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
        <div class="mb-4 flex items-center gap-3">
          <AlertCircle class="text-sage-green h-5 w-5" />
          <h3 class="text-xl font-medium">Your Admin Permissions</h3>
        </div>
        
        <div v-if="permissions && permissions.length" class="bg-muted rounded-lg p-4">
          <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="permission in permissions" :key="permission" class="flex items-center gap-2">
              <div class="text-primary flex h-6 w-6 items-center justify-center rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
              </div>
              <span>{{ permission }}</span>
            </li>
          </ul>
        </div>
        <p v-else class="text-muted-foreground italic">No specific permissions found.</p>
      </div>
    </div>
  </AppLayout>
</template> 