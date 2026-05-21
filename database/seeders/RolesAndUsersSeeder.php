<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = \App\Models\Role::create(['name' => 'Owner', 'slug' => 'owner', 'description' => 'Full access']);
        $manager = \App\Models\Role::create(['name' => 'Manager', 'slug' => 'manager', 'description' => 'Can approve transactions']);
        $staff = \App\Models\Role::create(['name' => 'Staff', 'slug' => 'staff', 'description' => 'Can create transactions']);

        \App\Models\User::create([
            'name' => 'System Owner',
            'email' => 'owner@accounthub.test',
            'password' => bcrypt('password'),
            'role_id' => $owner->id,
            'is_active' => true,
        ]);
    }
}
