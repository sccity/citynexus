<template>
    <Head title="GovTxt Configuration" />
    
    <AppLayout>
        <div class="flex h-full flex-1 flex-col space-y-4 p-8">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Link href="/dashboard">Dashboard</Link>
                    <span>/</span>
                    <Link href="/govtxt">GovTxt</Link>
                    <span>/</span>
                    <span class="text-foreground">Configuration</span>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight">GovTxt Configuration</h2>
                        <p class="text-muted-foreground">
                            Manage your automated text message responses.
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Button @click="openModal()">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Response
                        </Button>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex flex-1 items-center space-x-2">
                        <Input
                            v-model="searchQuery"
                            placeholder="Search responses..."
                            class="h-8 w-[150px] lg:w-[250px]"
                        >
                            <template #prefix>
                                <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
                            </template>
                        </Input>
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" class="ml-auto h-8">
                                    <Filter class="mr-2 h-4 w-4" />
                                    View
                                    <ChevronDown class="ml-2 h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-[150px]">
                                <DropdownMenuCheckboxItem
                                    v-model="statusFilter"
                                    value="all"
                                >
                                    All
                                </DropdownMenuCheckboxItem>
                                <DropdownMenuCheckboxItem
                                    v-model="statusFilter"
                                    value="active"
                                >
                                    Active Only
                                </DropdownMenuCheckboxItem>
                                <DropdownMenuCheckboxItem
                                    v-model="statusFilter"
                                    value="inactive"
                                >
                                    Inactive Only
                                </DropdownMenuCheckboxItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>

                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-[200px]">
                                    <div class="flex items-center space-x-2">
                                        <button class="flex items-center" @click="toggleSort('name')">
                                            Name
                                            <ArrowUpDown class="ml-2 h-4 w-4" />
                                        </button>
                                    </div>
                                </TableHead>
                                <TableHead class="w-[250px]">Terms</TableHead>
                                <TableHead>Response</TableHead>
                                <TableHead class="w-[100px]">
                                    <div class="flex items-center space-x-2">
                                        <button class="flex items-center" @click="toggleSort('active')">
                                            Status
                                            <ArrowUpDown class="ml-2 h-4 w-4" />
                                        </button>
                                    </div>
                                </TableHead>
                                <TableHead class="w-[100px] text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in paginatedResponses" :key="item.id" class="hover:bg-muted/50">
                                <TableCell class="font-medium">{{ item.name }}</TableCell>
                                <TableCell>{{ item.terms }}</TableCell>
                                <TableCell class="max-w-[400px] truncate">{{ item.response }}</TableCell>
                                <TableCell>
                                    <Badge :variant="item.active ? 'default' : 'secondary'" class="capitalize">
                                        {{ item.active ? 'Active' : 'Inactive' }}
                                    </Badge>
                                </TableCell>
                                <TableCell class="text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <Button variant="ghost" size="icon" class="h-8 w-8 p-0">
                                                <span class="sr-only">Open menu</span>
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem @click="openModal(item)">
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem @click="deleteAutoResponse(item)" class="text-destructive">
                                                <Trash2 class="mr-2 h-4 w-4" />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </TableCell>
                            </TableRow>
                            <TableEmpty v-if="paginatedResponses.length === 0" colspan="5">
                                No responses found.
                            </TableEmpty>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex items-center justify-between px-2">
                    <div class="flex-1 text-sm text-muted-foreground">
                        Page {{ currentPage }} of {{ totalPages }}
                        ({{ paginatedResponses.length }} total responses)
                    </div>
                    <div class="flex items-center space-x-6 lg:space-x-8">
                        <div class="flex items-center space-x-2">
                            <p class="text-sm font-medium">Rows per page</p>
                            <Select
                                v-model="itemsPerPage"
                                :options="[
                                    { value: 5, label: '5' },
                                    { value: 10, label: '10' },
                                    { value: 20, label: '20' },
                                    { value: 50, label: '50' }
                                ]"
                                class="w-[70px]"
                            />
                        </div>
                        <div class="flex w-[100px] items-center justify-center text-sm font-medium">
                            Page {{ currentPage }} of {{ totalPages }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="icon"
                                @click="goToPage(1)"
                                :disabled="currentPage === 1"
                            >
                                <ChevronsLeft class="h-4 w-4" />
                                <span class="sr-only">First page</span>
                            </Button>
                            <Button
                                variant="outline"
                                size="icon"
                                @click="prevPage"
                                :disabled="currentPage === 1"
                            >
                                <ChevronLeft class="h-4 w-4" />
                                <span class="sr-only">Previous page</span>
                            </Button>
                            <Button
                                variant="outline"
                                size="icon"
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                            >
                                <ChevronRight class="h-4 w-4" />
                                <span class="sr-only">Next page</span>
                            </Button>
                            <Button
                                variant="outline"
                                size="icon"
                                @click="goToPage(totalPages)"
                                :disabled="currentPage === totalPages"
                            >
                                <ChevronsRight class="h-4 w-4" />
                                <span class="sr-only">Last page</span>
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog :open="isModalOpen" @update:open="closeModal">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Edit' : 'Add' }} Auto Response</DialogTitle>
                    <DialogDescription>
                        Configure an automatic response for specific terms.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitForm" class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="Enter a name for this auto response"
                            required
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="terms">Terms</Label>
                        <Input
                            id="terms"
                            v-model="form.terms"
                            placeholder="Enter trigger terms (comma separated)"
                            required
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="response">Response</Label>
                        <Textarea
                            id="response"
                            v-model="form.response"
                            placeholder="Enter the automatic response message"
                            required
                        />
                    </div>
                    <div class="flex items-center space-x-2">
                        <Switch
                            id="active"
                            v-model="form.active"
                        />
                        <Label for="active">Active</Label>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="secondary" @click="closeModal">Cancel</Button>
                        <Button type="submit">Save changes</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger, DropdownMenuCheckboxItem } from '@/components/ui/dropdown-menu';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Plus, Search, Filter, ChevronDown, ArrowUpDown, MoreHorizontal, Pencil, Trash2, ChevronsLeft, ChevronLeft, ChevronRight, ChevronsRight } from 'lucide-vue-next';

interface AutoResponse {
    id: number;
    name: string;
    terms: string;
    response: string;
    active: boolean;
}

const props = defineProps<{
    autoResponses: AutoResponse[];
}>();

// Search and filter state
const searchQuery = ref('');
const statusFilter = ref('all');
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('name');
const sortDirection = ref('asc');

// Modal state
const isModalOpen = ref(false);
const isEditing = ref(false);
const form = ref({
    id: null as number | null,
    name: '',
    terms: '',
    response: '',
    active: true,
});

// Computed properties
const filteredResponses = computed(() => {
    let filtered = [...props.autoResponses];
    
    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item => 
            item.name.toLowerCase().includes(query) ||
            item.terms.toLowerCase().includes(query) ||
            item.response.toLowerCase().includes(query)
        );
    }
    
    // Apply status filter
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(item => 
            statusFilter.value === 'active' ? item.active : !item.active
        );
    }
    
    return filtered;
});

const sortedResponses = computed(() => {
    return [...filteredResponses.value].sort((a, b) => {
        const modifier = sortDirection.value === 'asc' ? 1 : -1;
        if (sortField.value === 'name') {
            return a.name.localeCompare(b.name) * modifier;
        } else if (sortField.value === 'active') {
            return (a.active === b.active ? 0 : a.active ? -1 : 1) * modifier;
        }
        return 0;
    });
});

const paginatedResponses = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return sortedResponses.value.slice(start, end);
});

const totalPages = computed(() => 
    Math.ceil(sortedResponses.value.length / itemsPerPage.value)
);

// Methods
const toggleSort = (field: string) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
};

const openModal = (item?: AutoResponse) => {
    if (item) {
        form.value = { ...item };
        isEditing.value = true;
    } else {
        form.value = {
            id: null,
            name: '',
            terms: '',
            response: '',
            active: true,
        };
        isEditing.value = false;
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.value = {
        id: null,
        name: '',
        terms: '',
        response: '',
        active: true,
    };
};

const submitForm = () => {
    // TODO: Implement form submission
    closeModal();
};

const deleteAutoResponse = (item: AutoResponse) => {
    // TODO: Implement delete functionality
};

const goToPage = (page: number) => {
    currentPage.value = page;
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

// Watch for filter changes to reset pagination
watch([searchQuery, statusFilter], () => {
    currentPage.value = 1;
});
</script> 