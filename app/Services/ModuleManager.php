<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Cache;

class ModuleManager
{
    /**
     * Cache key for modules
     *
     * @var string
     */
    protected $cacheKey = 'system_modules';
    
    /**
     * Cache TTL in minutes
     *
     * @var int
     */
    protected $cacheTtl = 1440; // 24 hours
    
    /**
     * Get all modules
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllModules()
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return Module::all();
        });
    }
    
    /**
     * Check if a module is enabled
     *
     * @param string $moduleKey
     * @return bool
     */
    public function isModuleEnabled($moduleKey)
    {
        $modules = $this->getAllModules();
        $module = $modules->where('module_key', $moduleKey)->first();
        
        return $module ? (bool) $module->status : false;
    }
    
    /**
     * Enable a module
     *
     * @param string $moduleKey
     * @return bool
     */
    public function enableModule($moduleKey)
    {
        $module = Module::where('module_key', $moduleKey)->first();
        
        if (!$module) {
            return false;
        }
        
        $module->status = true;
        $result = $module->save();
        
        if ($result) {
            $this->clearCache();
        }
        
        return $result;
    }
    
    /**
     * Disable a module
     *
     * @param string $moduleKey
     * @return bool
     */
    public function disableModule($moduleKey)
    {
        $module = Module::where('module_key', $moduleKey)->first();
        
        if (!$module) {
            return false;
        }
        
        $module->status = false;
        $result = $module->save();
        
        if ($result) {
            $this->clearCache();
        }
        
        return $result;
    }
    
    /**
     * Get enabled modules
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnabledModules()
    {
        $modules = $this->getAllModules();
        return $modules->where('status', true);
    }
    
    /**
     * Clear modules cache
     *
     * @return void
     */
    public function clearCache()
    {
        Cache::forget($this->cacheKey);
    }
}
