<?php

namespace App\Services;

use App\Repositories\Contracts\TeacherRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Exception;

class TeacherService extends BaseService
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * TeacherService constructor.
     *
     * @param TeacherRepositoryInterface $repository
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        TeacherRepositoryInterface $repository,
        UserRepositoryInterface $userRepository
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    /**
     * Create a new teacher with user account
     *
     * @param array $data
     * @return \App\Models\Employee|null
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
                'password' => Hash::make($data['password'] ?? 'teacher123'),
                'status' => $data['status'] ?? 1,
            ];
            
            $user = $this->userRepository->create($userData);
            $user->assignRole('Teacher');
            
            // Create teacher profile
            $teacherData = array_merge($data, [
                'user_id' => $user->id,
                'role_id' => 2, // Assuming 2 is the role_id for teachers
            ]);
            
            $teacher = $this->repository->create($teacherData);
            
            // Assign subjects if provided
            if (isset($data['subjects']) && is_array($data['subjects'])) {
                $teacher->subjects()->sync($data['subjects']);
            }
            
            DB::commit();
            return $teacher;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating teacher: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a teacher
     *
     * @param array $data
     * @param int $id
     * @return \App\Models\Employee|null
     */
    public function update(array $data, $id)
    {
        try {
            DB::beginTransaction();
            
            $teacher = $this->repository->find($id);
            
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
                
                $this->userRepository->update($userData, $teacher->user_id);
            }
            
            // Update teacher profile
            $teacher = $this->repository->update($data, $id);
            
            // Update subjects if provided
            if (isset($data['subjects']) && is_array($data['subjects'])) {
                $teacher->subjects()->sync($data['subjects']);
            }
            
            DB::commit();
            return $teacher;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating teacher: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a teacher
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            
            $teacher = $this->repository->find($id);
            
            // Delete teacher profile
            $this->repository->delete($id);
            
            // Delete user account
            $this->userRepository->delete($teacher->user_id);
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting teacher: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
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
        return $this->repository->getTeachersBySubject($subjectId, $columns);
    }

    /**
     * Get active teachers
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveTeachers($columns = ['*'])
    {
        return $this->repository->getActiveTeachers($columns);
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
        return $this->repository->getTeachersWithAttendance($date, $columns);
    }

    /**
     * Get teachers with subjects
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersWithSubjects($columns = ['*'])
    {
        return $this->repository->getTeachersWithSubjects($columns);
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
