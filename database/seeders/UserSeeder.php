<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create super admin user
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@zophlic.com',
            'phone_no' => '1234567890',
            'password' => Hash::make('admin123'),
            'status' => true,
            'is_super_admin' => true,
        ]);
        
        // Assign role
        $superAdmin->assignRole('Super Admin');
        
        // Create demo users for each role
        $demoUsers = [
            [
                'name' => 'John Doe',
                'username' => 'teacher',
                'email' => 'teacher@zophlic.com',
                'phone_no' => '1234567891',
                'password' => Hash::make('teacher123'),
                'role' => 'Teacher',
            ],
            [
                'name' => 'Jane Smith',
                'username' => 'student',
                'email' => 'student@zophlic.com',
                'phone_no' => '1234567892',
                'password' => Hash::make('student123'),
                'role' => 'Student',
            ],
            [
                'name' => 'Robert Johnson',
                'username' => 'parent',
                'email' => 'parent@zophlic.com',
                'phone_no' => '1234567893',
                'password' => Hash::make('parent123'),
                'role' => 'Parent',
            ],
            [
                'name' => 'Emily Davis',
                'username' => 'accountant',
                'email' => 'accountant@zophlic.com',
                'phone_no' => '1234567894',
                'password' => Hash::make('accountant123'),
                'role' => 'Accountant',
            ],
            [
                'name' => 'Michael Wilson',
                'username' => 'librarian',
                'email' => 'librarian@zophlic.com',
                'phone_no' => '1234567895',
                'password' => Hash::make('librarian123'),
                'role' => 'Librarian',
            ],
        ];
        
        foreach ($demoUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::create($userData + ['status' => true]);
            $user->assignRole($role);
        }
        
        $this->command->info('Users seeded successfully!');
    }
}
