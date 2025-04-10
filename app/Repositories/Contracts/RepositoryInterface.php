<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    /**
     * Get all resources
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*']);

    /**
     * Get paginated resources
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*']);

    /**
     * Get filtered paginated resources
     *
     * @param array $filters
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithFilters(array $filters = [], $perPage = 15, $columns = ['*']);

    /**
     * Create a resource
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data);

    /**
     * Update a resource
     *
     * @param array $data
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, $id);

    /**
     * Delete a resource
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Find a resource by id
     *
     * @param int $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id, $columns = ['*']);

    /**
     * Find a resource by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findByField($field, $value, $columns = ['*']);

    /**
     * Find multiple resources by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllByField($field, $value, $columns = ['*']);

    /**
     * Find a resource by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findWhere(array $where, $columns = ['*']);

    /**
     * Find all resources by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAllWhere(array $where, $columns = ['*']);
}
