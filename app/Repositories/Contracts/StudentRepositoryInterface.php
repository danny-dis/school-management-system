<?php

namespace App\Repositories\Contracts;

interface StudentRepositoryInterface extends RepositoryInterface
{
    /**
     * Get students by class
     *
     * @param int $classId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsByClass($classId, $columns = ['*']);

    /**
     * Get students by section
     *
     * @param int $sectionId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsBySection($sectionId, $columns = ['*']);

    /**
     * Get active students
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveStudents($columns = ['*']);

    /**
     * Get students with attendance
     *
     * @param string $date
     * @param int $classId
     * @param int $sectionId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsWithAttendance($date, $classId, $sectionId, $columns = ['*']);

    /**
     * Get students with results
     *
     * @param int $examId
     * @param int $classId
     * @param int $sectionId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsWithResults($examId, $classId, $sectionId, $columns = ['*']);

    /**
     * Get students with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getStudentsWithFilters(array $filters = [], $perPage = 15);
}
