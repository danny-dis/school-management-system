<?php

namespace App\Services;

use App\Models\FeeType;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FeeManagementService
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * FeeManagementService constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a fee type
     *
     * @param array $data
     * @param int $createdBy
     * @return FeeType|null
     */
    public function createFeeType(array $data, $createdBy)
    {
        try {
            return FeeType::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'amount' => $data['amount'],
                'frequency' => $data['frequency'],
                'applicable_to' => $data['applicable_to'],
                'class_id' => $data['class_id'] ?? null,
                'status' => $data['status'] ?? FeeType::STATUS_ACTIVE,
                'created_by' => $createdBy
            ]);
        } catch (Exception $e) {
            Log::error('Error creating fee type: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a fee type
     *
     * @param int $id
     * @param array $data
     * @return FeeType|null
     */
    public function updateFeeType($id, array $data)
    {
        try {
            $feeType = FeeType::find($id);
            
            if (!$feeType) {
                return null;
            }
            
            $feeType->update($data);
            return $feeType;
        } catch (Exception $e) {
            Log::error('Error updating fee type: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a fee type
     *
     * @param int $id
     * @return bool
     */
    public function deleteFeeType($id)
    {
        try {
            $feeType = FeeType::find($id);
            
            if (!$feeType) {
                return false;
            }
            
            // Check if fee type is used in any invoice
            $invoiceCount = FeeInvoice::where('fee_type_id', $id)->count();
            
            if ($invoiceCount > 0) {
                // Don't delete, just mark as inactive
                $feeType->status = FeeType::STATUS_INACTIVE;
                $feeType->save();
            } else {
                $feeType->delete();
            }
            
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting fee type: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a fee invoice
     *
     * @param array $data
     * @param int $createdBy
     * @return FeeInvoice|null
     */
    public function createFeeInvoice(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $invoice = FeeInvoice::create([
                'student_id' => $data['student_id'],
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'fee_type_id' => $data['fee_type_id'],
                'invoice_no' => $this->generateInvoiceNumber(),
                'amount' => $data['amount'],
                'discount' => $data['discount'] ?? 0,
                'fine' => $data['fine'] ?? 0,
                'total' => $data['amount'] - ($data['discount'] ?? 0) + ($data['fine'] ?? 0),
                'paid' => 0,
                'due' => $data['amount'] - ($data['discount'] ?? 0) + ($data['fine'] ?? 0),
                'issue_date' => $data['issue_date'] ?? now(),
                'due_date' => $data['due_date'],
                'status' => FeeInvoice::STATUS_UNPAID,
                'description' => $data['description'] ?? null,
                'academic_year_id' => $data['academic_year_id'],
                'created_by' => $createdBy
            ]);
            
            // Notify student about invoice
            $student = Student::find($data['student_id']);
            if ($student && $student->user_id) {
                $this->notificationService->createNotification(
                    $student->user_id,
                    'New Fee Invoice',
                    'A new fee invoice has been generated: ' . $invoice->invoice_no,
                    route('fee_management.invoices.show', $invoice->id),
                    'info'
                );
            }
            
            DB::commit();
            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating fee invoice: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a fee invoice
     *
     * @param int $id
     * @param array $data
     * @return FeeInvoice|null
     */
    public function updateFeeInvoice($id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $invoice = FeeInvoice::find($id);
            
            if (!$invoice) {
                return null;
            }
            
            // Calculate new total and due
            $amount = $data['amount'] ?? $invoice->amount;
            $discount = $data['discount'] ?? $invoice->discount;
            $fine = $data['fine'] ?? $invoice->fine;
            $total = $amount - $discount + $fine;
            $due = $total - $invoice->paid;
            
            $updateData = array_merge($data, [
                'total' => $total,
                'due' => $due
            ]);
            
            // Update status if needed
            if ($due <= 0 && $invoice->status != FeeInvoice::STATUS_PAID) {
                $updateData['status'] = FeeInvoice::STATUS_PAID;
            } elseif ($due > 0 && $invoice->paid > 0 && $invoice->status != FeeInvoice::STATUS_PARTIALLY_PAID) {
                $updateData['status'] = FeeInvoice::STATUS_PARTIALLY_PAID;
            } elseif ($due > 0 && $invoice->paid == 0 && $invoice->status != FeeInvoice::STATUS_UNPAID) {
                $updateData['status'] = FeeInvoice::STATUS_UNPAID;
            }
            
            $invoice->update($updateData);
            
            DB::commit();
            return $invoice;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating fee invoice: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a fee invoice
     *
     * @param int $id
     * @return bool
     */
    public function deleteFeeInvoice($id)
    {
        try {
            DB::beginTransaction();
            
            $invoice = FeeInvoice::find($id);
            
            if (!$invoice) {
                return false;
            }
            
            // Check if invoice has payments
            $paymentCount = FeePayment::where('invoice_id', $id)->count();
            
            if ($paymentCount > 0) {
                // Don't delete, just mark as cancelled
                $invoice->status = FeeInvoice::STATUS_CANCELLED;
                $invoice->save();
            } else {
                $invoice->delete();
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting fee invoice: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a fee payment
     *
     * @param array $data
     * @param int $createdBy
     * @return FeePayment|null
     */
    public function createFeePayment(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $invoice = FeeInvoice::find($data['invoice_id']);
            
            if (!$invoice) {
                return null;
            }
            
            // Check if payment amount is valid
            $amount = $data['amount'];
            
            if ($amount > $invoice->due) {
                throw new Exception('Payment amount cannot be greater than due amount');
            }
            
            $payment = FeePayment::create([
                'invoice_id' => $data['invoice_id'],
                'receipt_no' => $this->generateReceiptNumber(),
                'amount' => $amount,
                'payment_date' => $data['payment_date'] ?? now(),
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'note' => $data['note'] ?? null,
                'created_by' => $createdBy
            ]);
            
            // Update invoice
            $invoice->paid += $amount;
            $invoice->due -= $amount;
            
            if ($invoice->due <= 0) {
                $invoice->status = FeeInvoice::STATUS_PAID;
            } else {
                $invoice->status = FeeInvoice::STATUS_PARTIALLY_PAID;
            }
            
            $invoice->save();
            
            // Notify student about payment
            $student = Student::find($invoice->student_id);
            if ($student && $student->user_id) {
                $this->notificationService->createNotification(
                    $student->user_id,
                    'Fee Payment Received',
                    'Your fee payment of ' . $amount . ' has been received for invoice: ' . $invoice->invoice_no,
                    route('fee_management.payments.show', $payment->id),
                    'success'
                );
            }
            
            DB::commit();
            return $payment;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating fee payment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a fee payment
     *
     * @param int $id
     * @return bool
     */
    public function deleteFeePayment($id)
    {
        try {
            DB::beginTransaction();
            
            $payment = FeePayment::find($id);
            
            if (!$payment) {
                return false;
            }
            
            // Update invoice
            $invoice = $payment->invoice;
            $invoice->paid -= $payment->amount;
            $invoice->due += $payment->amount;
            
            if ($invoice->paid <= 0) {
                $invoice->status = FeeInvoice::STATUS_UNPAID;
            } else {
                $invoice->status = FeeInvoice::STATUS_PARTIALLY_PAID;
            }
            
            $invoice->save();
            
            // Delete payment
            $payment->delete();
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting fee payment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Generate bulk invoices
     *
     * @param int $feeTypeId
     * @param int $classId
     * @param int|null $sectionId
     * @param int $academicYearId
     * @param string $issueDate
     * @param string $dueDate
     * @param int $createdBy
     * @return int Number of invoices created
     */
    public function generateBulkInvoices($feeTypeId, $classId, $sectionId, $academicYearId, $issueDate, $dueDate, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $feeType = FeeType::find($feeTypeId);
            
            if (!$feeType) {
                return 0;
            }
            
            // Get students
            $query = Student::whereHas('registration', function ($q) use ($classId, $sectionId, $academicYearId) {
                $q->where('class_id', $classId)
                    ->where('academic_year_id', $academicYearId);
                
                if ($sectionId) {
                    $q->where('section_id', $sectionId);
                }
            });
            
            $students = $query->get();
            $count = 0;
            
            foreach ($students as $student) {
                // Check if invoice already exists
                $existingInvoice = FeeInvoice::where('student_id', $student->id)
                    ->where('fee_type_id', $feeTypeId)
                    ->where('academic_year_id', $academicYearId)
                    ->first();
                
                if ($existingInvoice) {
                    continue;
                }
                
                // Get student registration
                $registration = $student->registration()
                    ->where('academic_year_id', $academicYearId)
                    ->first();
                
                if (!$registration) {
                    continue;
                }
                
                // Create invoice
                $invoice = FeeInvoice::create([
                    'student_id' => $student->id,
                    'class_id' => $registration->class_id,
                    'section_id' => $registration->section_id,
                    'fee_type_id' => $feeTypeId,
                    'invoice_no' => $this->generateInvoiceNumber(),
                    'amount' => $feeType->amount,
                    'discount' => 0,
                    'fine' => 0,
                    'total' => $feeType->amount,
                    'paid' => 0,
                    'due' => $feeType->amount,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'status' => FeeInvoice::STATUS_UNPAID,
                    'academic_year_id' => $academicYearId,
                    'created_by' => $createdBy
                ]);
                
                // Notify student about invoice
                if ($student->user_id) {
                    $this->notificationService->createNotification(
                        $student->user_id,
                        'New Fee Invoice',
                        'A new fee invoice has been generated: ' . $invoice->invoice_no,
                        route('fee_management.invoices.show', $invoice->id),
                        'info'
                    );
                }
                
                $count++;
            }
            
            DB::commit();
            return $count;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error generating bulk invoices: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Get fee types
     *
     * @param bool $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeeTypes($activeOnly = false)
    {
        $query = FeeType::query();
        
        if ($activeOnly) {
            $query->where('status', FeeType::STATUS_ACTIVE);
        }
        
        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Get fee invoices for a student
     *
     * @param int $studentId
     * @param int|null $academicYearId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInvoicesForStudent($studentId, $academicYearId = null)
    {
        $query = FeeInvoice::with(['feeType', 'payments'])
            ->where('student_id', $studentId);
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        return $query->orderBy('issue_date', 'desc')->get();
    }

    /**
     * Get fee invoices for a class
     *
     * @param int $classId
     * @param int|null $sectionId
     * @param int|null $academicYearId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInvoicesForClass($classId, $sectionId = null, $academicYearId = null)
    {
        $query = FeeInvoice::with(['student', 'feeType'])
            ->where('class_id', $classId);
        
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        return $query->orderBy('issue_date', 'desc')->get();
    }

    /**
     * Get payments for an invoice
     *
     * @param int $invoiceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPaymentsForInvoice($invoiceId)
    {
        return FeePayment::where('invoice_id', $invoiceId)
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Generate invoice number
     *
     * @return string
     */
    protected function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = FeeInvoice::orderBy('id', 'desc')->first();
        
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_no, -5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate receipt number
     *
     * @return string
     */
    protected function generateReceiptNumber()
    {
        $prefix = 'RCPT';
        $year = date('Y');
        $month = date('m');
        
        $lastPayment = FeePayment::orderBy('id', 'desc')->first();
        
        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->receipt_no, -5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
