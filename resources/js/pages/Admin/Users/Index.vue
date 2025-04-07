<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue'; // Assuming you have an AdminLayout
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/components/ui/toast/use-toast';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger, // We can bind the button directly if preferred, but using ref works too
} from '@/components/ui/dialog';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area'; // For potentially long role lists
import type { PageProps } from '@inertiajs/core'; // Import PageProps for correct typing

// Define interfaces for cleaner type hinting
interface KeycloakRole {
  id: string;
  name: string;
  description?: string;
  composite?: boolean; // Add other properties if returned by your API
  clientRole?: boolean;
  containerId?: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  provider_id: string; // Keycloak User ID
  keycloak_roles: KeycloakRole[];
  fetch_error?: string;
}

// Extend PageProps for Inertia compatibility and add specific props
interface ComponentProps extends PageProps {
  users: User[];
  availableRoles: KeycloakRole[];
  error?: string; // Error passed from controller during initial load
  // Add index signature for compatibility with PageProps
  [key: string]: unknown;
}

const props = defineProps<ComponentProps>();
const page = usePage<ComponentProps>(); // Use the correctly typed props

const { toast } = useToast();

// State for managing the role editing modal/dialog
const editingUser = ref<User | null>(null);
const isModalOpen = ref(false);
const selectedRoles = ref<string[]>([]); // Store names of selected roles for the editing user
const isLoading = ref(false); // For managing loading state during role updates

// Computed property to get available role names for easier handling
const availableRoleNames = computed(() => props.availableRoles.map(role => role.name));

// Function to open the modal and set the current user's roles
const openEditModal = (user: User) => {
  editingUser.value = user;
  // Ensure roles are properly mapped even if keycloak_roles is initially null/undefined
  selectedRoles.value = user.keycloak_roles?.map(role => role.name) ?? [];
  isModalOpen.value = true;
};

// Function to handle the role update via API call
const handleUpdateRoles = async () => {
  if (!editingUser.value) return;

  isLoading.value = true;
  const targetUser = editingUser.value; // Keep a reference
  const rolesPayload = { roles: selectedRoles.value };

  router.put(route('admin.users.updateRoles', { user: targetUser.id }), rolesPayload, {
    preserveScroll: true,
    // preserveState: true, // Not needed as much since we manually update state
    onStart: () => {
        console.log('Starting role update...');
    },
    onSuccess: () => { // The JSON response doesn't update Inertia page props directly
        toast({
            title: 'Success',
            description: `Roles updated successfully for ${targetUser.name}.`,
        });

        // Manually update the user's roles in the local state for immediate feedback
        const userIndex = props.users.findIndex(u => u.id === targetUser.id);
        if (userIndex !== -1) {
            // Filter the availableRoles to get the full objects for the selected names
            props.users[userIndex].keycloak_roles = props.availableRoles.filter(role =>
                selectedRoles.value.includes(role.name)
            );
        } else {
            console.error('Failed to find user in local state after update.');
            // Optionally trigger a full page reload if state becomes inconsistent
            // router.reload({ only: ['users'] });
        }
        isModalOpen.value = false; // Close modal on success
    },
    onError: (errors) => {
        console.error('Role update failed:', errors);
        // Attempt to parse Laravel validation errors or show generic message
        const errorMsg = Object.values(errors).flat().join(' ') || 'An unknown error occurred.';
        toast({
            title: 'Error Updating Roles',
            description: errorMsg,
            variant: 'destructive',
        });
    },
    onFinish: () => {
        isLoading.value = false; // Ensure loading state is turned off
    },
  });
};

// Display error toast if initial load failed
if (props.error) {
    toast({
        title: 'Error Loading Page',
        description: props.error,
        variant: 'destructive',
    });
}

</script>

<template>
  <Head title="User Role Management" />

  <AdminLayout>
    <div class="space-y-6 p-4 md:p-6">
      <h1 class="text-2xl font-semibold">User Role Management</h1>

      <Card>
        <CardHeader>
          <CardTitle>Users</CardTitle>
          <CardDescription>
            Manage Keycloak realm roles for registered users.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <Table v-if="!props.error && props.users.length > 0">
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Keycloak Roles</TableHead>
                <TableHead class="text-right">Actions</TableHead> 
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="user in props.users" :key="user.id">
                <TableCell class="font-medium">{{ user.name }}</TableCell>
                <TableCell>{{ user.email }}</TableCell>
                <TableCell>
                  <div v-if="user.fetch_error" class="text-destructive text-sm">
                    {{ user.fetch_error }}
                  </div>
                  <div v-else class="flex flex-wrap gap-1">
                    <Badge
                      v-for="role in user.keycloak_roles"
                      :key="role.id"
                      variant="secondary"
                      class="whitespace-nowrap"
                    >
                      {{ role.name }}
                    </Badge>
                    <span v-if="!user.keycloak_roles || user.keycloak_roles.length === 0" class="text-muted-foreground text-sm">
                      No roles assigned
                    </span>
                  </div>
                </TableCell>
                <TableCell class="text-right">
                  <Button
                    @click="openEditModal(user)"
                    size="sm"
                    variant="outline"
                    :disabled="!!user.fetch_error" 
                  >
                    Manage Roles
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
          <div v-else-if="!props.error && props.users.length === 0" class="text-center text-muted-foreground py-6">
            No users linked to Keycloak found.
          </div>
           <div v-else class="text-center text-destructive py-6">
            {{ props.error || 'Failed to load user data.' }}
          </div>
        </CardContent>
      </Card>
    </div>

    <!-- Role Editing Dialog -->
    <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
      <DialogContent class="sm:max-w-[425px]" @interact-outside="(e) => { if (isLoading) e.preventDefault(); }"> 
        <DialogHeader>
          <DialogTitle>Edit Roles for {{ editingUser?.name }}</DialogTitle>
          <DialogDescription>
            Select the Keycloak realm roles to assign to this user.
          </DialogDescription>
        </DialogHeader>
        <div class="py-4">
          <ScrollArea class="h-72 w-full rounded-md border p-4">
             <div v-if="props.availableRoles.length > 0" class="space-y-2">
                <div
                    v-for="role in props.availableRoles" 
                    :key="role.id"
                    class="flex items-center space-x-2"
                >
                    <Checkbox 
                        :id="`role-${role.id}`" 
                        :value="role.name" 
                        :checked="selectedRoles.includes(role.name)"
                        @update:checked="(checked) => {
                            if (checked) {
                                selectedRoles.push(role.name);
                            } else {
                                selectedRoles = selectedRoles.filter(r => r !== role.name);
                            }
                        }"
                        :disabled="isLoading"
                    />
                    <Label :for="`role-${role.id}`" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                        {{ role.name }}
                    </Label>
                </div>
            </div>
            <div v-else class="text-sm text-muted-foreground text-center">
                No available roles found.
            </div>
          </ScrollArea>
        </div>
        <DialogFooter>
          <Button type="button" variant="outline" @click="isModalOpen = false" :disabled="isLoading">
            Cancel
          </Button>
          <Button type="button" @click="handleUpdateRoles" :disabled="isLoading">
            {{ isLoading ? 'Saving...' : 'Save Changes' }}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

  </AdminLayout>
</template> 