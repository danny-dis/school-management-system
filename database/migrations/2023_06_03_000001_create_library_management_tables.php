<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Helpers\AppHelper;

/**
 * Migration for creating the library management module tables
 * 
 * This migration creates all the necessary tables for the library management module.
 */
class CreateLibraryManagementTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create book_categories table
        Schema::create('book_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create books table
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('isbn', 30)->nullable();
            $table->string('author', 100)->nullable();
            $table->string('publisher', 100)->nullable();
            $table->string('edition', 50)->nullable();
            $table->unsignedInteger('category_id');
            $table->text('description')->nullable();
            $table->string('publish_year', 4)->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('available')->default(1);
            $table->string('rack_no', 20)->nullable();
            $table->string('image')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('book_categories')->onDelete('cascade');
        });

        // Create book_issues table
        Schema::create('book_issues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('book_id');
            $table->unsignedInteger('student_id');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->decimal('fine_amount', 10, 2)->default(0.00);
            $table->decimal('fine_paid', 10, 2)->default(0.00);
            $table->string('status', 20)->default('issued');
            $table->text('notes')->nullable();
            $table->unsignedInteger('issued_by');
            $table->unsignedInteger('returned_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');
        });

        // Create library_settings table
        Schema::create('library_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('max_books_per_student')->default(2);
            $table->integer('max_days_per_issue')->default(14);
            $table->decimal('fine_per_day', 10, 2)->default(1.00);
            $table->boolean('allow_renewal')->default(true);
            $table->integer('max_renewals')->default(1);
            $table->timestamps();
        });

        // Insert default library settings
        DB::table('library_settings')->insert([
            'max_books_per_student' => 2,
            'max_days_per_issue' => 14,
            'fine_per_day' => 1.00,
            'allow_renewal' => true,
            'max_renewals' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_issues');
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_categories');
        Schema::dropIfExists('library_settings');
    }
}
