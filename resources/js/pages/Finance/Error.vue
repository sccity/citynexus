<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { AlertTriangle } from 'lucide-vue-next';

// Define props for our component
const props = defineProps<{
  error: string;
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
];

// Function to reload the page
const reloadPage = () => {
  window.location.reload();
};
</script>

<template>
  <Head title="Finance API Error" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Finance API Error</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-700">
                    Finance API Error
                  </h3>
                  <div class="mt-2 text-sm text-red-600">
                    <p>{{ error }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-4">What happened?</h3>
              <p class="mb-4">
                There was an error while trying to fetch data from the Finance API. This could be due to:
              </p>
              <ul class="list-disc pl-5 mb-4 space-y-2">
                <li>The API endpoint might be temporarily unavailable</li>
                <li>There might be connectivity issues with the external API server</li>
                <li>The endpoint might have changed or been removed</li>
                <li>The authentication token might have expired or is invalid</li>
              </ul>
            </div>
            
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-4">What can you do?</h3>
              <ul class="list-disc pl-5 mb-4 space-y-2">
                <li>Try again later - the issue might be temporary</li>
                <li>Contact the API administrator to check if the endpoint is available</li>
                <li>Verify that your authentication token is valid</li>
              </ul>
            </div>
            
            <div class="flex mt-6">
              <Link :href="route('finance.dashboard')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                Return to Dashboard
              </Link>
              <button 
                @click="reloadPage" 
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
              >
                Try Again
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 