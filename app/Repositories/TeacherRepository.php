<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Contracts\TeacherRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TeacherRepository extends BaseRepository implements TeacherRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Employee::class;
    }
    
    /**
     * Get teachers by subject
     *
     * @param int $subjectId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersBySubject($subjectId, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($subjectId, $columns) {
                return $this->model->whereHas('subjects', function ($query) use ($subjectId) {
                    $query->where('subject_id', $subjectId);
                })->get($columns);
            });
        }
        
        return $this->model->whereHas('subjects', function ($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })->get($columns);
    }
    
    /**
     * Get active teachers
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveTeachers($columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($columns) {
                return $this->model->where('status', 1)
                    ->where('role_id', 2) // Assuming 2 is the role_id for teachers
                    ->get($columns);
            });
        }
        
        return $this->model->where('status', 1)
            ->where('role_id', 2) // Assuming 2 is the role_id for teachers
            ->get($columns);
    }
    
    /**
     * Get teachers with attendance
     *
     * @param string $date
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersWithAttendance($date, $columns = ['*'])
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        
        return $this->model->with(['attendance' => function ($query) use ($date) {
            $query->where('attendance_date', $date);
        }])
        ->where('role_id', 2) // Assuming 2 is the role_id for teachers
        ->get($columns);
    }
    
    /**
     * Get teachers with subjects
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersWithSubjects($columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($columns) {
                return $this->model->with('subjects')
                    ->where('role_id', 2) // Assuming 2 is the role_id for teachers
                    ->get($columns);
            });
        }
        
        return $this->model->with('subjects')
            ->where('role_id', 2) // Assuming 2 is the role_id for teachers
            ->get($columns);
    }
    
    /**
     * Get teachers by class
     *
     * @param int $classId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersByClass($classId, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($classId, $columns) {
                return $this->model->whereHas('sections', function ($query) use ($classId) {
                    $query->where('class_id', $classId);
                })
                ->where('role_id', 2) // Assuming 2 is the role_id for teachers
                ->get($columns);
            });
        }
        
        return $this->model->whereHas('sections', function ($query) use ($classId) {
            $query->where('class_id', $classId);
        })
        ->where('role_id', 2) // Assuming 2 is the role_id for teachers
        ->get($columns);
    }
}
