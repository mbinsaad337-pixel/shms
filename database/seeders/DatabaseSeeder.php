<?php

namespace Database\Seeders;

use App\Models\User;
use App\Database\RoleAndPermissionSeeder;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run Role and Permission Seeder
        $this->call(RoleAndPermissionSeeder::class);

        // Create Super Admin User
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@shms.com',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $admin->assignRole('super-admin');
    }
}
