<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

// Define props for our component
const props = defineProps<{
  budgetData: any;
  employeeData: any;
  expenseData: any;
  pastDueData: any;
  revenueData: any;
  errors?: Record<string, string>;
}>();

const activeTab = ref('budget');

const setActiveTab = (tab: string) => {
  activeTab.value = tab;
};

// Helper function to check if a tab has data or errors
const hasError = (tabName: string): boolean => {
  if (!props.errors) return false;
  
  switch(tabName) {
    case 'budget': return !!props.errors.budget;
    case 'employees': return !!props.errors.employee;
    case 'expenses': return !!props.errors.expense;
    case 'pastdue': return !!props.errors.pastdue;
    case 'revenue': return !!props.errors.revenue;
    default: return false;
  }
};

const getErrorMessage = (tabName: string): string => {
  if (!props.errors) return '';
  
  switch(tabName) {
    case 'budget': return props.errors.budget || '';
    case 'employees': return props.errors.employee || '';
    case 'expenses': return props.errors.expense || '';
    case 'pastdue': return props.errors.pastdue || '';
    case 'revenue': return props.errors.revenue || '';
    default: return '';
  }
};
</script>

<template>
  <Head title="Finance Dashboard" />

  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Finance Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="mb-6">
              <h3 class="text-lg font-medium mb-4">Finance Data Overview</h3>
              <p class="mb-4">
                This dashboard displays financial data from various endpoints of the external Finance API.
                Use the tabs below to explore different datasets.
              </p>
              
              <!-- Navigation Tabs -->
              <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                  <button
                    @click="setActiveTab('budget')"
                    :class="[
                      activeTab === 'budget'
                        ? 'border-blue-500 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                      'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm relative'
                    ]"
                  >
                    Budget
                    <span v-if="hasError('budget')" class="absolute top-2 right-0 h-2 w-2 rounded-full bg-red-600"></span>
                  </button>
                  <button
                    @click="setActiveTab('employees')"
                    :class="[
                      activeTab === 'employees'
                        ? 'border-blue-500 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                      'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm relative'
                    ]"
                  >
                    Employees
                    <span v-if="hasError('employees')" class="absolute top-2 right-0 h-2 w-2 rounded-full bg-red-600"></span>
                  </button>
                  <button
                    @click="setActiveTab('expenses')"
                    :class="[
                      activeTab === 'expenses'
                        ? 'border-blue-500 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                      'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm relative'
                    ]"
                  >
                    Expense Details
                    <span v-if="hasError('expenses')" class="absolute top-2 right-0 h-2 w-2 rounded-full bg-red-600"></span>
                  </button>
                  <button
                    @click="setActiveTab('pastdue')"
                    :class="[
                      activeTab === 'pastdue'
                        ? 'border-blue-500 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                      'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm relative'
                    ]"
                  >
                    Past Due
                    <span v-if="hasError('pastdue')" class="absolute top-2 right-0 h-2 w-2 rounded-full bg-red-600"></span>
                  </button>
                  <button
                    @click="setActiveTab('revenue')"
                    :class="[
                      activeTab === 'revenue'
                        ? 'border-blue-500 text-blue-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                      'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm relative'
                    ]"
                  >
                    Revenue Details
                    <span v-if="hasError('revenue')" class="absolute top-2 right-0 h-2 w-2 rounded-full bg-red-600"></span>
                  </button>
                </nav>
              </div>
              
              <!-- Tab Content -->
              <div class="mt-6">
                <!-- Budget Data -->
                <div v-if="activeTab === 'budget'" class="space-y-4">
                  <h4 class="text-lg font-medium">Budget Information</h4>
                  <div v-if="hasError('budget')" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <p class="font-medium">Error loading budget data</p>
                    <p class="text-sm">{{ getErrorMessage('budget') }}</p>
                  </div>
                  <pre v-else class="bg-gray-100 p-4 rounded overflow-auto max-h-96">{{ JSON.stringify(budgetData, null, 2) }}</pre>
                  <div class="mt-4">
                    <Link :href="route('finance.budget')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      View Detailed Budget Data
                    </Link>
                  </div>
                </div>
                
                <!-- Employee Data -->
                <div v-if="activeTab === 'employees'" class="space-y-4">
                  <h4 class="text-lg font-medium">Employee Information</h4>
                  <div v-if="hasError('employees')" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <p class="font-medium">Error loading employee data</p>
                    <p class="text-sm">{{ getErrorMessage('employees') }}</p>
                  </div>
                  <pre v-else class="bg-gray-100 p-4 rounded overflow-auto max-h-96">{{ JSON.stringify(employeeData, null, 2) }}</pre>
                  <div class="mt-4">
                    <Link :href="route('finance.employees')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      View Detailed Employee Data
                    </Link>
                  </div>
                </div>
                
                <!-- Expense Data -->
                <div v-if="activeTab === 'expenses'" class="space-y-4">
                  <h4 class="text-lg font-medium">Expense Details</h4>
                  <div v-if="hasError('expenses')" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <p class="font-medium">Error loading expense data</p>
                    <p class="text-sm">{{ getErrorMessage('expenses') }}</p>
                    <p class="mt-2 text-sm">This endpoint appears to be unavailable on the API server.</p>
                  </div>
                  <pre v-else class="bg-gray-100 p-4 rounded overflow-auto max-h-96">{{ JSON.stringify(expenseData, null, 2) }}</pre>
                  <div class="mt-4">
                    <Link :href="route('finance.expense-details')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      View Detailed Expense Data
                    </Link>
                  </div>
                </div>
                
                <!-- Past Due Data -->
                <div v-if="activeTab === 'pastdue'" class="space-y-4">
                  <h4 class="text-lg font-medium">Past Due Information</h4>
                  <div v-if="hasError('pastdue')" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <p class="font-medium">Error loading past due data</p>
                    <p class="text-sm">{{ getErrorMessage('pastdue') }}</p>
                  </div>
                  <pre v-else class="bg-gray-100 p-4 rounded overflow-auto max-h-96">{{ JSON.stringify(pastDueData, null, 2) }}</pre>
                  <div class="mt-4">
                    <Link :href="route('finance.past-due')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      View Detailed Past Due Data
                    </Link>
                  </div>
                </div>
                
                <!-- Revenue Data -->
                <div v-if="activeTab === 'revenue'" class="space-y-4">
                  <h4 class="text-lg font-medium">Revenue Details</h4>
                  <div v-if="hasError('revenue')" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    <p class="font-medium">Error loading revenue data</p>
                    <p class="text-sm">{{ getErrorMessage('revenue') }}</p>
                  </div>
                  <pre v-else class="bg-gray-100 p-4 rounded overflow-auto max-h-96">{{ JSON.stringify(revenueData, null, 2) }}</pre>
                  <div class="mt-4">
                    <Link :href="route('finance.revenue-details')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                      View Detailed Revenue Data
                    </Link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 