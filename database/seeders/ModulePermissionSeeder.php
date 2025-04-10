<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get roles
        $adminRole = Role::findByName('Admin');
        $teacherRole = Role::findByName('Teacher');
        $studentRole = Role::findByName('Student');
        $parentRole = Role::findByName('Parent');
        $accountantRole = Role::findByName('Accountant');
        $librarianRole = Role::findByName('Librarian');
        
        // Assign permissions to roles
        
        // Admin permissions
        $adminPermissions = Permission::all();
        $adminRole->syncPermissions($adminPermissions);
        
        // Teacher permissions
        $teacherPermissions = [
            'dashboard.view',
            'student.view',
            'class.view',
            'section.view',
            'subject.view',
            'exam.view',
            'mark.view',
            'mark.create',
            'mark.edit',
            'attendance.view',
            'attendance.create',
            'attendance.edit',
        ];
        $teacherRole->syncPermissions($teacherPermissions);
        
        // Student permissions
        $studentPermissions = [
            'dashboard.view',
        ];
        $studentRole->syncPermissions($studentPermissions);
        
        // Parent permissions
        $parentPermissions = [
            'dashboard.view',
        ];
        $parentRole->syncPermissions($parentPermissions);
        
        // Accountant permissions
        $accountantPermissions = [
            'dashboard.view',
            'student.view',
        ];
        $accountantRole->syncPermissions($accountantPermissions);
        
        // Librarian permissions
        $librarianPermissions = [
            'dashboard.view',
            'student.view',
        ];
        $librarianRole->syncPermissions($librarianPermissions);
        
        $this->command->info('Module permissions assigned successfully!');
    }
}
