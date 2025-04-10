<?php

use Illuminate\Support\Facades\Schema;
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
        $modules = [
            [
                'module_key' => 'student_portal',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'parent_portal',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'online_learning',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'employees_management',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'sms_email',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'id_card',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'online_admission',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'online_documents',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'notice_board',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'accounting',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'student_billing',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'payroll',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'hostel',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'library',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'academic_calendar',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'bulk_communication',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'advanced_reporting',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'website_management',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'photo_gallery',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'event_management',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_key' => 'analytics',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('modules')->insert($modules);
    }
}
