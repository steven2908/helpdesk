<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'staff']);
        Role::firstOrCreate(['name' => 'user']);
        
        $company = Company::updateOrCreate(
            ['name' => 'PT. Jaringan Pintar Nusantara'],
            [] // Tidak perlu update kolom lain
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'company_id' => $company->id,
            ]
        );
         $admin->assignRole('admin');

        $staff = User::updateOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('staff123'),
                'company_id' => $company->id,
            ]
        );
            $staff->assignRole('staff');


        $user = User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User',
                'password' => Hash::make('user123'),
                'company_id' => $company->id,
            ]
        );
            $user->assignRole('user');

    }
}
