<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\FeeType;
use App\FeeInvoice;
use App\FeePayment;
use App\IClass;
use App\Registration;
use App\Section;
use App\AcademicYear;
use App\Http\Helpers\AppHelper;

class FeeManagementTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test fee type creation.
     *
     * @return void
     */
    public function testFeeTypeCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create a class for the fee type
        $class = factory(IClass::class)->create();
        
        // Test fee type creation
        $response = $this->post(route('fee_management.fee_types.store'), [
            'name' => 'Test Fee Type',
            'description' => 'This is a test fee type',
            'class_id' => $class->id,
            'amount' => 1000,
            'status' => AppHelper::ACTIVE,
            'is_recurring' => true,
            'frequency' => FeeType::FREQUENCY_MONTHLY,
            'due_day' => 10
        ]);
        
        $response->assertRedirect(route('fee_management.fee_types'));
        $this->assertDatabaseHas('fee_types', [
            'name' => 'Test Fee Type',
            'amount' => 1000
        ]);
    }
    
    /**
     * Test invoice creation.
     *
     * @return void
     */
    public function testInvoiceCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create necessary data
        $class = factory(IClass::class)->create();
        $section = factory(Section::class)->create(['class_id' => $class->id]);
        $student = factory(Registration::class)->create([
            'class_id' => $class->id,
            'section_id' => $section->id
        ]);
        $feeType = factory(FeeType::class)->create([
            'class_id' => $class->id,
            'amount' => 1000
        ]);
        $academicYear = factory(AcademicYear::class)->create(['status' => '1']);
        
        // Test invoice creation
        $response = $this->post(route('fee_management.invoices.store'), [
            'class_id' => $class->id,
            'section_id' => $section->id,
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'amount' => 1000,
            'discount' => 100,
            'fine' => 50,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'academic_year_id' => $academicYear->id,
            'notes' => 'Test invoice'
        ]);
        
        $invoice = FeeInvoice::first();
        
        $response->assertRedirect(route('fee_management.invoices.show', $invoice->id));
        $this->assertDatabaseHas('fee_invoices', [
            'student_id' => $student->id,
            'amount' => 1000,
            'discount' => 100,
            'fine' => 50,
            'total_amount' => 950
        ]);
    }
    
    /**
     * Test payment creation.
     *
     * @return void
     */
    public function testPaymentCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create necessary data
        $class = factory(IClass::class)->create();
        $section = factory(Section::class)->create(['class_id' => $class->id]);
        $student = factory(Registration::class)->create([
            'class_id' => $class->id,
            'section_id' => $section->id
        ]);
        $feeType = factory(FeeType::class)->create([
            'class_id' => $class->id,
            'amount' => 1000
        ]);
        $academicYear = factory(AcademicYear::class)->create(['status' => '1']);
        
        // Create an invoice
        $invoice = FeeInvoice::create([
            'invoice_no' => 'INV-' . date('Ymd') . '-' . rand(1000, 9999),
            'student_id' => $student->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'fee_type_id' => $feeType->id,
            'amount' => 1000,
            'discount' => 100,
            'fine' => 50,
            'total_amount' => 950,
            'due_amount' => 950,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => FeeInvoice::STATUS_UNPAID,
            'academic_year_id' => $academicYear->id,
            'notes' => 'Test invoice'
        ]);
        
        // Test payment creation
        $response = $this->post(route('fee_management.payments.store', $invoice->id), [
            'payment_method' => FeePayment::METHOD_CASH,
            'amount' => 500,
            'payment_date' => now()->format('Y-m-d'),
            'notes' => 'Test payment'
        ]);
        
        $response->assertRedirect(route('fee_management.invoices.show', $invoice->id));
        $this->assertDatabaseHas('fee_payments', [
            'fee_invoice_id' => $invoice->id,
            'payment_method' => FeePayment::METHOD_CASH,
            'amount' => 500
        ]);
        
        // Check if invoice is updated
        $updatedInvoice = FeeInvoice::find($invoice->id);
        $this->assertEquals(500, $updatedInvoice->paid_amount);
        $this->assertEquals(450, $updatedInvoice->due_amount);
        $this->assertEquals(FeeInvoice::STATUS_PARTIALLY_PAID, $updatedInvoice->status);
    }
    
    /**
     * Test full payment and invoice status change.
     *
     * @return void
     */
    public function testFullPayment()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create necessary data
        $class = factory(IClass::class)->create();
        $section = factory(Section::class)->create(['class_id' => $class->id]);
        $student = factory(Registration::class)->create([
            'class_id' => $class->id,
            'section_id' => $section->id
        ]);
        $feeType = factory(FeeType::class)->create([
            'class_id' => $class->id,
            'amount' => 1000
        ]);
        $academicYear = factory(AcademicYear::class)->create(['status' => '1']);
        
        // Create an invoice
        $invoice = FeeInvoice::create([
            'invoice_no' => 'INV-' . date('Ymd') . '-' . rand(1000, 9999),
            'student_id' => $student->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'fee_type_id' => $feeType->id,
            'amount' => 1000,
            'total_amount' => 1000,
            'due_amount' => 1000,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => FeeInvoice::STATUS_UNPAID,
            'academic_year_id' => $academicYear->id
        ]);
        
        // Test full payment
        $response = $this->post(route('fee_management.payments.store', $invoice->id), [
            'payment_method' => FeePayment::METHOD_CASH,
            'amount' => 1000,
            'payment_date' => now()->format('Y-m-d')
        ]);
        
        // Check if invoice is updated to paid
        $updatedInvoice = FeeInvoice::find($invoice->id);
        $this->assertEquals(1000, $updatedInvoice->paid_amount);
        $this->assertEquals(0, $updatedInvoice->due_amount);
        $this->assertEquals(FeeInvoice::STATUS_PAID, $updatedInvoice->status);
    }
}
