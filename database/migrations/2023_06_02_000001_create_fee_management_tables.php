<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Http\Helpers\AppHelper;

/**
 * Migration for creating the fee management module tables
 * 
 * This migration creates all the necessary tables for the fee management module.
 */
class CreateFeeManagementTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create fee_types table
        Schema::create('fee_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->unsignedInteger('class_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', [AppHelper::ACTIVE, AppHelper::INACTIVE])->default(AppHelper::ACTIVE);
            $table->boolean('is_recurring')->default(false);
            $table->string('frequency', 20)->nullable();
            $table->integer('due_day')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('class_id')->references('id')->on('i_classes')->onDelete('set null');
        });

        // Create fee_invoices table
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no', 20)->unique();
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('class_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('fee_type_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('fine', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('due_amount', 10, 2);
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('status', 20)->default('unpaid');
            $table->unsignedInteger('academic_year_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('registrations')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('i_classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('fee_type_id')->references('id')->on('fee_types')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });

        // Create fee_payments table
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fee_invoice_id');
            $table->string('payment_method', 20);
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id', 100)->nullable();
            $table->dateTime('payment_date');
            $table->string('status', 20)->default('completed');
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('fee_invoice_id')->references('id')->on('fee_invoices')->onDelete('cascade');
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
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_invoices');
        Schema::dropIfExists('fee_types');
    }
}
