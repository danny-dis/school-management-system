<?php

namespace App\Services;

use App\Models\HealthRecord;
use App\Models\MedicalVisit;
use App\Models\Vaccination;
use App\Models\Allergy;
use App\Models\Medication;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class HealthRecordService
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * HealthRecordService constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create or update a health record
     *
     * @param array $data
     * @param int $createdBy
     * @return HealthRecord|null
     */
    public function createOrUpdateHealthRecord(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            // Check if record already exists
            $record = HealthRecord::where('student_id', $data['student_id'])->first();
            
            if ($record) {
                // Update existing record
                $record->update([
                    'blood_group' => $data['blood_group'] ?? $record->blood_group,
                    'height' => $data['height'] ?? $record->height,
                    'weight' => $data['weight'] ?? $record->weight,
                    'medical_conditions' => $data['medical_conditions'] ?? $record->medical_conditions,
                    'emergency_contact' => $data['emergency_contact'] ?? $record->emergency_contact,
                    'emergency_contact_phone' => $data['emergency_contact_phone'] ?? $record->emergency_contact_phone,
                    'doctor_name' => $data['doctor_name'] ?? $record->doctor_name,
                    'doctor_phone' => $data['doctor_phone'] ?? $record->doctor_phone,
                    'notes' => $data['notes'] ?? $record->notes,
                    'updated_by' => $createdBy
                ]);
            } else {
                // Create new record
                $record = HealthRecord::create([
                    'student_id' => $data['student_id'],
                    'blood_group' => $data['blood_group'] ?? null,
                    'height' => $data['height'] ?? null,
                    'weight' => $data['weight'] ?? null,
                    'medical_conditions' => $data['medical_conditions'] ?? null,
                    'emergency_contact' => $data['emergency_contact'] ?? null,
                    'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                    'doctor_name' => $data['doctor_name'] ?? null,
                    'doctor_phone' => $data['doctor_phone'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'created_by' => $createdBy,
                    'updated_by' => $createdBy
                ]);
            }
            
            DB::commit();
            return $record;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating/updating health record: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Add a medical visit
     *
     * @param array $data
     * @param int $createdBy
     * @return MedicalVisit|null
     */
    public function addMedicalVisit(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $visit = MedicalVisit::create([
                'student_id' => $data['student_id'],
                'visit_date' => $data['visit_date'],
                'reason' => $data['reason'],
                'diagnosis' => $data['diagnosis'] ?? null,
                'treatment' => $data['treatment'] ?? null,
                'notes' => $data['notes'] ?? null,
                'follow_up_date' => $data['follow_up_date'] ?? null,
                'created_by' => $createdBy
            ]);
            
            // Notify parent about medical visit
            $student = Student::find($data['student_id']);
            
            if ($student) {
                // Notify student
                if ($student->user_id) {
                    $this->notificationService->createNotification(
                        $student->user_id,
                        'Medical Visit Recorded',
                        'A medical visit has been recorded for you on ' . $data['visit_date'],
                        route('health_records.visits.show', $visit->id),
                        'info'
                    );
                }
                
                // Notify parent if available
                if ($student->parent_id) {
                    $parent = \App\Models\ParentInfo::find($student->parent_id);
                    
                    if ($parent && $parent->user_id) {
                        $this->notificationService->createNotification(
                            $parent->user_id,
                            'Medical Visit Recorded',
                            'A medical visit has been recorded for your child on ' . $data['visit_date'],
                            route('health_records.visits.show', $visit->id),
                            'info'
                        );
                    }
                }
            }
            
            DB::commit();
            return $visit;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error adding medical visit: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a medical visit
     *
     * @param int $id
     * @param array $data
     * @return MedicalVisit|null
     */
    public function updateMedicalVisit($id, array $data)
    {
        try {
            $visit = MedicalVisit::find($id);
            
            if (!$visit) {
                return null;
            }
            
            $visit->update($data);
            return $visit;
        } catch (Exception $e) {
            Log::error('Error updating medical visit: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a medical visit
     *
     * @param int $id
     * @return bool
     */
    public function deleteMedicalVisit($id)
    {
        try {
            $visit = MedicalVisit::find($id);
            
            if (!$visit) {
                return false;
            }
            
            $visit->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting medical visit: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Add a vaccination
     *
     * @param array $data
     * @param int $createdBy
     * @return Vaccination|null
     */
    public function addVaccination(array $data, $createdBy)
    {
        try {
            return Vaccination::create([
                'student_id' => $data['student_id'],
                'name' => $data['name'],
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy
            ]);
        } catch (Exception $e) {
            Log::error('Error adding vaccination: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a vaccination
     *
     * @param int $id
     * @param array $data
     * @return Vaccination|null
     */
    public function updateVaccination($id, array $data)
    {
        try {
            $vaccination = Vaccination::find($id);
            
            if (!$vaccination) {
                return null;
            }
            
            $vaccination->update($data);
            return $vaccination;
        } catch (Exception $e) {
            Log::error('Error updating vaccination: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a vaccination
     *
     * @param int $id
     * @return bool
     */
    public function deleteVaccination($id)
    {
        try {
            $vaccination = Vaccination::find($id);
            
            if (!$vaccination) {
                return false;
            }
            
            $vaccination->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting vaccination: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Add an allergy
     *
     * @param array $data
     * @param int $createdBy
     * @return Allergy|null
     */
    public function addAllergy(array $data, $createdBy)
    {
        try {
            return Allergy::create([
                'student_id' => $data['student_id'],
                'name' => $data['name'],
                'severity' => $data['severity'] ?? 'Moderate',
                'reaction' => $data['reaction'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy
            ]);
        } catch (Exception $e) {
            Log::error('Error adding allergy: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update an allergy
     *
     * @param int $id
     * @param array $data
     * @return Allergy|null
     */
    public function updateAllergy($id, array $data)
    {
        try {
            $allergy = Allergy::find($id);
            
            if (!$allergy) {
                return null;
            }
            
            $allergy->update($data);
            return $allergy;
        } catch (Exception $e) {
            Log::error('Error updating allergy: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete an allergy
     *
     * @param int $id
     * @return bool
     */
    public function deleteAllergy($id)
    {
        try {
            $allergy = Allergy::find($id);
            
            if (!$allergy) {
                return false;
            }
            
            $allergy->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting allergy: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Add a medication
     *
     * @param array $data
     * @param int $createdBy
     * @return Medication|null
     */
    public function addMedication(array $data, $createdBy)
    {
        try {
            return Medication::create([
                'student_id' => $data['student_id'],
                'name' => $data['name'],
                'dosage' => $data['dosage'],
                'frequency' => $data['frequency'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy
            ]);
        } catch (Exception $e) {
            Log::error('Error adding medication: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a medication
     *
     * @param int $id
     * @param array $data
     * @return Medication|null
     */
    public function updateMedication($id, array $data)
    {
        try {
            $medication = Medication::find($id);
            
            if (!$medication) {
                return null;
            }
            
            $medication->update($data);
            return $medication;
        } catch (Exception $e) {
            Log::error('Error updating medication: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a medication
     *
     * @param int $id
     * @return bool
     */
    public function deleteMedication($id)
    {
        try {
            $medication = Medication::find($id);
            
            if (!$medication) {
                return false;
            }
            
            $medication->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting medication: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get health record for a student
     *
     * @param int $studentId
     * @return HealthRecord|null
     */
    public function getHealthRecord($studentId)
    {
        return HealthRecord::where('student_id', $studentId)->first();
    }

    /**
     * Get medical visits for a student
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMedicalVisits($studentId)
    {
        return MedicalVisit::where('student_id', $studentId)
            ->orderBy('visit_date', 'desc')
            ->get();
    }

    /**
     * Get vaccinations for a student
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVaccinations($studentId)
    {
        return Vaccination::where('student_id', $studentId)
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get allergies for a student
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllergies($studentId)
    {
        return Allergy::where('student_id', $studentId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get medications for a student
     *
     * @param int $studentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMedications($studentId)
    {
        return Medication::where('student_id', $studentId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    /**
     * Get complete health profile for a student
     *
     * @param int $studentId
     * @return array
     */
    public function getHealthProfile($studentId)
    {
        return [
            'record' => $this->getHealthRecord($studentId),
            'visits' => $this->getMedicalVisits($studentId),
            'vaccinations' => $this->getVaccinations($studentId),
            'allergies' => $this->getAllergies($studentId),
            'medications' => $this->getMedications($studentId)
        ];
    }

    /**
     * Get students with medical conditions
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsWithMedicalConditions()
    {
        return HealthRecord::with('student')
            ->whereNotNull('medical_conditions')
            ->where('medical_conditions', '!=', '')
            ->get();
    }

    /**
     * Get students with allergies
     *
     * @param string|null $allergyName
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsWithAllergies($allergyName = null)
    {
        $query = Allergy::with('student');
        
        if ($allergyName) {
            $query->where('name', 'like', "%{$allergyName}%");
        }
        
        return $query->get();
    }

    /**
     * Get upcoming follow-ups
     *
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingFollowUps($days = 7)
    {
        $startDate = now();
        $endDate = now()->addDays($days);
        
        return MedicalVisit::with('student')
            ->whereNotNull('follow_up_date')
            ->whereBetween('follow_up_date', [$startDate, $endDate])
            ->orderBy('follow_up_date', 'asc')
            ->get();
    }
}
