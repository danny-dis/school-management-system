<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var bool
     */
    protected $useCache = true;

    /**
     * @var int
     */
    protected $cacheTtl = 60; // minutes

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();

    /**
     * Make Model instance
     *
     * @return Model
     * @throws \Exception
     */
    public function makeModel()
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Get all resources
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($columns) {
                return $this->model->all($columns);
            });
        }

        return $this->model->all($columns);
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
        return $this->model->paginate($perPage, $columns);
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
        $query = $this->model->query();

        // Apply filters
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                if (isset($value['operator']) && isset($value['value'])) {
                    $query->where($field, $value['operator'], $value['value']);
                } elseif (isset($value['in'])) {
                    $query->whereIn($field, $value['in']);
                } elseif (isset($value['not_in'])) {
                    $query->whereNotIn($field, $value['not_in']);
                } elseif (isset($value['between'])) {
                    $query->whereBetween($field, $value['between']);
                } elseif (isset($value['like'])) {
                    $query->where($field, 'like', '%' . $value['like'] . '%');
                }
            } elseif ($value !== null && $value !== '') {
                $query->where($field, '=', $value);
            }
        }

        return $query->paginate($perPage, $columns);
    }

    /**
     * Create a resource
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        $model = $this->model->create($data);

        if ($this->useCache) {
            $this->clearCache();
        }

        return $model;
    }

    /**
     * Update a resource
     *
     * @param array $data
     * @param int $id
     * @return Model
     */
    public function update(array $data, $id)
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);

        if ($this->useCache) {
            $this->clearCache();
        }

        return $model;
    }

    /**
     * Delete a resource
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $result = $this->model->findOrFail($id)->delete();

        if ($this->useCache) {
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Find a resource by id
     *
     * @param int $id
     * @param array $columns
     * @return Model
     */
    public function find($id, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $columns) {
                return $this->model->findOrFail($id, $columns);
            });
        }

        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Find a resource by field
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return Model|null
     */
    public function findByField($field, $value, $columns = ['*'])
    {
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($field, $value, $columns) {
                return $this->model->where($field, $value)->first($columns);
            });
        }

        return $this->model->where($field, $value)->first($columns);
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
        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($field, $value, $columns) {
                return $this->model->where($field, $value)->get($columns);
            });
        }

        return $this->model->where($field, $value)->get($columns);
    }

    /**
     * Find a resource by multiple fields
     *
     * @param array $where
     * @param array $columns
     * @return Model|null
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        $query = $this->model->query();

        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $query->where($field, $condition, $val);
            } else {
                $query->where($field, '=', $value);
            }
        }

        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $columns) {
                return $query->first($columns);
            });
        }

        return $query->first($columns);
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
        $query = $this->model->query();

        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $query->where($field, $condition, $val);
            } else {
                $query->where($field, '=', $value);
            }
        }

        if ($this->useCache) {
            $cacheKey = $this->getCacheKey(__FUNCTION__, func_get_args());
            $this->addCacheKey($cacheKey);

            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $columns) {
                return $query->get($columns);
            });
        }

        return $query->get($columns);
    }

    /**
     * Generate cache key
     *
     * @param string $method
     * @param array $args
     * @return string
     */
    protected function getCacheKey($method, array $args = [])
    {
        $className = get_class($this->model);
        $className = str_replace('\\', '_', $className);

        $argsKey = '';
        if (count($args)) {
            $argsKey = '_' . md5(serialize($args));
        }

        return "repository_{$className}_{$method}{$argsKey}";
    }

    /**
     * Clear cache
     *
     * @return void
     */
    protected function clearCache()
    {
        $className = get_class($this->model);
        $className = str_replace('\\', '_', $className);

        // Get all cache keys for this repository
        $keys = Cache::get("repository_{$className}_keys", []);

        // Clear each key
        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Clear the keys list itself
        Cache::forget("repository_{$className}_keys");

        // Also clear the 'all' cache specifically
        Cache::forget("repository_{$className}_all");
    }

    /**
     * Add a key to the cache keys list
     *
     * @param string $key
     * @return void
     */
    protected function addCacheKey($key)
    {
        if (!$this->useCache) {
            return;
        }

        $className = get_class($this->model);
        $className = str_replace('\\', '_', $className);

        $keysName = "repository_{$className}_keys";
        $keys = Cache::get($keysName, []);

        if (!in_array($key, $keys)) {
            $keys[] = $key;
            Cache::forever($keysName, $keys);
        }
    }
}
