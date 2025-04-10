<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Helpers\AppHelper;

/**
 * Migration for creating the transportation module tables
 * 
 * This migration creates all the necessary tables for the transportation module.
 */
class CreateTransportationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create vehicles table
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('registration_no', 50)->unique();
            $table->string('type', 20)->default('bus');
            $table->integer('capacity')->nullable();
            $table->unsignedInteger('driver_id')->nullable();
            $table->string('contact_no', 20)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('driver_id')->references('id')->on('employees')->onDelete('set null');
        });

        // Create transport_routes table
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedInteger('vehicle_id');
            $table->string('start_place', 100);
            $table->time('start_time');
            $table->string('end_place', 100);
            $table->time('end_time');
            $table->decimal('distance', 10, 2)->nullable();
            $table->decimal('fare', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });

        // Create transport_stops table
        Schema::create('transport_stops', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('route_id');
            $table->string('name', 100);
            $table->time('stop_time');
            $table->integer('stop_order');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('route_id')->references('id')->on('transport_routes')->onDelete('cascade');
        });

        // Create transport_students table
        Schema::create('transport_students', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('stop_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('transport_routes')->onDelete('cascade');
            $table->foreign('stop_id')->references('id')->on('transport_stops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transport_students');
        Schema::dropIfExists('transport_stops');
        Schema::dropIfExists('transport_routes');
        Schema::dropIfExists('vehicles');
    }
}
