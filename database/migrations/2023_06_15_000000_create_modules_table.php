<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name');
            $table->string('module_key')->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(false);
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            // Add indexes
            $table->index('status');
            $table->index('module_key');
        });
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
}
