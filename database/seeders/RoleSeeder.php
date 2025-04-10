<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $roles = [
            'Super Admin',
            'Admin',
            'Teacher',
            'Student',
            'Parent',
            'Accountant',
            'Librarian',
            'Receptionist',
        ];
        
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
        
        $this->command->info('Roles seeded successfully!');
    }
}
