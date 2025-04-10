<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
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
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($role, $columns) {
                return $this->model->role($role)->get($columns);
            });
        }
        
        return $this->model->role($role)->get($columns);
    }
    
    /**
     * Get active users
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveUsers($columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($columns) {
                return $this->model->where('status', 1)->get($columns);
            });
        }
        
        return $this->model->where('status', 1)->get($columns);
    }
    
    /**
     * Get users with permissions
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersWithPermissions($columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($columns) {
                return $this->model->with('permissions')->get($columns);
            });
        }
        
        return $this->model->with('permissions')->get($columns);
    }
    
    /**
     * Find user by username
     *
     * @param string $username
     * @param array $columns
     * @return User
     */
    public function findByUsername($username, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($username, $columns) {
                return $this->model->where('username', $username)->first($columns);
            });
        }
        
        return $this->model->where('username', $username)->first($columns);
    }
    
    /**
     * Find user by email
     *
     * @param string $email
     * @param array $columns
     * @return User
     */
    public function findByEmail($email, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($email, $columns) {
                return $this->model->where('email', $email)->first($columns);
            });
        }
        
        return $this->model->where('email', $email)->first($columns);
    }
}
