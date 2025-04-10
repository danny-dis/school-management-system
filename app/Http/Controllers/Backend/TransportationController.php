<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Vehicle;
use App\TransportRoute;
use App\TransportStop;
use App\TransportStudent;
use App\Registration;
use App\Employee;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * TransportationController
 * 
 * This controller handles the transportation module functionality.
 * Enhanced by Zophlic for better fleet management.
 */
class TransportationController extends Controller
{
    /**
     * Display a listing of vehicles.
     *
     * @return \Illuminate\Http\Response
     */
    public function vehicles()
    {
        $vehicles = Vehicle::with('driver')->orderBy('id', 'desc')->paginate(10);
        return view('backend.transportation.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     *
     * @return \Illuminate\Http\Response
     */
    public function createVehicle()
    {
        $drivers = Employee::where('role_id', AppHelper::EMP_DRIVER)->pluck('name', 'id');
        
        return view('backend.transportation.vehicles.create', compact('drivers'));
    }

    /**
     * Store a newly created vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeVehicle(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'registration_no' => 'required|string|max:50|unique:vehicles',
            'type' => 'required|string|in:bus,minibus,van,car,other',
            'capacity' => 'nullable|integer|min:1',
            'driver_id' => 'nullable|integer|exists:employees,id',
            'contact_no' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Create vehicle
        $vehicle = Vehicle::create([
            'name' => $request->name,
            'registration_no' => $request->registration_no,
            'type' => $request->type,
            'capacity' => $request->capacity,
            'driver_id' => $request->driver_id,
            'contact_no' => $request->contact_no,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('transportation.vehicles')->with('success', 'Vehicle created successfully!');
    }

    /**
     * Display the specified vehicle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showVehicle($id)
    {
        $vehicle = Vehicle::with('driver', 'routes')->findOrFail($id);
        
        return view('backend.transportation.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editVehicle($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $drivers = Employee::where('role_id', AppHelper::EMP_DRIVER)->pluck('name', 'id');
        
        return view('backend.transportation.vehicles.edit', compact('vehicle', 'drivers'));
    }

    /**
     * Update the specified vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateVehicle(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'registration_no' => 'required|string|max:50|unique:vehicles,registration_no,'.$id,
            'type' => 'required|string|in:bus,minibus,van,car,other',
            'capacity' => 'nullable|integer|min:1',
            'driver_id' => 'nullable|integer|exists:employees,id',
            'contact_no' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Update vehicle
        $vehicle->name = $request->name;
        $vehicle->registration_no = $request->registration_no;
        $vehicle->type = $request->type;
        $vehicle->capacity = $request->capacity;
        $vehicle->driver_id = $request->driver_id;
        $vehicle->contact_no = $request->contact_no;
        $vehicle->description = $request->description;
        $vehicle->status = $request->status;
        $vehicle->save();

        return redirect()->route('transportation.vehicles')->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Remove the specified vehicle from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyVehicle($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // Check if vehicle has routes
        if ($vehicle->routes()->count() > 0) {
            return redirect()->route('transportation.vehicles')->with('error', 'Cannot delete vehicle with existing routes!');
        }
        
        $vehicle->delete();
        
        return redirect()->route('transportation.vehicles')->with('success', 'Vehicle deleted successfully!');
    }

    /**
     * Display a listing of routes.
     *
     * @return \Illuminate\Http\Response
     */
    public function routes()
    {
        $routes = TransportRoute::with('vehicle')->orderBy('id', 'desc')->paginate(10);
        return view('backend.transportation.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new route.
     *
     * @return \Illuminate\Http\Response
     */
    public function createRoute()
    {
        $vehicles = Vehicle::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.transportation.routes.create', compact('vehicles'));
    }

    /**
     * Store a newly created route in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRoute(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'start_place' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_place' => 'required|string|max:100',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'distance' => 'nullable|numeric|min:0',
            'fare' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Create route
        $route = TransportRoute::create([
            'name' => $request->name,
            'vehicle_id' => $request->vehicle_id,
            'start_place' => $request->start_place,
            'start_time' => $request->start_time,
            'end_place' => $request->end_place,
            'end_time' => $request->end_time,
            'distance' => $request->distance,
            'fare' => $request->fare,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('transportation.routes.stops', $route->id)->with('success', 'Route created successfully! Now add stops to it.');
    }

    /**
     * Display the specified route.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRoute($id)
    {
        $route = TransportRoute::with('vehicle', 'stops', 'students.student.student')->findOrFail($id);
        
        // Sort stops by order
        $stops = $route->stops->sortBy('stop_order');
        
        return view('backend.transportation.routes.show', compact('route', 'stops'));
    }

    /**
     * Show the form for editing the specified route.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editRoute($id)
    {
        $route = TransportRoute::findOrFail($id);
        $vehicles = Vehicle::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.transportation.routes.edit', compact('route', 'vehicles'));
    }

    /**
     * Update the specified route in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRoute(Request $request, $id)
    {
        $route = TransportRoute::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'start_place' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_place' => 'required|string|max:100',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'distance' => 'nullable|numeric|min:0',
            'fare' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Update route
        $route->name = $request->name;
        $route->vehicle_id = $request->vehicle_id;
        $route->start_place = $request->start_place;
        $route->start_time = $request->start_time;
        $route->end_place = $request->end_place;
        $route->end_time = $request->end_time;
        $route->distance = $request->distance;
        $route->fare = $request->fare;
        $route->description = $request->description;
        $route->status = $request->status;
        $route->save();

        return redirect()->route('transportation.routes')->with('success', 'Route updated successfully!');
    }

    /**
     * Remove the specified route from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyRoute($id)
    {
        $route = TransportRoute::findOrFail($id);
        
        // Check if route has students
        if ($route->students()->count() > 0) {
            return redirect()->route('transportation.routes')->with('error', 'Cannot delete route with assigned students!');
        }
        
        // Delete stops
        $route->stops()->delete();
        
        // Delete route
        $route->delete();
        
        return redirect()->route('transportation.routes')->with('success', 'Route deleted successfully!');
    }

    /**
     * Show the form for managing route stops.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function routeStops($id)
    {
        $route = TransportRoute::with('vehicle', 'stops')->findOrFail($id);
        
        // Sort stops by order
        $stops = $route->stops->sortBy('stop_order');
        
        return view('backend.transportation.routes.stops', compact('route', 'stops'));
    }

    /**
     * Store a newly created stop in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeStop(Request $request, $id)
    {
        $route = TransportRoute::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'stop_time' => 'required|date_format:H:i',
            'stop_order' => 'required|integer|min:1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string'
        ]);

        // Check if stop time is valid
        $stopTime = Carbon::createFromFormat('H:i', $request->stop_time);
        $startTime = Carbon::createFromFormat('H:i', $route->start_time);
        $endTime = Carbon::createFromFormat('H:i', $route->end_time);
        
        if ($stopTime < $startTime || $stopTime > $endTime) {
            return redirect()->back()->with('error', 'Stop time must be between route start and end times!')->withInput();
        }

        // Check if stop order already exists
        $existingStop = TransportStop::where('route_id', $route->id)
            ->where('stop_order', $request->stop_order)
            ->first();
            
        if ($existingStop) {
            return redirect()->back()->with('error', 'A stop with this order already exists!')->withInput();
        }

        // Create stop
        $stop = TransportStop::create([
            'route_id' => $route->id,
            'name' => $request->name,
            'stop_time' => $request->stop_time,
            'stop_order' => $request->stop_order,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'description' => $request->description
        ]);

        return redirect()->route('transportation.routes.stops', $route->id)->with('success', 'Stop added successfully!');
    }

    /**
     * Show the form for editing the specified stop.
     *
     * @param  int  $id
     * @param  int  $stopId
     * @return \Illuminate\Http\Response
     */
    public function editStop($id, $stopId)
    {
        $route = TransportRoute::findOrFail($id);
        $stop = TransportStop::findOrFail($stopId);
        
        return view('backend.transportation.routes.edit_stop', compact('route', 'stop'));
    }

    /**
     * Update the specified stop in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $stopId
     * @return \Illuminate\Http\Response
     */
    public function updateStop(Request $request, $id, $stopId)
    {
        $route = TransportRoute::findOrFail($id);
        $stop = TransportStop::findOrFail($stopId);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'stop_time' => 'required|date_format:H:i',
            'stop_order' => 'required|integer|min:1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string'
        ]);

        // Check if stop time is valid
        $stopTime = Carbon::createFromFormat('H:i', $request->stop_time);
        $startTime = Carbon::createFromFormat('H:i', $route->start_time);
        $endTime = Carbon::createFromFormat('H:i', $route->end_time);
        
        if ($stopTime < $startTime || $stopTime > $endTime) {
            return redirect()->back()->with('error', 'Stop time must be between route start and end times!')->withInput();
        }

        // Check if stop order already exists
        $existingStop = TransportStop::where('route_id', $route->id)
            ->where('stop_order', $request->stop_order)
            ->where('id', '!=', $stop->id)
            ->first();
            
        if ($existingStop) {
            return redirect()->back()->with('error', 'A stop with this order already exists!')->withInput();
        }

        // Update stop
        $stop->name = $request->name;
        $stop->stop_time = $request->stop_time;
        $stop->stop_order = $request->stop_order;
        $stop->latitude = $request->latitude;
        $stop->longitude = $request->longitude;
        $stop->description = $request->description;
        $stop->save();

        return redirect()->route('transportation.routes.stops', $route->id)->with('success', 'Stop updated successfully!');
    }

    /**
     * Remove the specified stop from storage.
     *
     * @param  int  $id
     * @param  int  $stopId
     * @return \Illuminate\Http\Response
     */
    public function destroyStop($id, $stopId)
    {
        $route = TransportRoute::findOrFail($id);
        $stop = TransportStop::findOrFail($stopId);
        
        // Check if stop has students
        $hasStudents = TransportStudent::where('stop_id', $stop->id)->exists();
        
        if ($hasStudents) {
            return redirect()->route('transportation.routes.stops', $route->id)->with('error', 'Cannot delete stop with assigned students!');
        }
        
        $stop->delete();
        
        return redirect()->route('transportation.routes.stops', $route->id)->with('success', 'Stop deleted successfully!');
    }

    /**
     * Display a listing of students using transportation.
     *
     * @return \Illuminate\Http\Response
     */
    public function students()
    {
        $students = TransportStudent::with('student.student', 'route', 'stop')
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return view('backend.transportation.students.index', compact('students'));
    }

    /**
     * Show the form for assigning a student to transportation.
     *
     * @return \Illuminate\Http\Response
     */
    public function assignStudent()
    {
        $routes = TransportRoute::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.transportation.students.assign', compact('routes'));
    }

    /**
     * Get stops for a route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getRouteStops(Request $request)
    {
        $routeId = $request->route_id;
        
        $stops = TransportStop::where('route_id', $routeId)
            ->orderBy('stop_order', 'asc')
            ->get()
            ->map(function ($stop) {
                return [
                    'id' => $stop->id,
                    'name' => $stop->name . ' (' . $stop->formatted_time . ')'
                ];
            });
            
        return response()->json([
            'stops' => $stops
        ]);
    }

    /**
     * Store a newly created student assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeStudentAssignment(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|integer|exists:registrations,id',
            'route_id' => 'required|integer|exists:transport_routes,id',
            'stop_id' => 'required|integer|exists:transport_stops,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string'
        ]);

        // Check if student is already assigned to a route
        $existingAssignment = TransportStudent::where('student_id', $request->student_id)
            ->where('status', '!=', TransportStudent::STATUS_EXPIRED)
            ->first();
            
        if ($existingAssignment) {
            return redirect()->back()->with('error', 'Student is already assigned to a route!')->withInput();
        }

        // Create assignment
        $assignment = TransportStudent::create([
            'student_id' => $request->student_id,
            'route_id' => $request->route_id,
            'stop_id' => $request->stop_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->route('transportation.students')->with('success', 'Student assigned to transportation successfully!');
    }

    /**
     * Show the form for editing the specified student assignment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editStudentAssignment($id)
    {
        $assignment = TransportStudent::with('student.student', 'route', 'stop')->findOrFail($id);
        $routes = TransportRoute::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $stops = TransportStop::where('route_id', $assignment->route_id)
            ->orderBy('stop_order', 'asc')
            ->get()
            ->pluck('name', 'id');
        
        return view('backend.transportation.students.edit', compact('assignment', 'routes', 'stops'));
    }

    /**
     * Update the specified student assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStudentAssignment(Request $request, $id)
    {
        $assignment = TransportStudent::findOrFail($id);
        
        $this->validate($request, [
            'route_id' => 'required|integer|exists:transport_routes,id',
            'stop_id' => 'required|integer|exists:transport_stops,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,inactive,expired',
            'notes' => 'nullable|string'
        ]);

        // Update assignment
        $assignment->route_id = $request->route_id;
        $assignment->stop_id = $request->stop_id;
        $assignment->start_date = $request->start_date;
        $assignment->end_date = $request->end_date;
        $assignment->status = $request->status;
        $assignment->notes = $request->notes;
        $assignment->save();

        return redirect()->route('transportation.students')->with('success', 'Student transportation assignment updated successfully!');
    }

    /**
     * Remove the specified student assignment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyStudentAssignment($id)
    {
        $assignment = TransportStudent::findOrFail($id);
        $assignment->delete();
        
        return redirect()->route('transportation.students')->with('success', 'Student transportation assignment deleted successfully!');
    }

    /**
     * Display the transportation reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function reports()
    {
        $routes = TransportRoute::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.transportation.reports', compact('routes'));
    }

    /**
     * Generate transportation report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        $this->validate($request, [
            'report_type' => 'required|string|in:route_students,vehicle_usage',
            'route_id' => 'nullable|integer|exists:transport_routes,id',
            'output_format' => 'required|string|in:html,pdf,excel'
        ]);
        
        if ($request->report_type == 'route_students') {
            // Get route with students
            $route = TransportRoute::with(['vehicle', 'stops' => function ($query) {
                    $query->orderBy('stop_order', 'asc');
                }, 'students.student.student', 'students.stop'])
                ->findOrFail($request->route_id);
                
            // Group students by stop
            $studentsByStop = [];
            
            foreach ($route->stops as $stop) {
                $studentsByStop[$stop->id] = $route->students->filter(function ($student) use ($stop) {
                    return $student->stop_id == $stop->id && $student->status == TransportStudent::STATUS_ACTIVE;
                });
            }
            
            if ($request->output_format == 'pdf') {
                $pdf = \PDF::loadView('backend.transportation.reports.route_students_pdf', compact('route', 'studentsByStop'));
                return $pdf->download('route_students_' . $route->name . '.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.transportation.reports.route_students', compact('route', 'studentsByStop'));
            }
        } elseif ($request->report_type == 'vehicle_usage') {
            // Get all vehicles with routes
            $vehicles = Vehicle::with('routes')->where('status', AppHelper::ACTIVE)->get();
            
            if ($request->output_format == 'pdf') {
                $pdf = \PDF::loadView('backend.transportation.reports.vehicle_usage_pdf', compact('vehicles'));
                return $pdf->download('vehicle_usage_report.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.transportation.reports.vehicle_usage', compact('vehicles'));
            }
        }
        
        return redirect()->back()->with('error', 'Invalid report type!');
    }
}
