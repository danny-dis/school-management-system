<?php

namespace App\Services;

use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\TimetablePeriod;
use App\Models\TimetableDay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TimetableService
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * TimetableService constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a timetable
     *
     * @param array $data
     * @param int $createdBy
     * @return Timetable|null
     */
    public function createTimetable(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $timetable = Timetable::create([
                'name' => $data['name'],
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'academic_year_id' => $data['academic_year_id'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? Timetable::STATUS_ACTIVE,
                'created_by' => $createdBy
            ]);
            
            // Create days and periods if provided
            if (isset($data['days']) && is_array($data['days'])) {
                foreach ($data['days'] as $day) {
                    TimetableDay::create([
                        'timetable_id' => $timetable->id,
                        'day' => $day,
                        'status' => true
                    ]);
                }
            }
            
            if (isset($data['periods']) && is_array($data['periods'])) {
                foreach ($data['periods'] as $period) {
                    TimetablePeriod::create([
                        'timetable_id' => $timetable->id,
                        'name' => $period['name'],
                        'start_time' => $period['start_time'],
                        'end_time' => $period['end_time'],
                        'is_break' => $period['is_break'] ?? false,
                        'status' => true
                    ]);
                }
            }
            
            DB::commit();
            return $timetable;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating timetable: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a timetable
     *
     * @param int $id
     * @param array $data
     * @return Timetable|null
     */
    public function updateTimetable($id, array $data)
    {
        try {
            $timetable = Timetable::find($id);
            
            if (!$timetable) {
                return null;
            }
            
            $timetable->update($data);
            return $timetable;
        } catch (Exception $e) {
            Log::error('Error updating timetable: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a timetable
     *
     * @param int $id
     * @return bool
     */
    public function deleteTimetable($id)
    {
        try {
            DB::beginTransaction();
            
            $timetable = Timetable::find($id);
            
            if (!$timetable) {
                return false;
            }
            
            // Delete slots
            $timetable->slots()->delete();
            
            // Delete periods
            $timetable->periods()->delete();
            
            // Delete days
            $timetable->days()->delete();
            
            // Delete timetable
            $timetable->delete();
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting timetable: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a timetable day
     *
     * @param array $data
     * @return TimetableDay|null
     */
    public function createDay(array $data)
    {
        try {
            return TimetableDay::create([
                'timetable_id' => $data['timetable_id'],
                'day' => $data['day'],
                'status' => $data['status'] ?? true
            ]);
        } catch (Exception $e) {
            Log::error('Error creating timetable day: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a timetable day
     *
     * @param int $id
     * @param array $data
     * @return TimetableDay|null
     */
    public function updateDay($id, array $data)
    {
        try {
            $day = TimetableDay::find($id);
            
            if (!$day) {
                return null;
            }
            
            $day->update($data);
            return $day;
        } catch (Exception $e) {
            Log::error('Error updating timetable day: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a timetable day
     *
     * @param int $id
     * @return bool
     */
    public function deleteDay($id)
    {
        try {
            DB::beginTransaction();
            
            $day = TimetableDay::find($id);
            
            if (!$day) {
                return false;
            }
            
            // Delete slots for this day
            TimetableSlot::where('day_id', $id)->delete();
            
            // Delete day
            $day->delete();
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting timetable day: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a timetable period
     *
     * @param array $data
     * @return TimetablePeriod|null
     */
    public function createPeriod(array $data)
    {
        try {
            return TimetablePeriod::create([
                'timetable_id' => $data['timetable_id'],
                'name' => $data['name'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'is_break' => $data['is_break'] ?? false,
                'status' => $data['status'] ?? true
            ]);
        } catch (Exception $e) {
            Log::error('Error creating timetable period: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a timetable period
     *
     * @param int $id
     * @param array $data
     * @return TimetablePeriod|null
     */
    public function updatePeriod($id, array $data)
    {
        try {
            $period = TimetablePeriod::find($id);
            
            if (!$period) {
                return null;
            }
            
            $period->update($data);
            return $period;
        } catch (Exception $e) {
            Log::error('Error updating timetable period: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a timetable period
     *
     * @param int $id
     * @return bool
     */
    public function deletePeriod($id)
    {
        try {
            DB::beginTransaction();
            
            $period = TimetablePeriod::find($id);
            
            if (!$period) {
                return false;
            }
            
            // Delete slots for this period
            TimetableSlot::where('period_id', $id)->delete();
            
            // Delete period
            $period->delete();
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting timetable period: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a timetable slot
     *
     * @param array $data
     * @return TimetableSlot|null
     */
    public function createSlot(array $data)
    {
        try {
            DB::beginTransaction();
            
            // Check if slot already exists
            $existingSlot = TimetableSlot::where('timetable_id', $data['timetable_id'])
                ->where('day_id', $data['day_id'])
                ->where('period_id', $data['period_id'])
                ->first();
            
            if ($existingSlot) {
                // Update existing slot
                $existingSlot->update([
                    'subject_id' => $data['subject_id'],
                    'teacher_id' => $data['teacher_id'],
                    'room_id' => $data['room_id'] ?? null,
                    'note' => $data['note'] ?? null
                ]);
                
                $slot = $existingSlot;
            } else {
                // Create new slot
                $slot = TimetableSlot::create([
                    'timetable_id' => $data['timetable_id'],
                    'day_id' => $data['day_id'],
                    'period_id' => $data['period_id'],
                    'subject_id' => $data['subject_id'],
                    'teacher_id' => $data['teacher_id'],
                    'room_id' => $data['room_id'] ?? null,
                    'note' => $data['note'] ?? null
                ]);
            }
            
            DB::commit();
            return $slot;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating timetable slot: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a timetable slot
     *
     * @param int $id
     * @param array $data
     * @return TimetableSlot|null
     */
    public function updateSlot($id, array $data)
    {
        try {
            $slot = TimetableSlot::find($id);
            
            if (!$slot) {
                return null;
            }
            
            $slot->update($data);
            return $slot;
        } catch (Exception $e) {
            Log::error('Error updating timetable slot: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a timetable slot
     *
     * @param int $id
     * @return bool
     */
    public function deleteSlot($id)
    {
        try {
            $slot = TimetableSlot::find($id);
            
            if (!$slot) {
                return false;
            }
            
            $slot->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting timetable slot: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get timetables
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTimetables(array $filters = [])
    {
        $query = Timetable::with(['class', 'section', 'academicYear']);
        
        if (isset($filters['class_id']) && $filters['class_id']) {
            $query->where('class_id', $filters['class_id']);
        }
        
        if (isset($filters['section_id']) && $filters['section_id']) {
            $query->where('section_id', $filters['section_id']);
        }
        
        if (isset($filters['academic_year_id']) && $filters['academic_year_id']) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get timetable details
     *
     * @param int $id
     * @return Timetable|null
     */
    public function getTimetableDetails($id)
    {
        return Timetable::with([
            'days',
            'periods' => function ($query) {
                $query->orderBy('start_time', 'asc');
            },
            'slots.subject',
            'slots.teacher',
            'slots.room',
            'slots.day',
            'slots.period'
        ])->find($id);
    }

    /**
     * Get timetable for a class
     *
     * @param int $classId
     * @param int $sectionId
     * @param int $academicYearId
     * @return Timetable|null
     */
    public function getTimetableForClass($classId, $sectionId, $academicYearId)
    {
        return Timetable::with([
            'days',
            'periods' => function ($query) {
                $query->orderBy('start_time', 'asc');
            },
            'slots.subject',
            'slots.teacher',
            'slots.room',
            'slots.day',
            'slots.period'
        ])
        ->where('class_id', $classId)
        ->where('section_id', $sectionId)
        ->where('academic_year_id', $academicYearId)
        ->where('status', Timetable::STATUS_ACTIVE)
        ->first();
    }

    /**
     * Get timetable for a teacher
     *
     * @param int $teacherId
     * @param int $academicYearId
     * @return array
     */
    public function getTimetableForTeacher($teacherId, $academicYearId)
    {
        $slots = TimetableSlot::with([
            'timetable',
            'day',
            'period',
            'subject',
            'room'
        ])
        ->whereHas('timetable', function ($query) use ($academicYearId) {
            $query->where('academic_year_id', $academicYearId)
                ->where('status', Timetable::STATUS_ACTIVE);
        })
        ->where('teacher_id', $teacherId)
        ->get();
        
        $result = [];
        
        foreach ($slots as $slot) {
            $dayId = $slot->day_id;
            $periodId = $slot->period_id;
            
            if (!isset($result[$dayId])) {
                $result[$dayId] = [
                    'day' => $slot->day->day,
                    'periods' => []
                ];
            }
            
            $result[$dayId]['periods'][$periodId] = [
                'period' => $slot->period->name,
                'start_time' => $slot->period->start_time,
                'end_time' => $slot->period->end_time,
                'subject' => $slot->subject->name,
                'class' => $slot->timetable->class->name,
                'section' => $slot->timetable->section->name,
                'room' => $slot->room ? $slot->room->name : null,
                'note' => $slot->note
            ];
        }
        
        return $result;
    }

    /**
     * Publish timetable
     *
     * @param int $id
     * @return Timetable|null
     */
    public function publishTimetable($id)
    {
        try {
            DB::beginTransaction();
            
            $timetable = Timetable::find($id);
            
            if (!$timetable) {
                return null;
            }
            
            // Check if timetable has slots
            $slotCount = TimetableSlot::where('timetable_id', $id)->count();
            
            if ($slotCount == 0) {
                throw new Exception('Cannot publish empty timetable');
            }
            
            // Update status
            $timetable->status = Timetable::STATUS_ACTIVE;
            $timetable->save();
            
            // Notify students and teachers
            $this->notifyAboutTimetable($timetable);
            
            DB::commit();
            return $timetable;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error publishing timetable: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Notify about timetable
     *
     * @param Timetable $timetable
     * @return void
     */
    protected function notifyAboutTimetable(Timetable $timetable)
    {
        // Notify students
        $students = \App\Models\Student::whereHas('registration', function ($query) use ($timetable) {
            $query->where('class_id', $timetable->class_id)
                ->where('section_id', $timetable->section_id)
                ->where('academic_year_id', $timetable->academic_year_id);
        })->get();
        
        foreach ($students as $student) {
            if ($student->user_id) {
                $this->notificationService->createNotification(
                    $student->user_id,
                    'Timetable Published',
                    'Your class timetable has been published',
                    route('timetable.student.view'),
                    'info'
                );
            }
        }
        
        // Notify teachers
        $teacherIds = TimetableSlot::where('timetable_id', $timetable->id)
            ->distinct()
            ->pluck('teacher_id')
            ->toArray();
        
        $teachers = \App\Models\Employee::whereIn('id', $teacherIds)->get();
        
        foreach ($teachers as $teacher) {
            if ($teacher->user_id) {
                $this->notificationService->createNotification(
                    $teacher->user_id,
                    'Timetable Published',
                    'A new timetable has been published for ' . $timetable->class->name . ' - ' . $timetable->section->name,
                    route('timetable.teacher.view'),
                    'info'
                );
            }
        }
    }
}
