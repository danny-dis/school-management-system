<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Timetable;
use App\TimetableSlot;
use App\Room;
use App\IClass;
use App\Section;
use App\Subject;
use App\Employee;
use App\AcademicYear;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

/**
 * TimetableController
 * 
 * This controller handles the timetable and scheduling module functionality.
 * Enhanced by Zophlic for better scheduling capabilities.
 */
class TimetableController extends Controller
{
    /**
     * Display a listing of timetables.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timetables = Timetable::with('class', 'section', 'academicYear')
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return view('backend.timetable.index', compact('timetables'));
    }

    /**
     * Show the form for creating a new timetable.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $academicYears = AcademicYear::where('status', '1')->orderBy('id', 'desc')->pluck('title', 'id');
        
        return view('backend.timetable.create', compact('classes', 'academicYears'));
    }

    /**
     * Store a newly created timetable in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Create timetable
        $timetable = Timetable::create([
            'name' => $request->name,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'academic_year_id' => $request->academic_year_id,
            'description' => $request->description,
            'status' => $request->status,
            'created_by' => Auth::id()
        ]);

        return redirect()->route('timetable.slots', $timetable->id)->with('success', 'Timetable created successfully! Now add slots to it.');
    }

    /**
     * Display the specified timetable.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $timetable = Timetable::with('class', 'section', 'academicYear', 'slots.subject', 'slots.teacher', 'slots.room')
            ->findOrFail($id);
            
        // Group slots by day
        $days = [0, 1, 2, 3, 4, 5, 6];
        $slots = [];
        
        foreach ($days as $day) {
            $slots[$day] = $timetable->slots->where('day', $day)->sortBy('start_time');
        }
        
        return view('backend.timetable.show', compact('timetable', 'slots', 'days'));
    }

    /**
     * Show the form for editing the specified timetable.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $timetable = Timetable::findOrFail($id);
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $sections = Section::where('class_id', $timetable->class_id)->pluck('name', 'id');
        $academicYears = AcademicYear::where('status', '1')->orderBy('id', 'desc')->pluck('title', 'id');
        
        return view('backend.timetable.edit', compact('timetable', 'classes', 'sections', 'academicYears'));
    }

    /**
     * Update the specified timetable in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $timetable = Timetable::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Update timetable
        $timetable->name = $request->name;
        $timetable->class_id = $request->class_id;
        $timetable->section_id = $request->section_id;
        $timetable->academic_year_id = $request->academic_year_id;
        $timetable->description = $request->description;
        $timetable->status = $request->status;
        $timetable->save();

        return redirect()->route('timetable.index')->with('success', 'Timetable updated successfully!');
    }

    /**
     * Remove the specified timetable from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $timetable = Timetable::findOrFail($id);
        $timetable->delete();
        
        return redirect()->route('timetable.index')->with('success', 'Timetable deleted successfully!');
    }

    /**
     * Show the form for managing timetable slots.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function slots($id)
    {
        $timetable = Timetable::with('class', 'section', 'academicYear', 'slots.subject', 'slots.teacher', 'slots.room')
            ->findOrFail($id);
            
        $subjects = Subject::where('class_id', $timetable->class_id)->pluck('name', 'id');
        $teachers = Employee::where('role_id', AppHelper::EMP_TEACHER)->pluck('name', 'id');
        $rooms = Room::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        // Group slots by day
        $days = [0, 1, 2, 3, 4, 5, 6];
        $slots = [];
        
        foreach ($days as $day) {
            $slots[$day] = $timetable->slots->where('day', $day)->sortBy('start_time');
        }
        
        return view('backend.timetable.slots', compact('timetable', 'slots', 'days', 'subjects', 'teachers', 'rooms'));
    }

    /**
     * Store a newly created timetable slot in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeSlot(Request $request, $id)
    {
        $timetable = Timetable::findOrFail($id);
        
        $this->validate($request, [
            'day' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'subject_id' => 'required|integer|exists:subjects,id',
            'teacher_id' => 'required|integer|exists:employees,id',
            'room_id' => 'nullable|integer|exists:rooms,id'
        ]);

        // Check for conflicts
        $conflicts = $this->checkSlotConflicts(
            $timetable->id,
            $request->day,
            $request->start_time,
            $request->end_time,
            $request->teacher_id,
            $request->room_id
        );
        
        if ($conflicts) {
            return redirect()->back()->with('error', 'There is a conflict with another slot: ' . $conflicts)->withInput();
        }

        // Create slot
        $slot = TimetableSlot::create([
            'timetable_id' => $timetable->id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'room_id' => $request->room_id
        ]);

        return redirect()->route('timetable.slots', $timetable->id)->with('success', 'Slot added successfully!');
    }

    /**
     * Check for conflicts with existing slots.
     *
     * @param  int  $timetableId
     * @param  int  $day
     * @param  string  $startTime
     * @param  string  $endTime
     * @param  int  $teacherId
     * @param  int|null  $roomId
     * @param  int|null  $slotId
     * @return string|null
     */
    private function checkSlotConflicts($timetableId, $day, $startTime, $endTime, $teacherId, $roomId = null, $slotId = null)
    {
        // Get timetable class and section
        $timetable = Timetable::with('class', 'section')->findOrFail($timetableId);
        
        // Check for teacher conflicts
        $teacherConflicts = TimetableSlot::whereHas('timetable', function ($query) use ($timetable) {
                $query->where('academic_year_id', $timetable->academic_year_id);
            })
            ->where('day', $day)
            ->where('teacher_id', $teacherId)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });
            
        if ($slotId) {
            $teacherConflicts->where('id', '!=', $slotId);
        }
        
        $teacherConflict = $teacherConflicts->first();
        
        if ($teacherConflict) {
            $conflictTimetable = $teacherConflict->timetable;
            return "Teacher is already scheduled for " . $teacherConflict->subject->name . " in " . 
                   $conflictTimetable->class->name . " " . $conflictTimetable->section->name . 
                   " at " . $teacherConflict->time_range;
        }
        
        // Check for room conflicts if room is specified
        if ($roomId) {
            $roomConflicts = TimetableSlot::whereHas('timetable', function ($query) use ($timetable) {
                    $query->where('academic_year_id', $timetable->academic_year_id);
                })
                ->where('day', $day)
                ->where('room_id', $roomId)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime])
                        ->orWhere(function ($q) use ($startTime, $endTime) {
                            $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                        });
                });
                
            if ($slotId) {
                $roomConflicts->where('id', '!=', $slotId);
            }
            
            $roomConflict = $roomConflicts->first();
            
            if ($roomConflict) {
                $conflictTimetable = $roomConflict->timetable;
                return "Room is already scheduled for " . $roomConflict->subject->name . " in " . 
                       $conflictTimetable->class->name . " " . $conflictTimetable->section->name . 
                       " at " . $roomConflict->time_range;
            }
        }
        
        // Check for class/section conflicts
        $classConflicts = TimetableSlot::where('timetable_id', $timetableId)
            ->where('day', $day)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });
            
        if ($slotId) {
            $classConflicts->where('id', '!=', $slotId);
        }
        
        $classConflict = $classConflicts->first();
        
        if ($classConflict) {
            return "Class is already scheduled for " . $classConflict->subject->name . " at " . $classConflict->time_range;
        }
        
        return null;
    }

    /**
     * Show the form for editing the specified timetable slot.
     *
     * @param  int  $id
     * @param  int  $slotId
     * @return \Illuminate\Http\Response
     */
    public function editSlot($id, $slotId)
    {
        $timetable = Timetable::findOrFail($id);
        $slot = TimetableSlot::findOrFail($slotId);
        
        $subjects = Subject::where('class_id', $timetable->class_id)->pluck('name', 'id');
        $teachers = Employee::where('role_id', AppHelper::EMP_TEACHER)->pluck('name', 'id');
        $rooms = Room::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.timetable.edit_slot', compact('timetable', 'slot', 'subjects', 'teachers', 'rooms'));
    }

    /**
     * Update the specified timetable slot in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $slotId
     * @return \Illuminate\Http\Response
     */
    public function updateSlot(Request $request, $id, $slotId)
    {
        $timetable = Timetable::findOrFail($id);
        $slot = TimetableSlot::findOrFail($slotId);
        
        $this->validate($request, [
            'day' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'subject_id' => 'required|integer|exists:subjects,id',
            'teacher_id' => 'required|integer|exists:employees,id',
            'room_id' => 'nullable|integer|exists:rooms,id'
        ]);

        // Check for conflicts
        $conflicts = $this->checkSlotConflicts(
            $timetable->id,
            $request->day,
            $request->start_time,
            $request->end_time,
            $request->teacher_id,
            $request->room_id,
            $slotId
        );
        
        if ($conflicts) {
            return redirect()->back()->with('error', 'There is a conflict with another slot: ' . $conflicts)->withInput();
        }

        // Update slot
        $slot->day = $request->day;
        $slot->start_time = $request->start_time;
        $slot->end_time = $request->end_time;
        $slot->subject_id = $request->subject_id;
        $slot->teacher_id = $request->teacher_id;
        $slot->room_id = $request->room_id;
        $slot->save();

        return redirect()->route('timetable.slots', $timetable->id)->with('success', 'Slot updated successfully!');
    }

    /**
     * Remove the specified timetable slot from storage.
     *
     * @param  int  $id
     * @param  int  $slotId
     * @return \Illuminate\Http\Response
     */
    public function destroySlot($id, $slotId)
    {
        $timetable = Timetable::findOrFail($id);
        $slot = TimetableSlot::findOrFail($slotId);
        $slot->delete();
        
        return redirect()->route('timetable.slots', $timetable->id)->with('success', 'Slot deleted successfully!');
    }

    /**
     * Display a listing of rooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function rooms()
    {
        $rooms = Room::orderBy('name', 'asc')->paginate(10);
        
        return view('backend.timetable.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRoom()
    {
        return view('backend.timetable.rooms.create');
    }

    /**
     * Store a newly created room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRoom(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'room_no' => 'required|string|max:20|unique:rooms',
            'capacity' => 'nullable|integer|min:1',
            'type' => 'required|string|in:classroom,lab,library,computer_lab,science_lab,conference,other',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Create room
        $room = Room::create([
            'name' => $request->name,
            'room_no' => $request->room_no,
            'capacity' => $request->capacity,
            'type' => $request->type,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('timetable.rooms')->with('success', 'Room created successfully!');
    }

    /**
     * Show the form for editing the specified room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editRoom($id)
    {
        $room = Room::findOrFail($id);
        
        return view('backend.timetable.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRoom(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'room_no' => 'required|string|max:20|unique:rooms,room_no,'.$id,
            'capacity' => 'nullable|integer|min:1',
            'type' => 'required|string|in:classroom,lab,library,computer_lab,science_lab,conference,other',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Update room
        $room->name = $request->name;
        $room->room_no = $request->room_no;
        $room->capacity = $request->capacity;
        $room->type = $request->type;
        $room->description = $request->description;
        $room->status = $request->status;
        $room->save();

        return redirect()->route('timetable.rooms')->with('success', 'Room updated successfully!');
    }

    /**
     * Remove the specified room from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyRoom($id)
    {
        $room = Room::findOrFail($id);
        
        // Check if room is used in any timetable
        $usedInTimetable = TimetableSlot::where('room_id', $id)->exists();
        
        if ($usedInTimetable) {
            return redirect()->route('timetable.rooms')->with('error', 'Cannot delete room as it is used in timetables!');
        }
        
        $room->delete();
        
        return redirect()->route('timetable.rooms')->with('success', 'Room deleted successfully!');
    }

    /**
     * Print the specified timetable.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        $timetable = Timetable::with('class', 'section', 'academicYear', 'slots.subject', 'slots.teacher', 'slots.room')
            ->findOrFail($id);
            
        // Group slots by day
        $days = [0, 1, 2, 3, 4, 5, 6];
        $slots = [];
        
        foreach ($days as $day) {
            $slots[$day] = $timetable->slots->where('day', $day)->sortBy('start_time');
        }
        
        $pdf = PDF::loadView('backend.timetable.print', compact('timetable', 'slots', 'days'));
        
        return $pdf->download('timetable_' . $timetable->name . '.pdf');
    }

    /**
     * Get teacher timetable.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function teacherTimetable(Request $request)
    {
        $teachers = Employee::where('role_id', AppHelper::EMP_TEACHER)->pluck('name', 'id');
        $academicYears = AcademicYear::where('status', '1')->orderBy('id', 'desc')->pluck('title', 'id');
        
        $teacherId = $request->teacher_id;
        $academicYearId = $request->academic_year_id;
        
        $slots = null;
        
        if ($teacherId && $academicYearId) {
            $teacher = Employee::findOrFail($teacherId);
            $academicYear = AcademicYear::findOrFail($academicYearId);
            
            $slots = TimetableSlot::with('timetable.class', 'timetable.section', 'subject', 'room')
                ->whereHas('timetable', function ($query) use ($academicYearId) {
                    $query->where('academic_year_id', $academicYearId)
                        ->where('status', AppHelper::ACTIVE);
                })
                ->where('teacher_id', $teacherId)
                ->get();
                
            // Group slots by day
            $days = [0, 1, 2, 3, 4, 5, 6];
            $groupedSlots = [];
            
            foreach ($days as $day) {
                $groupedSlots[$day] = $slots->where('day', $day)->sortBy('start_time');
            }
            
            $slots = $groupedSlots;
        }
        
        return view('backend.timetable.teacher_timetable', compact('teachers', 'academicYears', 'teacherId', 'academicYearId', 'slots'));
    }
}
