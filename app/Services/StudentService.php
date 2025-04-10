<?php

namespace App\Services;

use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Exception;

class StudentService extends BaseService
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * StudentService constructor.
     *
     * @param StudentRepositoryInterface $repository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        StudentRepositoryInterface $repository,
        UserRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new student with user account
     *
     * @param array $data
     * @return \App\Models\Student|null
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            // Create user account
            $userData = [
                'name' => $data['name'],
                'username' => $data['username'] ?? $this->generateUsername($data['name']),
                'email' => $data['email'] ?? null,
                'phone_no' => $data['phone_no'] ?? null,
                'password' => Hash::make($data['password'] ?? 'student123'),
                'status' => $data['status'] ?? 1,
            ];

            $user = $this->userRepository->create($userData);
            $user->assignRole('Student');

            // Create student profile
            $studentData = array_merge($data, ['user_id' => $user->id]);
            $student = $this->repository->create($studentData);

            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating student: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a student
     *
     * @param array $data
     * @param int $id
     * @return \App\Models\Student|null
     */
    public function update(array $data, $id)
    {
        try {
            DB::beginTransaction();

            $student = $this->repository->find($id);

            // Update user account if needed
            if (isset($data['name']) || isset($data['email']) || isset($data['phone_no']) || isset($data['status'])) {
                $userData = [];

                if (isset($data['name'])) {
                    $userData['name'] = $data['name'];
                }

                if (isset($data['email'])) {
                    $userData['email'] = $data['email'];
                }

                if (isset($data['phone_no'])) {
                    $userData['phone_no'] = $data['phone_no'];
                }

                if (isset($data['status'])) {
                    $userData['status'] = $data['status'];
                }

                if (isset($data['password']) && !empty($data['password'])) {
                    $userData['password'] = Hash::make($data['password']);
                }

                $this->userRepository->update($userData, $student->user_id);
            }

            // Update student profile
            $student = $this->repository->update($data, $id);

            DB::commit();
            return $student;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating student: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a student
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $student = $this->repository->find($id);

            // Delete student profile
            $this->repository->delete($id);

            // Delete user account
            $this->userRepository->delete($student->user_id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting student: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
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
        return $this->repository->getStudentsByClass($classId, $columns);
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
        return $this->repository->getStudentsBySection($sectionId, $columns);
    }

    /**
     * Get active students
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveStudents($columns = ['*'])
    {
        return $this->repository->getActiveStudents($columns);
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
        return $this->repository->getStudentsWithAttendance($date, $classId, $sectionId, $columns);
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
        return $this->repository->getStudentsWithResults($examId, $classId, $sectionId, $columns);
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
        return $this->repository->getStudentsWithFilters($filters, $perPage);
    }

    /**
     * Generate a unique username
     *
     * @param string $name
     * @return string
     */
    protected function generateUsername($name)
    {
        $baseName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $username = $baseName;
        $counter = 1;

        while ($this->userRepository->findByUsername($username)) {
            $username = $baseName . $counter;
            $counter++;
        }

        return $username;
    }
}
