<?php

use Illuminate\Database\Seeder;
use App\Permission;
use App\Role;

/**
 * ModulePermissionSeeder - Seeds the permissions for module management
 *
 * This seeder adds the necessary permissions for the module management
 * functionality to the database.
 *
 * @author Zophlic Development Team
 */
class ModulePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modulePermissions = [
            [
                "slug" => "modules.index",
                "name" => "Module Management View",
                "group" => "Administration Exclusive"
            ],
            [
                "slug" => "modules.enable",
                "name" => "Module Management Edit",
                "group" => "Administration Exclusive"
            ],
            [
                "slug" => "modules.disable",
                "name" => "Module Management Edit",
                "group" => "Administration Exclusive"
            ],
            // Student Portal permissions
            [
                "slug" => "student.portal.dashboard",
                "name" => "Student Portal Access",
                "group" => "Student Portal"
            ],
            // Parent Portal permissions
            [
                "slug" => "parent.portal.dashboard",
                "name" => "Parent Portal Access",
                "group" => "Parent Portal"
            ],
            // Online Learning permissions
            [
                "slug" => "online.learning.access",
                "name" => "Online Learning Access",
                "group" => "Online Learning"
            ],
            // Employees Management permissions
            [
                "slug" => "employees.work.outside",
                "name" => "Employees Work Outside",
                "group" => "Employees Management"
            ],
            // SMS and Email permissions
            [
                "slug" => "sms.gateway.setup",
                "name" => "SMS Gateway Setup",
                "group" => "SMS and Email"
            ],
            [
                "slug" => "email.sms.template",
                "name" => "Email & SMS Templating",
                "group" => "SMS and Email"
            ],
            // ID Card permissions
            [
                "slug" => "id.card.manage",
                "name" => "ID Card Manage",
                "group" => "ID Card Management"
            ],
            [
                "slug" => "id.card.print",
                "name" => "ID Card Print",
                "group" => "ID Card Management"
            ],
            [
                "slug" => "id.card.bulk.print",
                "name" => "ID Card Bulk Print",
                "group" => "ID Card Management"
            ],
            // Online Admission permissions
            [
                "slug" => "online.admission.manage",
                "name" => "Online Admission Manage",
                "group" => "Online Admission"
            ],
            // Online Documents permissions
            [
                "slug" => "online.admit.card",
                "name" => "Online Admit Card",
                "group" => "Online Documents"
            ],
            [
                "slug" => "online.payslip",
                "name" => "Online Payslip",
                "group" => "Online Documents"
            ],
            // Notice Board permissions
            [
                "slug" => "notice.board.manage",
                "name" => "Notice Board Manage",
                "group" => "Notice Board"
            ],
            // Accounting permissions
            [
                "slug" => "account.manage",
                "name" => "Account Manage",
                "group" => "Accounting"
            ],
            [
                "slug" => "budget.manage",
                "name" => "Budget Manage",
                "group" => "Accounting"
            ],
            [
                "slug" => "account.heads",
                "name" => "Account Heads",
                "group" => "Accounting"
            ],
            [
                "slug" => "income.expense.manage",
                "name" => "Income/Expense Manage",
                "group" => "Accounting"
            ],
            // Student Billing permissions
            [
                "slug" => "student.invoice",
                "name" => "Student Invoice",
                "group" => "Student Billing"
            ],
            // Payroll permissions
            [
                "slug" => "payroll.manage",
                "name" => "Payroll Manage",
                "group" => "Payroll"
            ],
            [
                "slug" => "salary.template",
                "name" => "Salary Template",
                "group" => "Payroll"
            ],
            [
                "slug" => "employee.salary.payment",
                "name" => "Employee Salary Payment",
                "group" => "Payroll"
            ],
            // Hostel permissions
            [
                "slug" => "hostel.manage",
                "name" => "Hostel Manage",
                "group" => "Hostel Management"
            ],
            [
                "slug" => "hostel.collection",
                "name" => "Hostel Collection",
                "group" => "Hostel Management"
            ],
            // Library permissions
            [
                "slug" => "library.manage",
                "name" => "Library Manage",
                "group" => "Library Management"
            ],
            [
                "slug" => "issue.book",
                "name" => "Issue Book",
                "group" => "Library Management"
            ],
            [
                "slug" => "fine.collection",
                "name" => "Fine Collection",
                "group" => "Library Management"
            ],
            // Academic Calendar permissions
            [
                "slug" => "academic.calendar.print",
                "name" => "Academic Calendar Print",
                "group" => "Academic Calendar"
            ],
            // Bulk Communication permissions
            [
                "slug" => "bulk.sms.email",
                "name" => "Bulk SMS and Email",
                "group" => "Bulk Communication"
            ],
            // Advanced Reporting permissions
            [
                "slug" => "advanced.reporting",
                "name" => "Advanced Reporting",
                "group" => "Advanced Reporting"
            ],
            // Website Management permissions
            [
                "slug" => "website.manage",
                "name" => "Website Manage",
                "group" => "Website Management"
            ],
            // Photo Gallery permissions
            [
                "slug" => "photo.gallery.manage",
                "name" => "Photo Gallery Manage",
                "group" => "Photo Gallery"
            ],
            // Event Management permissions
            [
                "slug" => "event.manage",
                "name" => "Event Manage",
                "group" => "Event Management"
            ],
            // Analytics permissions
            [
                "slug" => "analytics.manage",
                "name" => "Analytics Manage",
                "group" => "Analytics"
            ]
        ];

        echo PHP_EOL, 'seeding module permissions...';

        Permission::insert($modulePermissions);

        echo PHP_EOL, 'seeding module role permissions...', PHP_EOL;

        // Add permissions to admin role
        $admin = Role::where('name', 'admin')->first();
        $permissions = Permission::whereIn('slug', ['modules.index', 'modules.enable', 'modules.disable'])->get();
        $admin->permissions()->saveMany($permissions);

        echo 'Module permissions seeded successfully!', PHP_EOL;
    }
}
