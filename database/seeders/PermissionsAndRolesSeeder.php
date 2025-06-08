<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //permissions only for Admin
        $permissionsAdmin = [
            'settings.view',
            'invoices.delete',
            'invoices.create',
            'invoices.forceDelete',
            'buyers.view',
            'buyers.create',
            'buyers.update',
            'buyers.delete',
            'buyers.forceDelete',
            'buyers.restore',
            'costs.create',
            'costs.update',
            'costs.delete',
            'costs.forceDelete',
            'costs.restore',
            'costCategories.forceDelete',
            'costCategories.create',
            'costCategories.update',
            'costCategories.delete',
            'costCategories.restore',
            'workLogs.view',
            'workLog.view',
            'workLogs.create',
            'workLogs.update',
            'workLogs.delete',
            'workLogs.restore',
            'workLogs.forceDelete',
        ];
        //the rest of the permissions
        $commonPermissions = [
            'invoices.view',
            'invoices.edit',
            'invoice.view',
            'invoices.restore',
            'buyer.view',
            'costs.view',
            'cost.view',
            'costCategories.view',
            'costCategory.view',
            'currencies.forceDelete',
            'currencies.create',
            'currencies.update',
            'currencies.delete',
            'currencies.restore',
            'currencies.view',
            'currency.view',
            'paymentMethod.view',
            'paymentMethods.view',
            'paymentMethods.create',
            'paymentMethods.update',
            'paymentMethods.delete',
            'paymentMethods.restore',
            'paymentMethods.forceDelete',
            'transfers.view',
            'transfer.view',
            'transfers.create',
            'transfers.update',
            'transfers.delete',
            'transfers.restore',
            'transfers.forceDelete',
        ];

        $allPermissions = array_merge($permissionsAdmin, $commonPermissions);
        foreach ($allPermissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission,
                'guard_name' => 'filament',
            ]);
        }

        // Assign permissions to role
        $role = Role::where('name', 'super-admin')->first();

        if ($role) {
            foreach ($allPermissions as $permission) {
                $role->givePermissionTo($permission);
            }
        }
        //assign permissions to accountant
        $accountantRole = Role::firstOrCreate([
            'name' => 'accountant',
            'guard_name' => 'filament',
        ]);

        if ($accountantRole) {
            foreach ($commonPermissions as $permission) {
                $accountantRole->givePermissionTo($permission);
            }
        }


    }
}
