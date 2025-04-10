<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Helpers\AppHelper;

/**
 * Migration for creating the online learning module tables
 * 
 * This migration creates all the necessary tables for the online learning module.
 */
class CreateOnlineLearningTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create courses table
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('teacher_id');
            $table->string('cover_image')->nullable();
            $table->text('syllabus')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('class_id')->references('id')->on('i_classes')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('employees')->onDelete('cascade');
        });

        // Create lessons table
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->unsignedInteger('course_id');
            $table->text('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('attachment')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->boolean('is_free')->default(false);
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::INACTIVE);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        // Create lesson_resources table
        Schema::create('lesson_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id');
            $table->string('title', 100);
            $table->string('type', 20);
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });

        // Create assignments table
        Schema::create('assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->unsignedInteger('course_id');
            $table->dateTime('due_date');
            $table->decimal('total_marks', 5, 2)->default(100.00);
            $table->string('attachment')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        // Create assignment_submissions table
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('assignment_id');
            $table->unsignedInteger('registration_id');
            $table->text('submission_text')->nullable();
            $table->string('attachment')->nullable();
            $table->dateTime('submitted_at');
            $table->decimal('marks', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->unsignedInteger('graded_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');

            // Unique constraint to prevent multiple submissions
            $table->unique(['assignment_id', 'registration_id']);
        });

        // Create lesson_progress table
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('registration_id');
            $table->string('status', 20)->default('not_started');
            $table->integer('progress_percentage')->default(0);
            $table->dateTime('last_accessed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');

            // Unique constraint
            $table->unique(['lesson_id', 'registration_id']);
        });

        // Create course_student pivot table
        Schema::create('course_student', function (Blueprint $table) {
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('registration_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('registration_id')->references('id')->on('registrations')->onDelete('cascade');

            // Primary key
            $table->primary(['course_id', 'registration_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_student');
        Schema::dropIfExists('lesson_progress');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('lesson_resources');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('courses');
    }
}
