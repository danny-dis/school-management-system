<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define permission groups
        $permissionGroups = [
            'dashboard' => [
                'dashboard.view',
            ],
            'user' => [
                'user.view',
                'user.create',
                'user.edit',
                'user.delete',
            ],
            'role' => [
                'role.view',
                'role.create',
                'role.edit',
                'role.delete',
            ],
            'student' => [
                'student.view',
                'student.create',
                'student.edit',
                'student.delete',
            ],
            'teacher' => [
                'teacher.view',
                'teacher.create',
                'teacher.edit',
                'teacher.delete',
            ],
            'class' => [
                'class.view',
                'class.create',
                'class.edit',
                'class.delete',
            ],
            'section' => [
                'section.view',
                'section.create',
                'section.edit',
                'section.delete',
            ],
            'subject' => [
                'subject.view',
                'subject.create',
                'subject.edit',
                'subject.delete',
            ],
            'exam' => [
                'exam.view',
                'exam.create',
                'exam.edit',
                'exam.delete',
            ],
            'mark' => [
                'mark.view',
                'mark.create',
                'mark.edit',
                'mark.delete',
            ],
            'attendance' => [
                'attendance.view',
                'attendance.create',
                'attendance.edit',
                'attendance.delete',
            ],
            'setting' => [
                'setting.view',
                'setting.edit',
            ],
            'module' => [
                'module.view',
                'module.edit',
            ],
        ];
        
        // Create permissions
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission, 'group' => $group]);
            }
        }
        
        $this->command->info('Permissions seeded successfully!');
    }
}
