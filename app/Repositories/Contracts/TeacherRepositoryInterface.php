<?php

namespace App\Repositories\Contracts;

interface TeacherRepositoryInterface extends RepositoryInterface
{
    /**
     * Get teachers by subject
     *
     * @param int $subjectId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersBySubject($subjectId, $columns = ['*']);
    
    /**
     * Get active teachers
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveTeachers($columns = ['*']);
    
    /**
     * Get teachers with attendance
     *
     * @param string $date
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersWithAttendance($date, $columns = ['*']);
    
    /**
     * Get teachers with subjects
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersWithSubjects($columns = ['*']);
    
    /**
     * Get teachers by class
     *
     * @param int $classId
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersByClass($classId, $columns = ['*']);
}
