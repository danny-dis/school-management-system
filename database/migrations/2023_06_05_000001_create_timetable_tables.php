<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Helpers\AppHelper;

/**
 * Migration for creating the timetable and scheduling module tables
 * 
 * This migration creates all the necessary tables for the timetable and scheduling module.
 */
class CreateTimetableTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create rooms table
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('room_no', 20);
            $table->integer('capacity')->nullable();
            $table->string('type', 20)->default('classroom');
            $table->text('description')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create timetables table
        Schema::create('timetables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('academic_year_id');
            $table->text('description')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->unsignedInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('class_id')->references('id')->on('i_classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Create timetable_slots table
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('timetable_id');
            $table->tinyInteger('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('room_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
        });

        // Add new fields to events table
        Schema::table('events', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
            $table->time('start_time')->nullable()->after('end_date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('location')->nullable()->after('end_time');
            $table->boolean('for_students')->default(true)->after('location');
            $table->boolean('for_teachers')->default(true)->after('for_students');
            $table->boolean('for_parents')->default(true)->after('for_teachers');
            $table->boolean('for_admins')->default(true)->after('for_parents');
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE)->after('for_admins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove added fields from events table
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'end_date',
                'start_time',
                'end_time',
                'location',
                'for_students',
                'for_teachers',
                'for_parents',
                'for_admins',
                'status'
            ]);
        });

        Schema::dropIfExists('timetable_slots');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('rooms');
    }
}
