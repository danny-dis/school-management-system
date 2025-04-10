<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Helpers\AppHelper;

/**
 * Migration for creating the health records module tables
 * 
 * This migration creates all the necessary tables for the health records module.
 */
class CreateHealthRecordsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create health_records table
        Schema::create('health_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->decimal('height', 5, 2)->nullable()->comment('in cm');
            $table->decimal('weight', 5, 2)->nullable()->comment('in kg');
            $table->decimal('bmi', 5, 2)->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->string('pulse_rate', 10)->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('past_medical_history')->nullable();
            $table->string('vision_left', 10)->nullable();
            $table->string('vision_right', 10)->nullable();
            $table->string('hearing_left', 10)->nullable();
            $table->string('hearing_right', 10)->nullable();
            $table->text('immunizations')->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('recorded_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Create medical_visits table
        Schema::create('medical_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('health_record_id');
            $table->date('visit_date');
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('prescribed_medications')->nullable();
            $table->decimal('temperature', 4, 1)->nullable()->comment('in Celsius');
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->unsignedInteger('attended_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('health_record_id')->references('id')->on('health_records')->onDelete('cascade');
            $table->foreign('attended_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Create vaccinations table
        Schema::create('vaccinations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('recommended_age', 50)->nullable();
            $table->integer('doses')->default(1);
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
        });

        // Create vaccination_records table
        Schema::create('vaccination_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('vaccination_id');
            $table->integer('dose_number')->default(1);
            $table->date('date_given');
            $table->date('next_due_date')->nullable();
            $table->string('administered_by', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('recorded_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('vaccination_id')->references('id')->on('vaccinations')->onDelete('cascade');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Insert default vaccinations
        DB::table('vaccinations')->insert([
            [
                'name' => 'BCG',
                'description' => 'Bacillus Calmette-Guérin vaccine for tuberculosis',
                'recommended_age' => 'At birth',
                'doses' => 1,
                'status' => AppHelper::ACTIVE,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Hepatitis B',
                'description' => 'Vaccine for Hepatitis B',
                'recommended_age' => 'Birth, 1-2 months, 6-18 months',
                'doses' => 3,
                'status' => AppHelper::ACTIVE,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'DTP',
                'description' => 'Diphtheria, Tetanus, Pertussis vaccine',
                'recommended_age' => '2 months, 4 months, 6 months, 15-18 months, 4-6 years',
                'doses' => 5,
                'status' => AppHelper::ACTIVE,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Polio',
                'description' => 'Polio vaccine',
                'recommended_age' => '2 months, 4 months, 6-18 months, 4-6 years',
                'doses' => 4,
                'status' => AppHelper::ACTIVE,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'MMR',
                'description' => 'Measles, Mumps, Rubella vaccine',
                'recommended_age' => '12-15 months, 4-6 years',
                'doses' => 2,
                'status' => AppHelper::ACTIVE,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaccination_records');
        Schema::dropIfExists('vaccinations');
        Schema::dropIfExists('medical_visits');
        Schema::dropIfExists('health_records');
    }
}
