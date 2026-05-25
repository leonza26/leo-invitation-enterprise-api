<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Permissions
        $permissions = [
            [
                'name' => 'View Dashboard',
                'slug' => 'view-dashboard',
                'description' => 'Can access standard metrics dashboards.'
            ],
            [
                'name' => 'Create Invitations',
                'slug' => 'create-invitation',
                'description' => 'Can generate new digital invitations.'
            ],
            [
                'name' => 'Edit Invitations',
                'slug' => 'edit-invitation',
                'description' => 'Can modify invitation designs.'
            ],
            [
                'name' => 'Delete Invitations',
                'slug' => 'delete-invitation',
                'description' => 'Can delete active invitations.'
            ],
            [
                'name' => 'View Audit Logs',
                'slug' => 'view-audits',
                'description' => 'Can inspect system action history.'
            ],
            [
                'name' => 'Manage Billing',
                'slug' => 'manage-billing',
                'description' => 'Can configure subscription tiers and pricing.'
            ],
        ];

        $permissionModels = [];
        foreach ($permissions as $p) {
            $permissionModels[$p['slug']] = Permission::create($p);
        }

        // 2. Seed Roles
        $roles = [
            'super_admin' => [
                'name' => 'Super Administrator',
                'description' => 'God-mode administrator with access to all dashboards and controls.',
                'permissions' => ['view-dashboard', 'create-invitation', 'edit-invitation', 'delete-invitation', 'view-audits', 'manage-billing']
            ],
            'client' => [
                'name' => 'SaaS Client',
                'description' => 'Wedding couple or planner who designs and distributes digital templates.',
                'permissions' => ['view-dashboard', 'create-invitation', 'edit-invitation']
            ],
            'operator' => [
                'name' => 'Support Operator',
                'description' => 'Staff operator responsible for checking audit metrics and assisting clients.',
                'permissions' => ['view-dashboard', 'view-audits']
            ],
            'guest' => [
                'name' => 'Wedding Guest / General Public',
                'description' => 'Visitor who views templates and posts attendance wishes.',
                'permissions' => ['view-dashboard']
            ]
        ];

        $roleModels = [];
        foreach ($roles as $slug => $data) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description']
            ]);
            $roleModels[$slug] = $role;

            // Attach permissions
            foreach ($data['permissions'] as $pSlug) {
                if (isset($permissionModels[$pSlug])) {
                    $role->permissions()->attach($permissionModels[$pSlug]->id);
                }
            }
        }

        // 3. Seed Users
        // Admin
        User::create([
            'name' => 'Aurelia Sterling',
            'email' => 'aurelia@leo-wedding.com',
            'password' => Hash::make('password123'),
            'role_id' => $roleModels['super_admin']->id
        ]);

        // Client
        User::create([
            'name' => 'John Doe (Client)',
            'email' => 'client@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $roleModels['client']->id
        ]);

        // Operator
        User::create([
            'name' => 'Staff Operator',
            'email' => 'operator@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $roleModels['operator']->id
        ]);
    }
}
gi