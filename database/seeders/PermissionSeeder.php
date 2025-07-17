<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'reply ticket',
            'update ticket',
            'create ticket',
            'view ticket',
            'delete ticket',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $admin = Role::firstOrCreate(['name' => 'admin']);

        $staff->syncPermissions(['reply ticket', 'update ticket', 'view ticket']);
        $admin->syncPermissions(Permission::all());

        // Assign role ke user tertentu jika perlu (opsional)
        // $user = \App\Models\User::find(1); // Ganti ID sesuai user-mu
        // $user->assignRole('admin');
    }
}
