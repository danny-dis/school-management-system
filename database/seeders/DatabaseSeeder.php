<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AppMeta;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Cleaning old data...');

        DB::statement("SET foreign_key_checks=0");

        User::truncate();
        DB::table('roles')->truncate();
        DB::table('model_has_roles')->truncate();
        Permission::truncate();
        AppMeta::truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('notifications')->truncate();

        DB::statement("SET foreign_key_checks=1");

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            ModuleSeeder::class,
            ModulePermissionSeeder::class,
        ]);
        
        $this->command->info('Database seeded successfully!');
    }
}
