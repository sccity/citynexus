<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Define props for our component
const props = defineProps<{
  budgetData: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Budget',
        href: '/budget',
    },
    {
        title: 'Finance API',
        href: '/budget/finance-dashboard',
    },
    {
        title: 'Budget Data',
        href: '/budget/finance-budget',
    },
];
</script>

<template>
  <Head title="Finance API Budget Data" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-xl font-semibold">Finance API Budget Data</h2>
              <Link 
                :href="route('finance.dashboard')" 
                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Back to Dashboard
              </Link>
            </div>
            
            <div class="space-y-6">
              <div>
                <h3 class="text-lg font-medium mb-2">Budget Information</h3>
                <pre class="bg-gray-100 p-4 rounded overflow-auto max-h-[70vh]">{{ JSON.stringify(budgetData, null, 2) }}</pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 