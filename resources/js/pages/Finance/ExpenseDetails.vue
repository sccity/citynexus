<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

// Define props for our component
const props = defineProps<{
  data: any;
}>();

// Set up search functionality
const searchQuery = ref('');
const sortBy = ref('amount');
const sortDirection = ref('desc');

// Compute filtered and sorted data
const filteredData = computed(() => {
  let result = props.data;
  
  // Apply search filter if there's a query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter((item: any) => {
      return (
        (item.account && item.account.toLowerCase().includes(query)) ||
        (item.department && item.department.toLowerCase().includes(query)) ||
        (item.description && item.description.toLowerCase().includes(query))
      );
    });
  }
  
  // Apply sorting
  return [...result].sort((a: any, b: any) => {
    let aValue = a[sortBy.value] || '';
    let bValue = b[sortBy.value] || '';
    
    // Handle numeric sorting
    if (sortBy.value === 'amount') {
      aValue = parseFloat(aValue) || 0;
      bValue = parseFloat(bValue) || 0;
    } else {
      // String comparisons
      aValue = String(aValue).toLowerCase();
      bValue = String(bValue).toLowerCase();
    }
    
    if (sortDirection.value === 'asc') {
      return aValue > bValue ? 1 : -1;
    } else {
      return aValue < bValue ? 1 : -1;
    }
  });
});

// Handle sorting when column headers are clicked
const toggleSort = (column: string) => {
  if (sortBy.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = column;
    sortDirection.value = 'desc';
  }
};

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2
  }).format(amount);
};

// Calculate totals
const totalExpenses = computed(() => {
  return props.data.reduce((sum: number, item: any) => sum + (parseFloat(item.amount) || 0), 0);
});
</script>

<template>
  <Head title="Finance Expense Details" />

  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Expense Details</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Summary Card -->
            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
              <h3 class="text-lg font-medium text-blue-800 mb-2">Expense Summary</h3>
              <p class="text-blue-700">
                Total Expenses: <span class="font-bold">{{ formatCurrency(totalExpenses) }}</span>
              </p>
              <p class="text-blue-700">
                Number of Expense Records: <span class="font-bold">{{ props.data.length }}</span>
              </p>
            </div>
            
            <!-- Search and Filter -->
            <div class="mb-6">
              <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="w-full md:w-1/3 mb-4 md:mb-0">
                  <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search expenses..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                  />
                </div>
                <div>
                  <Link :href="route('finance.dashboard')" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back to Dashboard
                  </Link>
                </div>
              </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full bg-white border border-gray-200">
                <thead>
                  <tr>
                    <th @click="toggleSort('department')" class="px-4 py-2 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer">
                      Department
                      <span v-if="sortBy === 'department'" class="ml-1">
                        {{ sortDirection === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                    <th @click="toggleSort('account')" class="px-4 py-2 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer">
                      Account
                      <span v-if="sortBy === 'account'" class="ml-1">
                        {{ sortDirection === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                    <th @click="toggleSort('description')" class="px-4 py-2 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer">
                      Description
                      <span v-if="sortBy === 'description'" class="ml-1">
                        {{ sortDirection === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                    <th @click="toggleSort('amount')" class="px-4 py-2 border-b border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer">
                      Amount
                      <span v-if="sortBy === 'amount'" class="ml-1">
                        {{ sortDirection === 'asc' ? '↑' : '↓' }}
                      </span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in filteredData" :key="index" class="hover:bg-gray-50">
                    <td class="px-4 py-2 border-b border-gray-200">{{ item.department || 'N/A' }}</td>
                    <td class="px-4 py-2 border-b border-gray-200">{{ item.account || 'N/A' }}</td>
                    <td class="px-4 py-2 border-b border-gray-200">{{ item.description || 'N/A' }}</td>
                    <td class="px-4 py-2 border-b border-gray-200 text-right">{{ formatCurrency(parseFloat(item.amount) || 0) }}</td>
                  </tr>
                  <tr v-if="filteredData.length === 0">
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                      No expense data found. Try adjusting your search criteria.
                    </td>
                  </tr>
                </tbody>
                <tfoot v-if="filteredData.length > 0">
                  <tr class="bg-gray-50">
                    <td colspan="3" class="px-4 py-2 border-t border-gray-300 font-semibold">Total</td>
                    <td class="px-4 py-2 border-t border-gray-300 text-right font-semibold">
                      {{ formatCurrency(filteredData.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0)) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 