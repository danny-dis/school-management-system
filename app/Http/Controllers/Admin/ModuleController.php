<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\ModuleManager;
use App\Services\LicensingService;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;
    
    /**
     * @var LicensingService
     */
    protected $licensingService;

    /**
     * Create a new controller instance.
     *
     * @param ModuleManager $moduleManager
     * @param LicensingService $licensingService
     * @return void
     */
    public function __construct(ModuleManager $moduleManager, LicensingService $licensingService)
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
        $this->moduleManager = $moduleManager;
        $this->licensingService = $licensingService;
    }

    /**
     * Display a listing of the modules.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $modules = Module::orderBy('order', 'asc')->get();
        $licensedModules = $this->licensingService->getLicensedModules();
        
        return view('admin.modules.index', compact('modules', 'licensedModules'));
    }

    /**
     * Enable a module.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enable(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        // Check if module is licensed
        if (!$this->licensingService->isModuleLicensed($module->module_key)) {
            return redirect()->route('admin.modules.index')
                ->with('error', 'This module is not licensed. Please purchase a license to enable it.');
        }
        
        // Check if dependencies are met
        if (!$this->moduleManager->checkDependencies($module->module_key)) {
            $dependencies = $this->moduleManager->getDependencies($module->module_key);
            $dependencyNames = Module::whereIn('module_key', $dependencies)->pluck('name')->toArray();
            
            return redirect()->route('admin.modules.index')
                ->with('error', 'This module depends on the following modules: ' . implode(', ', $dependencyNames) . '. Please enable them first.');
        }
        
        // Enable module
        if ($this->moduleManager->enableModule($module->module_key)) {
            return redirect()->route('admin.modules.index')
                ->with('success', 'Module enabled successfully.');
        }
        
        return redirect()->route('admin.modules.index')
            ->with('error', 'Failed to enable module.');
    }

    /**
     * Disable a module.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disable(Request $request, $id)
    {
        $module = Module::findOrFail($id);
        
        // Check if other modules depend on this module
        if (!$this->moduleManager->canDisable($module->module_key)) {
            $dependents = $this->moduleManager->getDependents($module->module_key);
            $dependentNames = Module::whereIn('module_key', $dependents)->pluck('name')->toArray();
            
            return redirect()->route('admin.modules.index')
                ->with('error', 'The following modules depend on this module: ' . implode(', ', $dependentNames) . '. Please disable them first.');
        }
        
        // Disable module
        if ($this->moduleManager->disableModule($module->module_key)) {
            return redirect()->route('admin.modules.index')
                ->with('success', 'Module disabled successfully.');
        }
        
        return redirect()->route('admin.modules.index')
            ->with('error', 'Failed to disable module.');
    }

    /**
     * Refresh module cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshCache()
    {
        $this->moduleManager->refreshCache();
        
        return redirect()->route('admin.modules.index')
            ->with('success', 'Module cache refreshed successfully.');
    }
}
