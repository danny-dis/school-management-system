<?php

namespace App\Services;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseService
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Get all resources
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * Get paginated resources
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->repository->paginate($perPage, $columns);
    }

    /**
     * Get filtered paginated resources
     *
     * @param array $filters
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithFilters(array $filters = [], $perPage = 15, $columns = ['*'])
    {
        return $this->repository->paginateWithFilters($filters, $perPage, $columns);
    }

    /**
     * Find a resource by id
     *
     * @param int $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, $columns = ['*'])
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * Find a resource by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findByField($field, $value, $columns = ['*'])
    {
        return $this->repository->findByField($field, $value, $columns);
    }

    /**
     * Find multiple resources by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllByField($field, $value, $columns = ['*'])
    {
        return $this->repository->findAllByField($field, $value, $columns);
    }

    /**
     * Find a resource by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        return $this->repository->findWhere($where, $columns);
    }

    /**
     * Find all resources by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllWhere(array $where, $columns = ['*'])
    {
        return $this->repository->findAllWhere($where, $columns);
    }

    /**
     * Create a resource
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function create(array $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->create($data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating resource: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a resource
     *
     * @param array $data
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function update(array $data, $id)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->update($data, $id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating resource: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a resource
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->repository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting resource: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }
}
