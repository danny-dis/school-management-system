<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\StudentTransport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TransportationService
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * TransportationService constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a vehicle
     *
     * @param array $data
     * @param int $createdBy
     * @return Vehicle|null
     */
    public function createVehicle(array $data, $createdBy)
    {
        try {
            return Vehicle::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'registration_number' => $data['registration_number'],
                'capacity' => $data['capacity'],
                'driver_name' => $data['driver_name'] ?? null,
                'driver_license' => $data['driver_license'] ?? null,
                'driver_contact' => $data['driver_contact'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? Vehicle::STATUS_ACTIVE,
                'created_by' => $createdBy
            ]);
        } catch (Exception $e) {
            Log::error('Error creating vehicle: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a vehicle
     *
     * @param int $id
     * @param array $data
     * @return Vehicle|null
     */
    public function updateVehicle($id, array $data)
    {
        try {
            $vehicle = Vehicle::find($id);
            
            if (!$vehicle) {
                return null;
            }
            
            $vehicle->update($data);
            return $vehicle;
        } catch (Exception $e) {
            Log::error('Error updating vehicle: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a vehicle
     *
     * @param int $id
     * @return bool
     */
    public function deleteVehicle($id)
    {
        try {
            $vehicle = Vehicle::find($id);
            
            if (!$vehicle) {
                return false;
            }
            
            // Check if vehicle is assigned to any route
            $routeCount = Route::where('vehicle_id', $id)->count();
            
            if ($routeCount > 0) {
                // Don't delete, just mark as inactive
                $vehicle->status = Vehicle::STATUS_INACTIVE;
                $vehicle->save();
            } else {
                $vehicle->delete();
            }
            
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting vehicle: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a route
     *
     * @param array $data
     * @param int $createdBy
     * @return Route|null
     */
    public function createRoute(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $route = Route::create([
                'name' => $data['name'],
                'vehicle_id' => $data['vehicle_id'],
                'description' => $data['description'] ?? null,
                'fare' => $data['fare'],
                'status' => $data['status'] ?? Route::STATUS_ACTIVE,
                'created_by' => $createdBy
            ]);
            
            // Create stops if provided
            if (isset($data['stops']) && is_array($data['stops'])) {
                foreach ($data['stops'] as $index => $stop) {
                    RouteStop::create([
                        'route_id' => $route->id,
                        'name' => $stop['name'],
                        'time' => $stop['time'],
                        'fare' => $stop['fare'] ?? $data['fare'],
                        'order' => $index + 1
                    ]);
                }
            }
            
            DB::commit();
            return $route;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating route: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a route
     *
     * @param int $id
     * @param array $data
     * @return Route|null
     */
    public function updateRoute($id, array $data)
    {
        try {
            $route = Route::find($id);
            
            if (!$route) {
                return null;
            }
            
            $route->update($data);
            return $route;
        } catch (Exception $e) {
            Log::error('Error updating route: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a route
     *
     * @param int $id
     * @return bool
     */
    public function deleteRoute($id)
    {
        try {
            DB::beginTransaction();
            
            $route = Route::find($id);
            
            if (!$route) {
                return false;
            }
            
            // Check if route has students assigned
            $studentCount = StudentTransport::where('route_id', $id)->count();
            
            if ($studentCount > 0) {
                // Don't delete, just mark as inactive
                $route->status = Route::STATUS_INACTIVE;
                $route->save();
            } else {
                // Delete stops
                $route->stops()->delete();
                
                // Delete route
                $route->delete();
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting route: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a route stop
     *
     * @param array $data
     * @return RouteStop|null
     */
    public function createRouteStop(array $data)
    {
        try {
            return RouteStop::create([
                'route_id' => $data['route_id'],
                'name' => $data['name'],
                'time' => $data['time'],
                'fare' => $data['fare'],
                'order' => $data['order'] ?? 0
            ]);
        } catch (Exception $e) {
            Log::error('Error creating route stop: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a route stop
     *
     * @param int $id
     * @param array $data
     * @return RouteStop|null
     */
    public function updateRouteStop($id, array $data)
    {
        try {
            $stop = RouteStop::find($id);
            
            if (!$stop) {
                return null;
            }
            
            $stop->update($data);
            return $stop;
        } catch (Exception $e) {
            Log::error('Error updating route stop: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a route stop
     *
     * @param int $id
     * @return bool
     */
    public function deleteRouteStop($id)
    {
        try {
            $stop = RouteStop::find($id);
            
            if (!$stop) {
                return false;
            }
            
            // Check if stop has students assigned
            $studentCount = StudentTransport::where('stop_id', $id)->count();
            
            if ($studentCount > 0) {
                return false;
            }
            
            $stop->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting route stop: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Assign transport to a student
     *
     * @param array $data
     * @param int $createdBy
     * @return StudentTransport|null
     */
    public function assignTransport(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            // Check if student already has transport assigned
            $existingTransport = StudentTransport::where('student_id', $data['student_id'])->first();
            
            if ($existingTransport) {
                // Update existing assignment
                $existingTransport->update([
                    'route_id' => $data['route_id'],
                    'stop_id' => $data['stop_id'],
                    'effective_date' => $data['effective_date'] ?? now(),
                    'status' => $data['status'] ?? StudentTransport::STATUS_ACTIVE,
                    'updated_by' => $createdBy
                ]);
                
                $transport = $existingTransport;
            } else {
                // Create new assignment
                $transport = StudentTransport::create([
                    'student_id' => $data['student_id'],
                    'route_id' => $data['route_id'],
                    'stop_id' => $data['stop_id'],
                    'effective_date' => $data['effective_date'] ?? now(),
                    'status' => $data['status'] ?? StudentTransport::STATUS_ACTIVE,
                    'created_by' => $createdBy
                ]);
            }
            
            // Notify student about transport assignment
            $student = \App\Models\Student::find($data['student_id']);
            $route = Route::find($data['route_id']);
            $stop = RouteStop::find($data['stop_id']);
            
            if ($student && $student->user_id && $route && $stop) {
                $this->notificationService->createNotification(
                    $student->user_id,
                    'Transport Assigned',
                    'You have been assigned to route: ' . $route->name . ', stop: ' . $stop->name,
                    route('transportation.student.view'),
                    'info'
                );
            }
            
            DB::commit();
            return $transport;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error assigning transport: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Remove transport assignment from a student
     *
     * @param int $studentId
     * @return bool
     */
    public function removeTransport($studentId)
    {
        try {
            $transport = StudentTransport::where('student_id', $studentId)->first();
            
            if (!$transport) {
                return false;
            }
            
            $transport->delete();
            
            // Notify student about transport removal
            $student = \App\Models\Student::find($studentId);
            
            if ($student && $student->user_id) {
                $this->notificationService->createNotification(
                    $student->user_id,
                    'Transport Removed',
                    'Your transport assignment has been removed',
                    route('transportation.student.view'),
                    'info'
                );
            }
            
            return true;
        } catch (Exception $e) {
            Log::error('Error removing transport: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get vehicles
     *
     * @param bool $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehicles($activeOnly = false)
    {
        $query = Vehicle::query();
        
        if ($activeOnly) {
            $query->where('status', Vehicle::STATUS_ACTIVE);
        }
        
        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Get routes
     *
     * @param bool $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoutes($activeOnly = false)
    {
        $query = Route::with('vehicle');
        
        if ($activeOnly) {
            $query->where('status', Route::STATUS_ACTIVE);
        }
        
        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Get route details
     *
     * @param int $id
     * @return Route|null
     */
    public function getRouteDetails($id)
    {
        return Route::with([
            'vehicle',
            'stops' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'students.student'
        ])->find($id);
    }

    /**
     * Get stops for a route
     *
     * @param int $routeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStopsForRoute($routeId)
    {
        return RouteStop::where('route_id', $routeId)
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * Get students for a route
     *
     * @param int $routeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsForRoute($routeId)
    {
        return StudentTransport::with(['student', 'stop'])
            ->where('route_id', $routeId)
            ->where('status', StudentTransport::STATUS_ACTIVE)
            ->get();
    }

    /**
     * Get students for a stop
     *
     * @param int $stopId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsForStop($stopId)
    {
        return StudentTransport::with('student')
            ->where('stop_id', $stopId)
            ->where('status', StudentTransport::STATUS_ACTIVE)
            ->get();
    }

    /**
     * Get transport details for a student
     *
     * @param int $studentId
     * @return StudentTransport|null
     */
    public function getTransportForStudent($studentId)
    {
        return StudentTransport::with(['route.vehicle', 'stop'])
            ->where('student_id', $studentId)
            ->where('status', StudentTransport::STATUS_ACTIVE)
            ->first();
    }

    /**
     * Get vehicle utilization
     *
     * @return array
     */
    public function getVehicleUtilization()
    {
        $vehicles = Vehicle::where('status', Vehicle::STATUS_ACTIVE)->get();
        $result = [];
        
        foreach ($vehicles as $vehicle) {
            $routes = Route::where('vehicle_id', $vehicle->id)
                ->where('status', Route::STATUS_ACTIVE)
                ->get();
            
            $studentCount = 0;
            
            foreach ($routes as $route) {
                $routeStudentCount = StudentTransport::where('route_id', $route->id)
                    ->where('status', StudentTransport::STATUS_ACTIVE)
                    ->count();
                
                $studentCount += $routeStudentCount;
            }
            
            $result[] = [
                'vehicle' => $vehicle,
                'capacity' => $vehicle->capacity,
                'utilized' => $studentCount,
                'utilization_percentage' => $vehicle->capacity > 0 ? round(($studentCount / $vehicle->capacity) * 100, 2) : 0
            ];
        }
        
        return $result;
    }
}
