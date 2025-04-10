<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Helpers\AppHelper;

/**
 * Migration for creating the communication module tables
 * 
 * This migration creates all the necessary tables for the communication module.
 */
class CreateCommunicationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('receiver_id');
            $table->string('subject', 255);
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->boolean('deleted_by_sender')->default(false);
            $table->boolean('deleted_by_receiver')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create notifications table
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title', 255);
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('read')->default(false);
            $table->string('type', 20)->default('info');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create announcements table
        Schema::create('announcements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->text('description');
            $table->unsignedInteger('created_by');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('for_students')->default(true);
            $table->boolean('for_teachers')->default(true);
            $table->boolean('for_parents')->default(true);
            $table->boolean('for_admins')->default(true);
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Create email_templates table
        Schema::create('email_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('subject', 255);
            $table->text('body');
            $table->text('variables')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
        });

        // Create sms_templates table
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->text('body');
            $table->text('variables')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
        });

        // Create sms_logs table
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('recipient', 20);
            $table->text('message');
            $table->string('status', 20);
            $table->text('response')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Create email_logs table
        Schema::create('email_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('recipient', 100);
            $table->string('subject', 255);
            $table->text('message');
            $table->string('status', 20);
            $table->text('response')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
    }
}
