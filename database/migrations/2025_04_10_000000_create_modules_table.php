<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Migration for creating the modules table
 *
 * This migration creates the modules table which stores information about
 * which modules are enabled or disabled in the system.
 *
 * @author Zophlic Development Team
 */
class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('module_key', 50)->unique();
            $table->boolean('status')->default(false);
            $table->timestamps();

            // Add indexes
            $table->index('module_key');
            $table->index('status');
        });

        // Insert default modules
        $this->seedDefaultModules();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }

    /**
     * Seed the default modules
     *
     * @return void
     */
    private function seedDefaultModules()
    {
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
    }
}
