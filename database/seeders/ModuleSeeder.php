<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing modules
        DB::table('modules')->truncate();
        
        $modules = [];
        $now = now();
        
        // Get all available modules from config
        $availableModules = config('modules.available');
        $defaultEnabled = config('modules.default_enabled', []);
        
        foreach ($availableModules as $key => $module) {
            $modules[] = [
                'module_key' => $key,
                'status' => in_array($key, $defaultEnabled),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        
        // Add core modules that are always enabled
        $coreModules = [
            'student_portal' => true,
            'parent_portal' => true,
            'employees_management' => true,
            'notice_board' => true,
            'academic_calendar' => true,
            'website_management' => true,
        ];
        
        foreach ($coreModules as $key => $status) {
            // Only add if not already in the modules array
            if (!array_key_exists($key, $availableModules)) {
                $modules[] = [
                    'module_key' => $key,
                    'status' => $status,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        
        DB::table('modules')->insert($modules);
        
        $this->command->info('Modules seeded successfully!');
    }
}
