<?php

namespace App\Repositories;

use App\Models\Student;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Student::class;
    }

    /**
     * Get students by class
     *
     * @param int $classId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsByClass($classId, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($classId, $columns) {
                return $this->model->whereHas('registration', function ($query) use ($classId) {
                    $query->where('class_id', $classId);
                })->get($columns);
            });
        }

        return $this->model->whereHas('registration', function ($query) use ($classId) {
            $query->where('class_id', $classId);
        })->get($columns);
    }

    /**
     * Get students by section
     *
     * @param int $sectionId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsBySection($sectionId, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($sectionId, $columns) {
                return $this->model->whereHas('registration', function ($query) use ($sectionId) {
                    $query->where('section_id', $sectionId);
                })->get($columns);
            });
        }

        return $this->model->whereHas('registration', function ($query) use ($sectionId) {
            $query->where('section_id', $sectionId);
        })->get($columns);
    }

    /**
     * Get active students
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveStudents($columns = ['*'])
    {
        return $this->findAllByField('status', 1, $columns);
    }

    /**
     * Get students with attendance
     *
     * @param string $date
     * @param int $classId
     * @param int $sectionId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsWithAttendance($date, $classId, $sectionId, $columns = ['*'])
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $this->model->with(['registration' => function ($query) use ($classId, $sectionId) {
            $query->where('class_id', $classId)
                ->where('section_id', $sectionId);
        }, 'registration.attendance' => function ($query) use ($date) {
            $query->where('attendance_date', $date);
        }])
        ->whereHas('registration', function ($query) use ($classId, $sectionId) {
            $query->where('class_id', $classId)
                ->where('section_id', $sectionId);
        })
        ->get($columns);
    }

    /**
     * Get students with results
     *
     * @param int $examId
     * @param int $classId
     * @param int $sectionId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsWithResults($examId, $classId, $sectionId, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($examId, $classId, $sectionId, $columns) {
                return $this->model->with(['registration' => function ($query) use ($classId, $sectionId) {
                    $query->where('class_id', $classId)
                        ->where('section_id', $sectionId);
                }, 'registration.result' => function ($query) use ($examId) {
                    $query->where('exam_id', $examId);
                }])
                ->whereHas('registration', function ($query) use ($classId, $sectionId) {
                    $query->where('class_id', $classId)
                        ->where('section_id', $sectionId);
                })
                ->get($columns);
            });
        }

        return $this->model->with(['registration' => function ($query) use ($classId, $sectionId) {
            $query->where('class_id', $classId)
                ->where('section_id', $sectionId);
        }, 'registration.result' => function ($query) use ($examId) {
            $query->where('exam_id', $examId);
        }])
        ->whereHas('registration', function ($query) use ($classId, $sectionId) {
            $query->where('class_id', $classId)
                ->where('section_id', $sectionId);
        })
        ->get($columns);
    }

    /**
     * Get students with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getStudentsWithFilters(array $filters = [], $perPage = 15)
    {
        $query = $this->model->query();

        // Apply student filters
        if (isset($filters['name']) && !empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['gender']) && !empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (isset($filters['religion']) && !empty($filters['religion'])) {
            $query->where('religion', $filters['religion']);
        }

        if (isset($filters['blood_group']) && !empty($filters['blood_group'])) {
            $query->where('blood_group', $filters['blood_group']);
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        // Apply registration filters
        if (isset($filters['class_id']) || isset($filters['section_id']) || isset($filters['academic_year_id'])) {
            $query->whereHas('registration', function ($q) use ($filters) {
                if (isset($filters['class_id']) && !empty($filters['class_id'])) {
                    $q->where('class_id', $filters['class_id']);
                }

                if (isset($filters['section_id']) && !empty($filters['section_id'])) {
                    $q->where('section_id', $filters['section_id']);
                }

                if (isset($filters['academic_year_id']) && !empty($filters['academic_year_id'])) {
                    $q->where('academic_year_id', $filters['academic_year_id']);
                }

                if (isset($filters['registration_status']) && $filters['registration_status'] !== '') {
                    $q->where('status', $filters['registration_status']);
                }
            });
        }

        // Include relationships
        $query->with(['registration' => function ($q) use ($filters) {
            $q->with(['class', 'section', 'academicYear']);

            if (isset($filters['academic_year_id']) && !empty($filters['academic_year_id'])) {
                $q->where('academic_year_id', $filters['academic_year_id']);
            }
        }]);

        // Order by
        $orderBy = $filters['order_by'] ?? 'name';
        $orderDir = $filters['order_dir'] ?? 'asc';
        $query->orderBy($orderBy, $orderDir);

        return $query->paginate($perPage);
    }
}
