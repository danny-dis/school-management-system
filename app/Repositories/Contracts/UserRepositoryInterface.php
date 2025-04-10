<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Get users by role
     *
     * @param string $role
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersByRole($role, $columns = ['*']);
    
    /**
     * Get active users
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveUsers($columns = ['*']);
    
    /**
     * Get users with permissions
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersWithPermissions($columns = ['*']);
    
    /**
     * Find user by username
     *
     * @param string $username
     * @param array $columns
     * @return \App\Models\User
     */
    public function findByUsername($username, $columns = ['*']);
    
    /**
     * Find user by email
     *
     * @param string $email
     * @param array $columns
     * @return \App\Models\User
     */
    public function findByEmail($email, $columns = ['*']);
}
