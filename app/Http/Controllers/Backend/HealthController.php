<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HealthRecord;
use App\MedicalVisit;
use App\Vaccination;
use App\VaccinationRecord;
use App\Registration;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * HealthController
 * 
 * This controller handles the health records module functionality.
 * Enhanced by Zophlic for better health tracking capabilities.
 */
class HealthController extends Controller
{
    /**
     * Display a listing of health records.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = HealthRecord::with('student.student')
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return view('backend.health.index', compact('records'));
    }

    /**
     * Show the form for creating a new health record.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $students = Registration::with('student')
            ->where('status', AppHelper::ACTIVE)
            ->get()
            ->pluck('student.name', 'id');
            
        return view('backend.health.create', compact('students'));
    }

    /**
     * Store a newly created health record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|integer|exists:registrations,id',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'blood_group' => 'nullable|string|max:10',
            'blood_pressure' => 'nullable|string|max:20',
            'pulse_rate' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'vision_left' => 'nullable|string|max:10',
            'vision_right' => 'nullable|string|max:10',
            'hearing_left' => 'nullable|string|max:10',
            'hearing_right' => 'nullable|string|max:10',
            'immunizations' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string'
        ]);

        // Check if student already has a health record
        $existingRecord = HealthRecord::where('student_id', $request->student_id)->first();
        
        if ($existingRecord) {
            return redirect()->back()->with('error', 'Student already has a health record!')->withInput();
        }

        // Calculate BMI if height and weight are provided
        $bmi = null;
        if ($request->height && $request->weight) {
            $bmi = HealthRecord::calculateBMI($request->height, $request->weight);
        }

        // Create health record
        $record = HealthRecord::create([
            'student_id' => $request->student_id,
            'height' => $request->height,
            'weight' => $request->weight,
            'bmi' => $bmi,
            'blood_group' => $request->blood_group,
            'blood_pressure' => $request->blood_pressure,
            'pulse_rate' => $request->pulse_rate,
            'allergies' => $request->allergies,
            'medications' => $request->medications,
            'past_medical_history' => $request->past_medical_history,
            'vision_left' => $request->vision_left,
            'vision_right' => $request->vision_right,
            'hearing_left' => $request->hearing_left,
            'hearing_right' => $request->hearing_right,
            'immunizations' => $request->immunizations,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
            'notes' => $request->notes,
            'recorded_by' => Auth::id()
        ]);

        return redirect()->route('health.show', $record->id)->with('success', 'Health record created successfully!');
    }

    /**
     * Display the specified health record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $record = HealthRecord::with('student.student', 'recorder', 'visits.attendedBy')
            ->findOrFail($id);
            
        // Get vaccination records
        $vaccinationRecords = VaccinationRecord::with('vaccination')
            ->where('student_id', $record->student_id)
            ->orderBy('date_given', 'desc')
            ->get();
            
        return view('backend.health.show', compact('record', 'vaccinationRecords'));
    }

    /**
     * Show the form for editing the specified health record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $record = HealthRecord::findOrFail($id);
        
        return view('backend.health.edit', compact('record'));
    }

    /**
     * Update the specified health record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $record = HealthRecord::findOrFail($id);
        
        $this->validate($request, [
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'blood_group' => 'nullable|string|max:10',
            'blood_pressure' => 'nullable|string|max:20',
            'pulse_rate' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'past_medical_history' => 'nullable|string',
            'vision_left' => 'nullable|string|max:10',
            'vision_right' => 'nullable|string|max:10',
            'hearing_left' => 'nullable|string|max:10',
            'hearing_right' => 'nullable|string|max:10',
            'immunizations' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string'
        ]);

        // Calculate BMI if height and weight are provided
        $bmi = null;
        if ($request->height && $request->weight) {
            $bmi = HealthRecord::calculateBMI($request->height, $request->weight);
        }

        // Update health record
        $record->height = $request->height;
        $record->weight = $request->weight;
        $record->bmi = $bmi;
        $record->blood_group = $request->blood_group;
        $record->blood_pressure = $request->blood_pressure;
        $record->pulse_rate = $request->pulse_rate;
        $record->allergies = $request->allergies;
        $record->medications = $request->medications;
        $record->past_medical_history = $request->past_medical_history;
        $record->vision_left = $request->vision_left;
        $record->vision_right = $request->vision_right;
        $record->hearing_left = $request->hearing_left;
        $record->hearing_right = $request->hearing_right;
        $record->immunizations = $request->immunizations;
        $record->emergency_contact = $request->emergency_contact;
        $record->emergency_phone = $request->emergency_phone;
        $record->notes = $request->notes;
        $record->save();

        return redirect()->route('health.show', $record->id)->with('success', 'Health record updated successfully!');
    }

    /**
     * Show the form for creating a new medical visit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createVisit($id)
    {
        $record = HealthRecord::with('student.student')->findOrFail($id);
        
        return view('backend.health.visits.create', compact('record'));
    }

    /**
     * Store a newly created medical visit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeVisit(Request $request, $id)
    {
        $record = HealthRecord::findOrFail($id);
        
        $this->validate($request, [
            'visit_date' => 'required|date',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prescribed_medications' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:visit_date'
        ]);

        // Create medical visit
        $visit = MedicalVisit::create([
            'student_id' => $record->student_id,
            'health_record_id' => $record->id,
            'visit_date' => $request->visit_date,
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'prescribed_medications' => $request->prescribed_medications,
            'temperature' => $request->temperature,
            'notes' => $request->notes,
            'follow_up_date' => $request->follow_up_date,
            'attended_by' => Auth::id()
        ]);

        return redirect()->route('health.show', $record->id)->with('success', 'Medical visit recorded successfully!');
    }

    /**
     * Show the form for editing the specified medical visit.
     *
     * @param  int  $id
     * @param  int  $visitId
     * @return \Illuminate\Http\Response
     */
    public function editVisit($id, $visitId)
    {
        $record = HealthRecord::findOrFail($id);
        $visit = MedicalVisit::findOrFail($visitId);
        
        return view('backend.health.visits.edit', compact('record', 'visit'));
    }

    /**
     * Update the specified medical visit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $visitId
     * @return \Illuminate\Http\Response
     */
    public function updateVisit(Request $request, $id, $visitId)
    {
        $record = HealthRecord::findOrFail($id);
        $visit = MedicalVisit::findOrFail($visitId);
        
        $this->validate($request, [
            'visit_date' => 'required|date',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prescribed_medications' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:visit_date'
        ]);

        // Update medical visit
        $visit->visit_date = $request->visit_date;
        $visit->symptoms = $request->symptoms;
        $visit->diagnosis = $request->diagnosis;
        $visit->treatment = $request->treatment;
        $visit->prescribed_medications = $request->prescribed_medications;
        $visit->temperature = $request->temperature;
        $visit->notes = $request->notes;
        $visit->follow_up_date = $request->follow_up_date;
        $visit->save();

        return redirect()->route('health.show', $record->id)->with('success', 'Medical visit updated successfully!');
    }

    /**
     * Display a listing of vaccinations.
     *
     * @return \Illuminate\Http\Response
     */
    public function vaccinations()
    {
        $vaccinations = Vaccination::orderBy('name', 'asc')->paginate(10);
        
        return view('backend.health.vaccinations.index', compact('vaccinations'));
    }

    /**
     * Show the form for creating a new vaccination.
     *
     * @return \Illuminate\Http\Response
     */
    public function createVaccination()
    {
        return view('backend.health.vaccinations.create');
    }

    /**
     * Store a newly created vaccination in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeVaccination(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:vaccinations',
            'description' => 'nullable|string',
            'recommended_age' => 'nullable|string|max:50',
            'doses' => 'required|integer|min:1',
            'status' => 'required|integer'
        ]);

        // Create vaccination
        $vaccination = Vaccination::create([
            'name' => $request->name,
            'description' => $request->description,
            'recommended_age' => $request->recommended_age,
            'doses' => $request->doses,
            'status' => $request->status
        ]);

        return redirect()->route('health.vaccinations')->with('success', 'Vaccination created successfully!');
    }

    /**
     * Show the form for editing the specified vaccination.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editVaccination($id)
    {
        $vaccination = Vaccination::findOrFail($id);
        
        return view('backend.health.vaccinations.edit', compact('vaccination'));
    }

    /**
     * Update the specified vaccination in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateVaccination(Request $request, $id)
    {
        $vaccination = Vaccination::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:vaccinations,name,'.$id,
            'description' => 'nullable|string',
            'recommended_age' => 'nullable|string|max:50',
            'doses' => 'required|integer|min:1',
            'status' => 'required|integer'
        ]);

        // Update vaccination
        $vaccination->name = $request->name;
        $vaccination->description = $request->description;
        $vaccination->recommended_age = $request->recommended_age;
        $vaccination->doses = $request->doses;
        $vaccination->status = $request->status;
        $vaccination->save();

        return redirect()->route('health.vaccinations')->with('success', 'Vaccination updated successfully!');
    }

    /**
     * Show the form for recording a vaccination.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function recordVaccination($id)
    {
        $record = HealthRecord::with('student.student')->findOrFail($id);
        $vaccinations = Vaccination::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.health.vaccinations.record', compact('record', 'vaccinations'));
    }

    /**
     * Store a newly created vaccination record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeVaccinationRecord(Request $request, $id)
    {
        $record = HealthRecord::findOrFail($id);
        
        $this->validate($request, [
            'vaccination_id' => 'required|integer|exists:vaccinations,id',
            'dose_number' => 'required|integer|min:1',
            'date_given' => 'required|date',
            'next_due_date' => 'nullable|date|after:date_given',
            'administered_by' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        // Get vaccination
        $vaccination = Vaccination::findOrFail($request->vaccination_id);
        
        // Check if dose number is valid
        if ($request->dose_number > $vaccination->doses) {
            return redirect()->back()->with('error', 'Dose number cannot be greater than the total doses for this vaccination!')->withInput();
        }

        // Check if this dose has already been recorded
        $existingRecord = VaccinationRecord::where('student_id', $record->student_id)
            ->where('vaccination_id', $request->vaccination_id)
            ->where('dose_number', $request->dose_number)
            ->first();
            
        if ($existingRecord) {
            return redirect()->back()->with('error', 'This dose has already been recorded for this student!')->withInput();
        }

        // Create vaccination record
        $vaccinationRecord = VaccinationRecord::create([
            'student_id' => $record->student_id,
            'vaccination_id' => $request->vaccination_id,
            'dose_number' => $request->dose_number,
            'date_given' => $request->date_given,
            'next_due_date' => $request->next_due_date,
            'administered_by' => $request->administered_by,
            'notes' => $request->notes,
            'recorded_by' => Auth::id()
        ]);

        return redirect()->route('health.show', $record->id)->with('success', 'Vaccination record added successfully!');
    }

    /**
     * Display the health reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function reports()
    {
        $vaccinations = Vaccination::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.health.reports', compact('vaccinations'));
    }

    /**
     * Generate health report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        $this->validate($request, [
            'report_type' => 'required|string|in:bmi,vaccination_status,medical_visits',
            'vaccination_id' => 'nullable|integer|exists:vaccinations,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'output_format' => 'required|string|in:html,pdf,excel'
        ]);
        
        if ($request->report_type == 'bmi') {
            // Get all students with health records
            $records = HealthRecord::with('student.student')
                ->whereNotNull('bmi')
                ->get();
                
            // Group by BMI category
            $underweight = $records->filter(function ($record) {
                return $record->bmi < 18.5;
            });
            
            $normal = $records->filter(function ($record) {
                return $record->bmi >= 18.5 && $record->bmi < 25;
            });
            
            $overweight = $records->filter(function ($record) {
                return $record->bmi >= 25 && $record->bmi < 30;
            });
            
            $obese = $records->filter(function ($record) {
                return $record->bmi >= 30;
            });
            
            if ($request->output_format == 'pdf') {
                $pdf = \PDF::loadView('backend.health.reports.bmi_pdf', compact('records', 'underweight', 'normal', 'overweight', 'obese'));
                return $pdf->download('bmi_report.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.health.reports.bmi', compact('records', 'underweight', 'normal', 'overweight', 'obese'));
            }
        } elseif ($request->report_type == 'vaccination_status') {
            // Get vaccination
            $vaccination = Vaccination::findOrFail($request->vaccination_id);
            
            // Get all students with this vaccination
            $records = VaccinationRecord::with('student.student')
                ->where('vaccination_id', $vaccination->id)
                ->get();
                
            // Group by dose
            $doseGroups = [];
            
            for ($i = 1; $i <= $vaccination->doses; $i++) {
                $doseGroups[$i] = $records->filter(function ($record) use ($i) {
                    return $record->dose_number == $i;
                });
            }
            
            if ($request->output_format == 'pdf') {
                $pdf = \PDF::loadView('backend.health.reports.vaccination_pdf', compact('vaccination', 'records', 'doseGroups'));
                return $pdf->download('vaccination_report_' . $vaccination->name . '.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.health.reports.vaccination', compact('vaccination', 'records', 'doseGroups'));
            }
        } elseif ($request->report_type == 'medical_visits') {
            // Get date range
            $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subMonth();
            $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();
            
            // Get all medical visits in date range
            $visits = MedicalVisit::with('student.student', 'attendedBy')
                ->whereBetween('visit_date', [$dateFrom, $dateTo])
                ->orderBy('visit_date', 'desc')
                ->get();
                
            if ($request->output_format == 'pdf') {
                $pdf = \PDF::loadView('backend.health.reports.visits_pdf', compact('visits', 'dateFrom', 'dateTo'));
                return $pdf->download('medical_visits_report.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.health.reports.visits', compact('visits', 'dateFrom', 'dateTo'));
            }
        }
        
        return redirect()->back()->with('error', 'Invalid report type!');
    }
}
