<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create base roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full access to all system features',
                'is_system_role' => true
            ]
        );
        
        $developerRole = Role::firstOrCreate(
            ['name' => 'developer'],
            [
                'display_name' => 'Developer',
                'description' => 'GodMode On',
                'is_system_role' => true
            ]
        );
        
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Departmental manager with elevated privileges',
                'is_system_role' => true
            ]
        );
        
        $staffRole = Role::firstOrCreate(
            ['name' => 'staff'],
            [
                'display_name' => 'Staff',
                'description' => 'Regular staff member',
                'is_system_role' => true
            ]
        );
        
        // Create permissions by module
        
        // User management permissions
        $permissions = [
            // User management
            [
                'name' => 'view-users',
                'display_name' => 'View Users',
                'description' => 'Can view user list',
                'module' => 'users'
            ],
            [
                'name' => 'create-users',
                'display_name' => 'Create Users',
                'description' => 'Can create new users',
                'module' => 'users'
            ],
            [
                'name' => 'edit-users',
                'display_name' => 'Edit Users',
                'description' => 'Can edit existing users',
                'module' => 'users'
            ],
            [
                'name' => 'delete-users',
                'display_name' => 'Delete Users',
                'description' => 'Can delete users',
                'module' => 'users'
            ],
            [
                'name' => 'manage-users',
                'display_name' => 'Manage Users',
                'description' => 'Full user management',
                'module' => 'users'
            ],
            
            // Role & permission management
            [
                'name' => 'view-roles',
                'display_name' => 'View Roles',
                'description' => 'Can view role list',
                'module' => 'roles'
            ],
            [
                'name' => 'manage-roles',
                'display_name' => 'Manage Roles',
                'description' => 'Can manage roles and permissions',
                'module' => 'roles'
            ],
            
            // Settings
            [
                'name' => 'view-settings',
                'display_name' => 'View Settings',
                'description' => 'Can view system settings',
                'module' => 'settings'
            ],
            [
                'name' => 'manage-settings',
                'display_name' => 'Manage Settings',
                'description' => 'Can manage system settings',
                'module' => 'settings'
            ],
            
            // Reports
            [
                'name' => 'view-reports',
                'display_name' => 'View Reports',
                'description' => 'Can view reports',
                'module' => 'reports'
            ],
            [
                'name' => 'create-reports',
                'display_name' => 'Create Reports',
                'description' => 'Can create new reports',
                'module' => 'reports'
            ],
            
            // Budget permissions
            [
                'name' => 'access-budget',
                'display_name' => 'Access Budget',
                'description' => 'Can access budget section',
                'module' => 'budget'
            ],
            [
                'name' => 'budget-view',
                'display_name' => 'View Budget',
                'description' => 'Can view budget details',
                'module' => 'budget'
            ],
            [
                'name' => 'budget-edit',
                'display_name' => 'Edit Budget',
                'description' => 'Can edit budget items',
                'module' => 'budget'
            ],
            [
                'name' => 'budget-create',
                'display_name' => 'Create Budget',
                'description' => 'Can create new budget items',
                'module' => 'budget'
            ],
            [
                'name' => 'budget-export',
                'display_name' => 'Export Budget',
                'description' => 'Can export budget data',
                'module' => 'budget'
            ],
            
            // Business License permissions
            [
                'name' => 'access-business-license',
                'display_name' => 'Access Business License',
                'description' => 'Can access business license section',
                'module' => 'business-license'
            ],
            [
                'name' => 'license-view',
                'display_name' => 'View License',
                'description' => 'Can view business license details',
                'module' => 'business-license'
            ],
            [
                'name' => 'license-edit',
                'display_name' => 'Edit License',
                'description' => 'Can edit business licenses',
                'module' => 'business-license'
            ],
            [
                'name' => 'license-create',
                'display_name' => 'Create License',
                'description' => 'Can create new business licenses',
                'module' => 'business-license'
            ],
            [
                'name' => 'license-search',
                'display_name' => 'Search License',
                'description' => 'Can search business licenses',
                'module' => 'business-license'
            ],
            [
                'name' => 'license-approve',
                'display_name' => 'Approve License',
                'description' => 'Can approve business licenses',
                'module' => 'business-license'
            ],
        ];
        
        // Create all permissions
        foreach ($permissions as $permData) {
            Permission::firstOrCreate(
                ['name' => $permData['name']],
                [
                    'display_name' => $permData['display_name'],
                    'description' => $permData['description'],
                    'module' => $permData['module']
                ]
            );
        }
        
        // Assign permissions to roles
        
        // Admin gets all permissions
        $allPermissions = Permission::all();
        foreach ($allPermissions as $permission) {
            $adminRole->permissions()->syncWithoutDetaching($permission->id);
        }
        
        // Developer gets all permissions
        foreach ($allPermissions as $permission) {
            $developerRole->permissions()->syncWithoutDetaching($permission->id);
        }
        
        // Manager gets most permissions except top-level management
        $managerPermissions = Permission::whereNotIn('name', ['manage-roles', 'manage-settings', 'delete-users'])->get();
        foreach ($managerPermissions as $permission) {
            $managerRole->permissions()->syncWithoutDetaching($permission->id);
        }
        
        // Staff gets basic view permissions
        $staffPermissions = Permission::whereIn('name', ['view-users', 'view-reports', 'view-settings'])->get();
        foreach ($staffPermissions as $permission) {
            $staffRole->permissions()->syncWithoutDetaching($permission->id);
        }
    }
}
