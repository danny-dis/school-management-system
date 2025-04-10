<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\FeeType;
use App\FeeInvoice;
use App\FeePayment;
use App\IClass;
use App\Registration;
use App\Section;
use App\AcademicYear;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * FeeManagementController
 * 
 * This controller handles the fee management module functionality.
 */
class FeeManagementController extends Controller
{
    /**
     * Display a listing of fee types.
     *
     * @return \Illuminate\Http\Response
     */
    public function feeTypes()
    {
        $feeTypes = FeeType::with('class')->orderBy('id', 'desc')->paginate(10);
        return view('backend.fee_management.fee_types.index', compact('feeTypes'));
    }

    /**
     * Show the form for creating a new fee type.
     *
     * @return \Illuminate\Http\Response
     */
    public function createFeeType()
    {
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.fee_management.fee_types.create', compact('classes'));
    }

    /**
     * Store a newly created fee type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFeeType(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'class_id' => 'nullable|integer|exists:i_classes,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'is_recurring' => 'nullable|boolean',
            'frequency' => 'required_if:is_recurring,1|nullable|string|in:monthly,quarterly,half_yearly,yearly',
            'due_day' => 'required_if:is_recurring,1|nullable|integer|min:1|max:31'
        ]);

        // Create fee type
        $feeType = FeeType::create([
            'name' => $request->name,
            'description' => $request->description,
            'class_id' => $request->class_id,
            'amount' => $request->amount,
            'status' => $request->status,
            'is_recurring' => $request->has('is_recurring'),
            'frequency' => $request->frequency,
            'due_day' => $request->due_day
        ]);

        return redirect()->route('fee_management.fee_types')->with('success', 'Fee type created successfully!');
    }

    /**
     * Display the specified fee type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showFeeType($id)
    {
        $feeType = FeeType::with('class')->findOrFail($id);
        
        return view('backend.fee_management.fee_types.show', compact('feeType'));
    }

    /**
     * Show the form for editing the specified fee type.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editFeeType($id)
    {
        $feeType = FeeType::findOrFail($id);
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.fee_management.fee_types.edit', compact('feeType', 'classes'));
    }

    /**
     * Update the specified fee type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFeeType(Request $request, $id)
    {
        $feeType = FeeType::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'class_id' => 'nullable|integer|exists:i_classes,id',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'is_recurring' => 'nullable|boolean',
            'frequency' => 'required_if:is_recurring,1|nullable|string|in:monthly,quarterly,half_yearly,yearly',
            'due_day' => 'required_if:is_recurring,1|nullable|integer|min:1|max:31'
        ]);

        // Update fee type
        $feeType->name = $request->name;
        $feeType->description = $request->description;
        $feeType->class_id = $request->class_id;
        $feeType->amount = $request->amount;
        $feeType->status = $request->status;
        $feeType->is_recurring = $request->has('is_recurring');
        $feeType->frequency = $request->frequency;
        $feeType->due_day = $request->due_day;
        $feeType->save();

        return redirect()->route('fee_management.fee_types')->with('success', 'Fee type updated successfully!');
    }

    /**
     * Remove the specified fee type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyFeeType($id)
    {
        $feeType = FeeType::findOrFail($id);
        
        // Check if fee type has invoices
        if ($feeType->invoices()->count() > 0) {
            return redirect()->route('fee_management.fee_types')->with('error', 'Cannot delete fee type with existing invoices!');
        }
        
        $feeType->delete();
        
        return redirect()->route('fee_management.fee_types')->with('success', 'Fee type deleted successfully!');
    }

    /**
     * Display a listing of fee invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function invoices()
    {
        $invoices = FeeInvoice::with('student.student', 'class', 'feeType')
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return view('backend.fee_management.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new fee invoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function createInvoice()
    {
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $feeTypes = FeeType::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $academicYears = AcademicYear::where('status', '1')->orderBy('id', 'desc')->pluck('title', 'id');
        
        return view('backend.fee_management.invoices.create', compact('classes', 'feeTypes', 'academicYears'));
    }

    /**
     * Get sections and students for a class.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getClassDetails(Request $request)
    {
        $classId = $request->class_id;
        
        $sections = Section::where('class_id', $classId)->pluck('name', 'id');
        
        return response()->json([
            'sections' => $sections
        ]);
    }

    /**
     * Get students for a section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSectionStudents(Request $request)
    {
        $sectionId = $request->section_id;
        
        $students = Registration::where('section_id', $sectionId)
            ->where('status', AppHelper::ACTIVE)
            ->with('student')
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'name' => $registration->student->name,
                    'roll_no' => $registration->roll_no
                ];
            });
        
        return response()->json([
            'students' => $students
        ]);
    }

    /**
     * Get fee type details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFeeTypeDetails(Request $request)
    {
        $feeTypeId = $request->fee_type_id;
        
        $feeType = FeeType::findOrFail($feeTypeId);
        
        return response()->json([
            'amount' => $feeType->amount,
            'description' => $feeType->description
        ]);
    }

    /**
     * Store a newly created fee invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInvoice(Request $request)
    {
        $this->validate($request, [
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'student_id' => 'required|integer|exists:registrations,id',
            'fee_type_id' => 'required|integer|exists:fee_types,id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'notes' => 'nullable|string'
        ]);

        // Calculate total and due amount
        $amount = $request->amount;
        $discount = $request->discount ?? 0;
        $fine = $request->fine ?? 0;
        $totalAmount = $amount - $discount + $fine;
        
        // Generate invoice number
        $invoiceNo = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
        
        // Create invoice
        $invoice = FeeInvoice::create([
            'invoice_no' => $invoiceNo,
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'fee_type_id' => $request->fee_type_id,
            'amount' => $amount,
            'discount' => $discount,
            'fine' => $fine,
            'total_amount' => $totalAmount,
            'due_amount' => $totalAmount,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'status' => FeeInvoice::STATUS_UNPAID,
            'academic_year_id' => $request->academic_year_id,
            'notes' => $request->notes
        ]);

        return redirect()->route('fee_management.invoices.show', $invoice->id)->with('success', 'Invoice created successfully!');
    }

    /**
     * Display the specified fee invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showInvoice($id)
    {
        $invoice = FeeInvoice::with('student.student', 'class', 'section', 'feeType', 'academicYear', 'payments')
            ->findOrFail($id);
        
        return view('backend.fee_management.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified fee invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editInvoice($id)
    {
        $invoice = FeeInvoice::with('student.student', 'class', 'section', 'feeType', 'academicYear')
            ->findOrFail($id);
            
        // Check if invoice has payments
        if ($invoice->payments()->count() > 0) {
            return redirect()->route('fee_management.invoices.show', $id)
                ->with('error', 'Cannot edit invoice with existing payments!');
        }
        
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $sections = Section::where('class_id', $invoice->class_id)->pluck('name', 'id');
        $students = Registration::where('section_id', $invoice->section_id)
            ->where('status', AppHelper::ACTIVE)
            ->with('student')
            ->get()
            ->pluck('student.name', 'id');
        $feeTypes = FeeType::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $academicYears = AcademicYear::where('status', '1')->orderBy('id', 'desc')->pluck('title', 'id');
        
        return view('backend.fee_management.invoices.edit', compact('invoice', 'classes', 'sections', 'students', 'feeTypes', 'academicYears'));
    }

    /**
     * Update the specified fee invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateInvoice(Request $request, $id)
    {
        $invoice = FeeInvoice::findOrFail($id);
        
        // Check if invoice has payments
        if ($invoice->payments()->count() > 0) {
            return redirect()->route('fee_management.invoices.show', $id)
                ->with('error', 'Cannot edit invoice with existing payments!');
        }
        
        $this->validate($request, [
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'fine' => 'nullable|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string'
        ]);

        // Calculate total and due amount
        $amount = $request->amount;
        $discount = $request->discount ?? 0;
        $fine = $request->fine ?? 0;
        $totalAmount = $amount - $discount + $fine;
        
        // Update invoice
        $invoice->amount = $amount;
        $invoice->discount = $discount;
        $invoice->fine = $fine;
        $invoice->total_amount = $totalAmount;
        $invoice->due_amount = $totalAmount;
        $invoice->issue_date = $request->issue_date;
        $invoice->due_date = $request->due_date;
        $invoice->notes = $request->notes;
        $invoice->save();

        return redirect()->route('fee_management.invoices.show', $id)->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified fee invoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyInvoice($id)
    {
        $invoice = FeeInvoice::findOrFail($id);
        
        // Check if invoice has payments
        if ($invoice->payments()->count() > 0) {
            return redirect()->route('fee_management.invoices')->with('error', 'Cannot delete invoice with existing payments!');
        }
        
        $invoice->delete();
        
        return redirect()->route('fee_management.invoices')->with('success', 'Invoice deleted successfully!');
    }

    /**
     * Show the form for adding a payment to an invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addPayment($id)
    {
        $invoice = FeeInvoice::with('student.student', 'class', 'section', 'feeType')
            ->findOrFail($id);
            
        // Check if invoice is already paid or cancelled
        if ($invoice->status == FeeInvoice::STATUS_PAID || $invoice->status == FeeInvoice::STATUS_CANCELLED) {
            return redirect()->route('fee_management.invoices.show', $id)
                ->with('error', 'Cannot add payment to a paid or cancelled invoice!');
        }
        
        return view('backend.fee_management.payments.create', compact('invoice'));
    }

    /**
     * Store a newly created payment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storePayment(Request $request, $id)
    {
        $invoice = FeeInvoice::findOrFail($id);
        
        // Check if invoice is already paid or cancelled
        if ($invoice->status == FeeInvoice::STATUS_PAID || $invoice->status == FeeInvoice::STATUS_CANCELLED) {
            return redirect()->route('fee_management.invoices.show', $id)
                ->with('error', 'Cannot add payment to a paid or cancelled invoice!');
        }
        
        $this->validate($request, [
            'payment_method' => 'required|string|in:cash,cheque,bank_transfer,online,other',
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->due_amount,
            'transaction_id' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Create payment
        $payment = FeePayment::create([
            'fee_invoice_id' => $invoice->id,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'transaction_id' => $request->transaction_id,
            'payment_date' => $request->payment_date,
            'status' => FeePayment::STATUS_COMPLETED,
            'notes' => $request->notes,
            'created_by' => Auth::id()
        ]);

        // Update invoice
        $paidAmount = $invoice->paid_amount + $request->amount;
        $dueAmount = $invoice->total_amount - $paidAmount;
        
        $status = FeeInvoice::STATUS_UNPAID;
        if ($dueAmount <= 0) {
            $status = FeeInvoice::STATUS_PAID;
        } elseif ($paidAmount > 0) {
            $status = FeeInvoice::STATUS_PARTIALLY_PAID;
        }
        
        $invoice->paid_amount = $paidAmount;
        $invoice->due_amount = $dueAmount;
        $invoice->status = $status;
        $invoice->save();

        return redirect()->route('fee_management.invoices.show', $id)->with('success', 'Payment added successfully!');
    }

    /**
     * Cancel the specified invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelInvoice($id)
    {
        $invoice = FeeInvoice::findOrFail($id);
        
        // Check if invoice has payments
        if ($invoice->payments()->count() > 0) {
            return redirect()->route('fee_management.invoices.show', $id)
                ->with('error', 'Cannot cancel invoice with existing payments!');
        }
        
        $invoice->status = FeeInvoice::STATUS_CANCELLED;
        $invoice->save();
        
        return redirect()->route('fee_management.invoices.show', $id)->with('success', 'Invoice cancelled successfully!');
    }

    /**
     * Print the specified invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        $invoice = FeeInvoice::with('student.student', 'class', 'section', 'feeType', 'academicYear', 'payments')
            ->findOrFail($id);
        
        return view('backend.fee_management.invoices.print', compact('invoice'));
    }

    /**
     * Generate invoices for recurring fees.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateRecurringInvoices()
    {
        // Get active academic year
        $academicYear = AcademicYear::where('status', '1')->first();
        if (!$academicYear) {
            return redirect()->route('fee_management.invoices')->with('error', 'No active academic year found!');
        }
        
        // Get recurring fee types
        $feeTypes = FeeType::where('status', AppHelper::ACTIVE)
            ->where('is_recurring', true)
            ->get();
            
        $count = 0;
        
        foreach ($feeTypes as $feeType) {
            // Determine the current period based on frequency
            $today = Carbon::today();
            $currentDay = $today->day;
            $dueDay = $feeType->due_day ?? 10;
            
            // Skip if not the right time to generate invoices
            if ($currentDay != $dueDay) {
                continue;
            }
            
            // Get students for this fee type
            $query = Registration::where('status', AppHelper::ACTIVE);
            
            if ($feeType->class_id) {
                $query->where('class_id', $feeType->class_id);
            }
            
            $students = $query->get();
            
            foreach ($students as $student) {
                // Check if invoice already exists for this period
                $existingInvoice = FeeInvoice::where('student_id', $student->id)
                    ->where('fee_type_id', $feeType->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->whereMonth('issue_date', $today->month)
                    ->whereYear('issue_date', $today->year)
                    ->first();
                    
                if ($existingInvoice) {
                    continue;
                }
                
                // Generate invoice number
                $invoiceNo = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
                
                // Calculate due date based on frequency
                $dueDate = $today->copy();
                switch ($feeType->frequency) {
                    case FeeType::FREQUENCY_MONTHLY:
                        $dueDate->addDays(30);
                        break;
                    case FeeType::FREQUENCY_QUARTERLY:
                        $dueDate->addMonths(3);
                        break;
                    case FeeType::FREQUENCY_HALF_YEARLY:
                        $dueDate->addMonths(6);
                        break;
                    case FeeType::FREQUENCY_YEARLY:
                        $dueDate->addYear();
                        break;
                }
                
                // Create invoice
                FeeInvoice::create([
                    'invoice_no' => $invoiceNo,
                    'student_id' => $student->id,
                    'class_id' => $student->class_id,
                    'section_id' => $student->section_id,
                    'fee_type_id' => $feeType->id,
                    'amount' => $feeType->amount,
                    'total_amount' => $feeType->amount,
                    'due_amount' => $feeType->amount,
                    'issue_date' => $today,
                    'due_date' => $dueDate,
                    'status' => FeeInvoice::STATUS_UNPAID,
                    'academic_year_id' => $academicYear->id
                ]);
                
                $count++;
            }
        }
        
        return redirect()->route('fee_management.invoices')->with('success', $count . ' recurring invoices generated successfully!');
    }
}
