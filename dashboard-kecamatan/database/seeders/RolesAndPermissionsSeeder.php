<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions (Capabilities)
        $permissions = [
            'submission.create',
            'submission.edit',
            'submission.delete',
            'submission.submit',
            'submission.verify',
            'submission.approve',
            'submission.reject',
            'dashboard.view_desa',
            'dashboard.view_kecamatan',
            'dashboard.view_kabupaten',
            'file.view',
            'file.download',
            'admin.manage_users',
            'admin.settings',
            'pemerintahan.manage_desa',
            'musrenbang.create',
            'pemerintahan.manage_visitors',
            'submission.review',
            'submission.return',
            'submission.recommend',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Define Roles and Assign Permissions
        // A. Operator Desa
        $roleOperator = Role::firstOrCreate(['name' => 'operator_desa']);
        $roleOperator->givePermissionTo([
            'submission.create',
            'submission.edit',
            'submission.delete', // only draft
            'submission.submit',
            'dashboard.view_desa',
            'file.view',
            'file.download',
            'pemerintahan.manage_desa',
            'musrenbang.create',
        ]);

        // B. Kepala Desa
        $roleKades = Role::firstOrCreate(['name' => 'kepala_desa']);
        $roleKades->givePermissionTo([
            'dashboard.view_desa',
            'file.view',
            'file.download'
        ]);

        // C. Verifikator Kecamatan (Kasi)
        $roleVerifikator = Role::firstOrCreate(['name' => 'verifikator_kecamatan']);
        $roleVerifikator->givePermissionTo([
            'submission.verify',
            'submission.reject',
            'submission.review',
            'submission.return',
            'submission.recommend',
            'dashboard.view_kecamatan',
            'file.view',
            'file.download'
        ]);

        // D. Admin Kecamatan (Camat)
        $roleCamat = Role::firstOrCreate(['name' => 'admin_kecamatan']);
        $roleCamat->givePermissionTo([
            'submission.verify',
            'submission.approve',
            'submission.reject',
            'dashboard.view_kecamatan',
            'file.view',
            'file.download'
        ]);

        // E. Super Admin (Kabupaten)
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin_kabupaten']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // F. Lintas Sektor
        $roleLintasSektor = Role::firstOrCreate(['name' => 'lintas_sektor']);
        $roleLintasSektor->givePermissionTo([
            'dashboard.view_kecamatan', // or specific view
            'file.view'
        ]);

        // 3. Assign Roles to Users based on Legacy Enum
        // This is the "Strangler" bridge.
        $users = User::all();

        foreach ($users as $user) {
            // Mapping Logic
            $roleName = null;

            switch ($user->role) {
                case 'superadmin':
                    $roleName = 'super_admin_kabupaten';
                    break;
                case 'camat':
                    $roleName = 'admin_kecamatan';
                    break;
                case 'kasi_pem':
                    $roleName = 'verifikator_kecamatan';
                    $user->givePermissionTo('pemerintahan.manage_visitors');
                    break;
                case 'kasi_ekbang':
                case 'kasi_kesra':
                case 'kasi_trantibum':
                    $roleName = 'verifikator_kecamatan';
                    if ($user->role === 'kasi_kesra') {
                        $user->givePermissionTo(['submission.review', 'submission.return', 'submission.recommend']);
                    }
                    if ($user->role === 'kasi_ekbang') {
                        $user->givePermissionTo(['submission.review', 'submission.return', 'submission.recommend']);
                    }
                    break;
                case 'operator_kecamatan':
                    $roleName = 'verifikator_kecamatan'; // or a new role
                    $user->givePermissionTo('pemerintahan.manage_visitors');
                    break;
                case 'kades':
                    $roleName = 'kepala_desa';
                    break;
                case 'operator_desa':
                    $roleName = 'operator_desa';
                    break;
                case 'lintas_sektor':
                    $roleName = 'lintas_sektor';
                    break;
            }

            if ($roleName) {
                // Assign role if not already assigned
                if (!$user->hasRole($roleName)) {
                    $user->assignRole($roleName);
                    $this->command->info("Assigned role {$roleName} to user {$user->email}");
                }
            }
        }
    }
}
