<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService extends BaseService
{
    /**
     * UserService constructor.
     *
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return \App\Models\User|null
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            $user = $this->repository->create($data);
            
            // Assign role if provided
            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }
            
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a user
     *
     * @param array $data
     * @param int $id
     * @return \App\Models\User|null
     */
    public function update(array $data, $id)
    {
        try {
            DB::beginTransaction();
            
            // Hash password if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            $user = $this->repository->update($data, $id);
            
            // Update role if provided
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }
            
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Get users by role
     *
     * @param string $role
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole($role, $columns = ['*'])
    {
        return $this->repository->getUsersByRole($role, $columns);
    }

    /**
     * Get active users
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveUsers($columns = ['*'])
    {
        return $this->repository->getActiveUsers($columns);
    }

    /**
     * Find user by username
     *
     * @param string $username
     * @param array $columns
     * @return \App\Models\User
     */
    public function findByUsername($username, $columns = ['*'])
    {
        return $this->repository->findByUsername($username, $columns);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @param array $columns
     * @return \App\Models\User
     */
    public function findByEmail($email, $columns = ['*'])
    {
        return $this->repository->findByEmail($email, $columns);
    }

    /**
     * Change user password
     *
     * @param int $userId
     * @param string $password
     * @return \App\Models\User|null
     */
    public function changePassword($userId, $password)
    {
        try {
            DB::beginTransaction();
            
            $user = $this->repository->find($userId);
            $user->password = Hash::make($password);
            $user->save();
            
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error changing password: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }
}
