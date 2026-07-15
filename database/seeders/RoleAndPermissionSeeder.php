<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ─── 1. Permissions ───────────────────────────────────────────
        $permissions = [
            'manage-kyc'      => 'Manage KYC submissions',
            'manage-loans'    => 'Approve or reject loan applications',
            'create-loans'    => 'Request a loan',
            'fund-loans'      => 'Invest in marketplace loans',
            'manage-users'    => 'Freeze or unfreeze accounts',
            'manage-settings' => 'Modify platform settings and fees',
        ];

        $createdPermissions = [];
        foreach ($permissions as $name => $desc) {
            $createdPermissions[$name] = Permission::updateOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // ─── 2. Roles ──────────────────────────────────────────────────
        $roles = [
            'admin',
            'borrower',
            'lender',
            'collection_officer',
            'customer_service',
        ];

        $createdRoles = [];
        foreach ($roles as $roleName) {
            $createdRoles[$roleName] = Role::updateOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );
        }

        // ─── 3. Assign Permissions ─────────────────────────────────────
        // Admin gets everything
        $createdRoles['admin']->permissions()->sync(
            array_column($createdPermissions, 'id')
        );

        // Borrower can request loans
        $createdRoles['borrower']->permissions()->sync([
            $createdPermissions['create-loans']->id,
        ]);

        // Lender can invest
        $createdRoles['lender']->permissions()->sync([
            $createdPermissions['fund-loans']->id,
        ]);

        // Collection Officer can view/manage loans
        $createdRoles['collection_officer']->permissions()->sync([
            $createdPermissions['manage-loans']->id,
        ]);

        // CS can manage KYC and view/manage loans
        $createdRoles['customer_service']->permissions()->sync([
            $createdPermissions['manage-kyc']->id,
            $createdPermissions['manage-loans']->id,
        ]);
    }

    /**
     * Run down operation (reverse seed)
     */
    public function down(): void
    {
        Permission::truncate();
        Role::truncate();
    }
}
